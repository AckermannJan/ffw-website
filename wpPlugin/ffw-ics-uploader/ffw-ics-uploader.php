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

// ─── Admin Page Renderer ──────────────────────────────────────────────────────

function ffw_render_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Keine Berechtigung.' );
    }

    $result = null;
    if ( isset( $_POST['ffw_ics_submit'] ) ) {
        $result = ffw_handle_upload();
    }

    // Load current meta if available
    $meta = null;
    if ( file_exists( FFW_ROSTER_META ) ) {
        $meta = json_decode( file_get_contents( FFW_ROSTER_META ), true );
    }

    $roster_url = FFW_ROSTER_URL . '/' . FFW_ROSTER_FILENAME;
    ?>
    <div class="wrap">
        <h1>ICS Dienstplan Upload</h1>
        <p>Lade eine <code>.ics</code>-Datei hoch, um den Dienstplan (<code>eAbtRoster.json</code>) zu aktualisieren.</p>

        <?php if ( $result !== null ): ?>
            <?php if ( isset( $result['error'] ) ): ?>
                <div class="notice notice-error">
                    <p><strong>Fehler:</strong> <?php echo esc_html( $result['error'] ); ?></p>
                </div>
            <?php else: ?>
                <div class="notice notice-success">
                    <p>
                        <strong>Erfolgreich hochgeladen!</strong>
                        <?php echo esc_html( $result['count'] ); ?> Termine gespeichert.
                        <br>
                        URL: <a href="<?php echo esc_url( $result['url'] ); ?>" target="_blank"><?php echo esc_html( $result['url'] ); ?></a>
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( $meta !== null && $result === null ): ?>
            <div class="notice notice-info">
                <p>
                    <strong>Letzter Upload:</strong> <?php echo esc_html( $meta['updated'] ); ?>
                    &mdash; <?php echo esc_html( $meta['count'] ); ?> Termine
                    &mdash; Quelle: <?php echo esc_html( $meta['source'] ); ?>
                    <br>
                    URL: <a href="<?php echo esc_url( $roster_url ); ?>" target="_blank"><?php echo esc_html( $roster_url ); ?></a>
                </p>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="margin-top: 1.5em;">
            <?php wp_nonce_field( 'ffw_ics_upload' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="ics_file">ICS-Datei</label>
                    </th>
                    <td>
                        <input
                            type="file"
                            id="ics_file"
                            name="ics_file"
                            accept=".ics,text/calendar"
                            required
                            style="font-size: 1em;"
                        >
                        <p class="description">Erlaubtes Format: <code>.ics</code> (iCalendar)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Dienstplan hochladen & konvertieren', 'primary', 'ffw_ics_submit' ); ?>
        </form>

        <hr>
        <h2>Technische Details</h2>
        <ul>
            <li><strong>Gespeicherte Datei:</strong> <code><?php echo esc_html( FFW_ROSTER_FILE ); ?></code></li>
            <li><strong>Öffentliche URL:</strong> <a href="<?php echo esc_url( $roster_url ); ?>" target="_blank"><?php echo esc_url( $roster_url ); ?></a></li>
            <li><strong>Format:</strong> JSON-Array (kompatibel mit <code>icsToJsonConverter.js</code>)</li>
        </ul>
    </div>
    <?php
}

// ─── REST Endpoint: GET /wp-json/types/v1/getRosterUrl/ ───────────────────────

add_action( 'rest_api_init', function () {
    register_rest_route( 'types/v1', '/getRosterUrl/', [
        'methods'             => 'GET',
        'callback'            => 'ffw_rest_get_roster_url',
        'permission_callback' => '__return_true',
    ] );
} );

function ffw_rest_get_roster_url(): WP_REST_Response {
    $url  = FFW_ROSTER_URL . '/' . FFW_ROSTER_FILENAME;
    $meta = file_exists( FFW_ROSTER_META )
        ? json_decode( file_get_contents( FFW_ROSTER_META ), true )
        : null;

    return new WP_REST_Response( [
        'url'     => $url,
        'updated' => $meta['updated'] ?? null,
        'count'   => $meta['count']   ?? null,
    ], 200 );
}
