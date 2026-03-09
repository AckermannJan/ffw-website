<?php
/**
 * Plugin Name: FFW ICS Dienstplan Uploader
 * Description: Upload an ICS calendar file via the WP Admin UI to generate the eAbtRoster.json served to the Vue frontend.
 * Version:     1.0.0
 * Author:      Feuerwehr Traisa
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─── Constants ───────────────────────────────────────────────────────────────

define( 'FFW_ROSTER_DIR',      wp_upload_dir()['basedir'] . '/ffw-roster' );
define( 'FFW_ROSTER_URL',      wp_upload_dir()['baseurl'] . '/ffw-roster' );
define( 'FFW_ROSTER_FILENAME', 'eAbtRoster.json' );
define( 'FFW_ROSTER_FILE',     FFW_ROSTER_DIR . '/' . FFW_ROSTER_FILENAME );
define( 'FFW_ROSTER_META',     FFW_ROSTER_DIR . '/.meta.json' );

// ─── ART_MAP (mirrors icsToJsonConverter.js) ─────────────────────────────────

const FFW_ART_MAP = [
    'Unterricht und Praxis'   => 'U/P',
    'Unterricht'              => 'U',
    'Praxis'                  => 'P',
    'Sonstige Veranstaltungen' => 'S',
    'Katastrophenschutz'      => 'KatS',
];

// ─── ICS Parsing (PHP port of icsToJsonConverter.js) ─────────────────────────

/**
 * Strip TZID param and return [ 'date' => 'YYYYMMDD', 'time' => 'HHMMSS' ].
 * Input examples: "TZID=Europe/Berlin:20260107T190000" or "20260107T190000"
 */
function ffw_parse_date_time( string $value ): array {
    $raw  = str_contains( $value, ':' ) ? substr( $value, strrpos( $value, ':' ) + 1 ) : $value;
    $date = substr( $raw, 0, 8 );
    $time = substr( $raw, 9, 6 );
    return [ 'date' => $date, 'time' => $time ];
}

/** "20260107" → "07.01.2026" */
function ffw_format_date( string $d ): string {
    return substr( $d, 6, 2 ) . '.' . substr( $d, 4, 2 ) . '.' . substr( $d, 0, 4 );
}

/** "190000" → "19:00:00" */
function ffw_format_time_von( string $t ): string {
    return substr( $t, 0, 2 ) . ':' . substr( $t, 2, 2 ) . ':' . substr( $t, 4, 2 );
}

/** "213000" → "21:30" */
function ffw_format_time_bis( string $t ): string {
    return substr( $t, 0, 2 ) . ':' . substr( $t, 2, 2 );
}

/** Both times in "HHMMSS" → duration string "H:MM" */
function ffw_calc_dauer( string $start, string $end ): string {
    $to_min = fn( $t ) => (int) substr( $t, 0, 2 ) * 60 + (int) substr( $t, 2, 2 );
    $diff   = $to_min( $end ) - $to_min( $start );
    if ( $diff <= 0 ) return '';
    $h = intdiv( $diff, 60 );
    $m = $diff % 60;
    return $h . ':' . str_pad( (string) $m, 2, '0', STR_PAD_LEFT );
}

/**
 * Parse "FwDV 1 | Art: Praxis" or "Art: Sonstige Veranstaltungen"
 * Returns [ 'fwdv' => '...', 'art' => '...' ]
 */
function ffw_parse_description( string $desc ): array {
    $fwdv = '';
    $art  = '';

    if ( str_contains( $desc, '|' ) ) {
        [ $left, $right ] = array_map( 'trim', explode( '|', $desc, 2 ) );
        if ( str_starts_with( $left, 'FwDV' ) ) {
            $fwdv = $left;
        }
        if ( preg_match( '/Art:\s*(.+)/u', $right, $m ) ) {
            $art = trim( $m[1] );
        }
    } else {
        if ( preg_match( '/Art:\s*(.+)/u', $desc, $m ) ) {
            $art = trim( $m[1] );
        }
    }

    return [
        'fwdv' => $fwdv,
        'art'  => FFW_ART_MAP[ $art ] ?? $art,
    ];
}

/**
 * Unfold RFC 5545 line continuations, then parse VEVENT blocks.
 * Returns array of associative arrays keyed by ICS property name.
 */
function ffw_parse_ics( string $raw ): array {
    // Unfold: CRLF or LF followed by a space/tab continues the previous line
    $unfolded = preg_replace( '/\r?\n[ \t]/', '', $raw );
    $lines    = preg_split( '/\r?\n/', $unfolded );

    $events  = [];
    $current = null;

    foreach ( $lines as $line ) {
        if ( $line === 'BEGIN:VEVENT' ) {
            $current = [];
            continue;
        }
        if ( $line === 'END:VEVENT' ) {
            if ( $current !== null ) {
                $events[] = $current;
            }
            $current = null;
            continue;
        }
        if ( $current === null ) continue;

        $colon = strpos( $line, ':' );
        if ( $colon === false ) continue;

        $key   = substr( $line, 0, $colon );
        $value = substr( $line, $colon + 1 );

        // Use base name (before ";") as key
        $base_name           = explode( ';', $key )[0];
        $current[ $base_name ] = $value;
    }

    return $events;
}

/**
 * Convert array of VEVENT property maps to roster entries.
 * Returns sorted roster array.
 */
function ffw_convert_ics_to_roster( string $raw ): array {
    $events = ffw_parse_ics( $raw );
    $roster = [];

    foreach ( $events as $ev ) {
        $dtstart = $ev['DTSTART'] ?? '';
        $dtend   = $ev['DTEND']   ?? '';
        if ( ! $dtstart || ! $dtend ) continue;

        $start              = ffw_parse_date_time( $dtstart );
        $end                = ffw_parse_date_time( $dtend );
        [ 'fwdv' => $fwdv, 'art' => $art ] = ffw_parse_description( $ev['DESCRIPTION'] ?? '' );

        $roster[] = [
            'Datum'              => ffw_format_date( $start['date'] ),
            'von'                => ffw_format_time_von( $start['time'] ),
            'bis'                => ffw_format_time_bis( $end['time'] ),
            'Thema'              => $ev['SUMMARY']  ?? '',
            'FwDV'               => $fwdv,
            'Art'                => $art,
            'Dauer'              => ffw_calc_dauer( $start['time'], $end['time'] ),
            'Ortsteil-Feuerwehr' => $ev['LOCATION'] ?? '',
        ];
    }

    // Sort by date ascending (convert DD.MM.YYYY → YYYY-MM-DD for comparison)
    usort( $roster, function ( $a, $b ) {
        $to_iso = fn( $d ) => implode( '-', array_reverse( explode( '.', $d ) ) );
        return strcmp( $to_iso( $a['Datum'] ), $to_iso( $b['Datum'] ) );
    } );

    return $roster;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

/** Stable key for an entry, used to track visibility. */
function ffw_entry_key( array $entry ): string {
    return md5( ( $entry['Datum'] ?? '' ) . '|' . ( $entry['von'] ?? '' ) );
}

/** Load all entries from the JSON file. Returns [] if file missing/invalid. */
function ffw_load_roster(): array {
    if ( ! file_exists( FFW_ROSTER_FILE ) ) return [];
    $roster = json_decode( file_get_contents( FFW_ROSTER_FILE ), true );
    return is_array( $roster ) ? $roster : [];
}

/** Return the set of hidden entry keys. */
function ffw_hidden_keys(): array {
    return (array) get_option( 'ffw_hidden_roster_entries', [] );
}

// ─── Admin Menu ───────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_management_page(
        'ICS Dienstplan Upload',
        'ICS Dienstplan',
        'manage_options',
        'ffw-ics-uploader',
        'ffw_render_admin_page'
    );
} );

// ─── Handle Upload ────────────────────────────────────────────────────────────

function ffw_handle_upload(): array {
    if ( ! current_user_can( 'manage_options' ) ) {
        return [ 'error' => 'Keine Berechtigung.' ];
    }

    check_admin_referer( 'ffw_ics_upload' );

    if ( empty( $_FILES['ics_file'] ) || $_FILES['ics_file']['error'] !== UPLOAD_ERR_OK ) {
        $code = $_FILES['ics_file']['error'] ?? -1;
        return [ 'error' => "Datei-Upload fehlgeschlagen (Code $code)." ];
    }

    $file = $_FILES['ics_file'];

    // Validate extension
    $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    if ( $ext !== 'ics' ) {
        return [ 'error' => 'Nur .ics-Dateien sind erlaubt.' ];
    }

    // Validate MIME (text/calendar or text/plain — clients vary)
    $allowed_mimes = [ 'text/calendar', 'text/plain', 'application/octet-stream' ];
    $mime          = mime_content_type( $file['tmp_name'] );
    if ( ! in_array( $mime, $allowed_mimes, true ) ) {
        return [ 'error' => "Ungültiger Dateityp: $mime" ];
    }

    $raw = file_get_contents( $file['tmp_name'] );
    if ( $raw === false ) {
        return [ 'error' => 'Datei konnte nicht gelesen werden.' ];
    }

    if ( ! str_contains( $raw, 'BEGIN:VCALENDAR' ) ) {
        return [ 'error' => 'Ungültiges ICS-Format (kein VCALENDAR-Block gefunden).' ];
    }

    $roster = ffw_convert_ics_to_roster( $raw );

    if ( empty( $roster ) ) {
        return [ 'error' => 'Keine Termine gefunden. Enthält die Datei VEVENT-Blöcke?' ];
    }

    // Ensure upload directory exists
    if ( ! wp_mkdir_p( FFW_ROSTER_DIR ) ) {
        return [ 'error' => 'Upload-Verzeichnis konnte nicht erstellt werden.' ];
    }

    $json = json_encode( $roster, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    if ( file_put_contents( FFW_ROSTER_FILE, $json ) === false ) {
        return [ 'error' => 'JSON-Datei konnte nicht gespeichert werden.' ];
    }

    // Save metadata
    $meta = [
        'updated' => current_time( 'c' ),
        'count'   => count( $roster ),
        'source'  => sanitize_file_name( $file['name'] ),
    ];
    file_put_contents( FFW_ROSTER_META, json_encode( $meta, JSON_PRETTY_PRINT ) );

    return [
        'success' => true,
        'count'   => count( $roster ),
        'url'     => FFW_ROSTER_URL . '/' . FFW_ROSTER_FILENAME,
        'updated' => $meta['updated'],
    ];
}

// ─── Handle Visibility Toggle ─────────────────────────────────────────────────

function ffw_handle_toggle(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Keine Berechtigung.' );
    }

    check_admin_referer( 'ffw_toggle_entry' );

    $key    = sanitize_text_field( $_POST['entry_key'] ?? '' );
    if ( $key === '' ) return;

    $hidden = ffw_hidden_keys();

    if ( in_array( $key, $hidden, true ) ) {
        $hidden = array_values( array_filter( $hidden, fn( $k ) => $k !== $key ) );
    } else {
        $hidden[] = $key;
    }

    update_option( 'ffw_hidden_roster_entries', $hidden );
}

// ─── Admin Page Renderer ──────────────────────────────────────────────────────

function ffw_render_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Keine Berechtigung.' );
    }

    $upload_result = null;
    if ( isset( $_POST['ffw_ics_submit'] ) ) {
        $upload_result = ffw_handle_upload();
    }

    if ( isset( $_POST['ffw_toggle_submit'] ) ) {
        ffw_handle_toggle();
    }

    // Load current meta if available
    $meta = null;
    if ( file_exists( FFW_ROSTER_META ) ) {
        $meta = json_decode( file_get_contents( FFW_ROSTER_META ), true );
    }

    $roster     = ffw_load_roster();
    $hidden     = ffw_hidden_keys();
    $page_url   = admin_url( 'tools.php?page=ffw-ics-uploader' );
    ?>
    <div class="wrap">
        <h1>ICS Dienstplan Upload</h1>
        <p>Lade eine <code>.ics</code>-Datei hoch, um den Dienstplan (<code>eAbtRoster.json</code>) zu aktualisieren.</p>

        <?php if ( $upload_result !== null ): ?>
            <?php if ( isset( $upload_result['error'] ) ): ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong>Fehler:</strong> <?php echo esc_html( $upload_result['error'] ); ?></p>
                </div>
            <?php else: ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <strong>Erfolgreich hochgeladen!</strong>
                        <?php echo esc_html( $upload_result['count'] ); ?> Termine importiert.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( $meta !== null && $upload_result === null ): ?>
            <div class="notice notice-info">
                <p>
                    <strong>Letzter Upload:</strong> <?php echo esc_html( $meta['updated'] ); ?>
                    &mdash; <?php echo esc_html( $meta['count'] ); ?> Termine
                    &mdash; Quelle: <?php echo esc_html( $meta['source'] ); ?>
                </p>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="margin-top: 1.5em;">
            <?php wp_nonce_field( 'ffw_ics_upload' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="ics_file">ICS-Datei</label></th>
                    <td>
                        <input type="file" id="ics_file" name="ics_file"
                               accept=".ics,text/calendar" required style="font-size: 1em;">
                        <p class="description">Erlaubtes Format: <code>.ics</code> (iCalendar)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Dienstplan hochladen & konvertieren', 'primary', 'ffw_ics_submit' ); ?>
        </form>

        <?php if ( ! empty( $roster ) ): ?>
            <hr>
            <h2>
                Alle Termine
                <span style="font-size: 0.75em; font-weight: normal; color: #666;">
                    (<?php echo esc_html( count( $roster ) ); ?> gesamt,
                     <?php echo esc_html( count( $hidden ) ); ?> ausgeblendet)
                </span>
            </h2>
            <p class="description">
                Ausgeblendete Termine werden im Frontend nicht angezeigt, aber nicht gelöscht.
                Sie bleiben beim nächsten Upload erhalten, solange die Kombination aus Datum und Uhrzeit identisch ist.
            </p>

            <style>
                .ffw-roster-table { border-collapse: collapse; width: 100%; margin-top: 1em; }
                .ffw-roster-table th,
                .ffw-roster-table td { padding: 7px 10px; border: 1px solid #c3c4c7; text-align: left; vertical-align: middle; }
                .ffw-roster-table th { background: #f0f0f1; }
                .ffw-roster-table tr.ffw-hidden td { opacity: 0.45; }
                .ffw-roster-table td.ffw-action { white-space: nowrap; }
            </style>

            <table class="ffw-roster-table widefat">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Von</th>
                        <th>Bis</th>
                        <th>Thema</th>
                        <th>Art</th>
                        <th>Ort</th>
                        <th>Dauer</th>
                        <th>Sichtbarkeit</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $roster as $entry ):
                    $key     = ffw_entry_key( $entry );
                    $is_hidden = in_array( $key, $hidden, true );
                ?>
                    <tr class="<?php echo $is_hidden ? 'ffw-hidden' : ''; ?>">
                        <td><?php echo esc_html( $entry['Datum'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['von']   ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['bis']   ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['Thema'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['Art']   ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['Ortsteil-Feuerwehr'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['Dauer'] ?? '' ); ?></td>
                        <td class="ffw-action">
                            <form method="post" action="<?php echo esc_url( $page_url ); ?>">
                                <?php wp_nonce_field( 'ffw_toggle_entry' ); ?>
                                <input type="hidden" name="entry_key" value="<?php echo esc_attr( $key ); ?>">
                                <button type="submit" name="ffw_toggle_submit"
                                        class="button button-small <?php echo $is_hidden ? 'button-primary' : ''; ?>">
                                    <?php echo $is_hidden ? 'Einblenden' : 'Ausblenden'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr>
        <h2>Technische Details</h2>
        <ul>
            <li><strong>Gespeicherte Datei:</strong> <code><?php echo esc_html( FFW_ROSTER_FILE ); ?></code></li>
            <li><strong>REST API:</strong> <code><?php echo esc_html( rest_url( 'types/v1/getRoster/' ) ); ?></code> — gibt das JSON-Array direkt zurück (CORS-kompatibel)</li>
            <li><strong>Format:</strong> JSON-Array (kompatibel mit <code>icsToJsonConverter.js</code>)</li>
        </ul>
    </div>
    <?php
}

// ─── REST Endpoint: GET /wp-json/types/v1/getRoster/ ─────────────────────────
// Returns the roster JSON array directly so the frontend avoids CORS issues
// with direct file access. Hidden entries are excluded.

add_action( 'rest_api_init', function () {
    register_rest_route( 'types/v1', '/getRoster/', [
        'methods'             => 'GET',
        'callback'            => 'ffw_rest_get_roster',
        'permission_callback' => '__return_true',
    ] );
} );

function ffw_rest_get_roster(): WP_REST_Response {
    $roster = ffw_load_roster();
    if ( empty( $roster ) ) {
        return new WP_REST_Response( [], 200 );
    }

    $hidden  = ffw_hidden_keys();
    $visible = array_values(
        array_filter( $roster, fn( $e ) => ! in_array( ffw_entry_key( $e ), $hidden, true ) )
    );

    return new WP_REST_Response( $visible, 200 );
}
