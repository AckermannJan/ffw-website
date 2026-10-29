<?php
// Remove all default WP template redirects/lookups
remove_action( 'template_redirect', 'redirect_canonical' );

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
function remove_redirects() {
    add_rewrite_rule( '^/(.+)/?', 'index.php', 'top' );
}
add_action( 'init', 'remove_redirects' );

// Pages get the standard excerpt box (used for news teasers and "Mitmachen")
// and a featured image (fallback for the start page hero).
function ffw_enable_page_features() {
    add_post_type_support( 'page', 'excerpt' );
    add_theme_support( 'post-thumbnails' );
}
add_action( 'init', 'ffw_enable_page_features' );

// Styles the classic editor (TinyMCE) like the content on the Vue site, so
// pages look in wp-admin roughly like they will on feuerwehr-traisa.de.
// The file time busts the editor's cache after each deploy.
function ffw_editor_style() {
    $file = get_template_directory() . '/editor-style.css';
    $version = file_exists( $file ) ? filemtime( $file ) : '1';
    add_editor_style( array(
        'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap',
        get_template_directory_uri() . '/editor-style.css?ver=' . $version,
    ) );
}
add_action( 'after_setup_theme', 'ffw_editor_style' );

/*
 * Helpers for the redesign fields.
 * Types is the free, unmaintained version: every lookup is guarded so a
 * missing plugin or a field that is not configured yet returns '' instead
 * of a fatal error.
 */

function ffw_field( $slug, $postId ) {
    if ( ! $postId || ! function_exists( 'types_get_field_meta_value' ) ) {
        return '';
    }
    $value = types_get_field_meta_value( $slug, $postId );
    return $value ? $value : '';
}

// Types date fields come back either as an array with 'timestamp' or as the
// raw timestamp, depending on the field settings.
function ffw_date_field( $slug, $postId ) {
    $value = ffw_field( $slug, $postId );
    $timestamp = is_array( $value ) ? ( isset( $value['timestamp'] ) ? $value['timestamp'] : '' ) : $value;
    if ( ! $timestamp || ! is_numeric( $timestamp ) ) {
        return '';
    }
    // Types stores local time as a UTC timestamp, same as the frontend reads it.
    return gmdate( 'Y-m-d H:i:s', (int) $timestamp );
}

// Alt text from the media library for an uploaded image URL.
function ffw_image_alt( $url ) {
    if ( ! $url || ! is_string( $url ) ) {
        return '';
    }
    $attachmentId = attachment_url_to_postid( $url );
    return $attachmentId ? (string) get_post_meta( $attachmentId, '_wp_attachment_image_alt', true ) : '';
}

function ffw_excerpt( $post, $words = 40 ) {
    if ( $post->post_excerpt ) {
        return wp_strip_all_tags( $post->post_excerpt );
    }
    return wp_trim_words( wp_strip_all_tags( $post->post_content ), $words, ' …' );
}

// Category label for news: a Types field "kategorie" if set, otherwise the
// title of the parent page (e.g. "Verein", "Einsatzabteilung").
function ffw_category( $post ) {
    $category = ffw_field( 'kategorie', $post->ID );
    if ( $category ) {
        return $category;
    }
    return $post->post_parent ? get_the_title( $post->post_parent ) : '';
}

/*
 * Custom API DOC
 * - getAlarmPost
 *   @info   -> Returns info about specific alarm
 *   @params -> post-id
 *
 * - getAllFromType
 *   @info   -> Returns all posts of a specific form type
 *   @params -> type
 *
 * - getSidebarInfo
 *   @info   -> Returns all all information needed for the sidebar, plus
 *              alarmCountThisYear, hero (start page image) and mitmachen
 *
 * - getArchive
 *   @info   -> Returns archived posts and pages (incl. kategorie, excerpt,
 *              startBildAlt)
 *
 * - getEinsatzabteilung
 *   @info   -> Returns the figures and Wehrführung contact for the
 *              Einsatzabteilung page (Types fields on that page)
 *
 * - getAllAlarmsFromYear
 *   @info   -> Returns all alarms for selected year
 *   @params -> year
 *
 * - getAllMeetings
 *   @info   -> Returns all meetings
 *
 * - getIndexInfo
 *   @info   -> Returns index info (incl. startBildAlt, excerpt, kategorie,
 *              veranstaltungsdatum)
*/

/*
 * Returns data from selected alarm id
 */

function getAlarm( $request_data ) {
    $name = $request_data->get_params()['slug'];
    $args = array(
        'post_type' => 'einsatz',
        'name' => $name,
        'posts_per_page'=>-1,
        'numberposts'=>-1
    );
    $query =  new WP_Query( $args );
    $post =  $query->post;
    $alarmierungszeitpunkt = types_get_field_meta_value( 'alarmierungszeitpunkt', $post->ID );
    $fahrzeuge = types_get_field_meta_value( 'fahrzeuge', $post->ID );
    $einsatzpersonal = types_get_field_meta_value( 'einsatzpersonal', $post->ID );
    $weitereKraefte = types_get_field_meta_value( 'weitere-kraefte', $post->ID );
    $medienbilder = types_get_field_meta_value( 'medienbilder', $post->ID );
    $einsatzfahrzeuge = types_get_field_meta_value( 'einsatzfahrzeuge', $post->ID );
    $einsatzort = types_get_field_meta_value( 'einsatzort', $post->ID );
    $alarmierungsart = types_get_field_meta_value( 'alarmierungsart', $post->ID );
    $einsatzart = types_get_field_meta_value( 'einsatzart', $post->ID );
    $einsatzicon = types_get_field_meta_value( 'einsatzicon', $post->ID );
    $post_content_html = apply_filters('the_content', $post->post_content);

    $array = [];
    $array2 = [];

    foreach ($fahrzeuge as &$value) {
        $array[] = $value[0];
    }
    foreach (array_filter($array) as &$value) {
        $array2[] = $value;
    }

    $data = (object) array(
        'alarmierungszeitpunkt' => $alarmierungszeitpunkt,
        'fahrzeuge' => $array2,
        'einsatzpersonal' => $einsatzpersonal,
        'weitereKraefte' => $weitereKraefte,
        'medienbilder' => $medienbilder,
        'einsatzfahrzeuge' => $einsatzfahrzeuge,
        'einsatzort' => $einsatzort,
        'alarmierungsart' => $alarmierungsart,
        'einsatzart' => $einsatzart,
        'einsatzicon' => $einsatzicon,
        'post_content_html' => $post_content_html,
    );
    $data =  (object) array_merge((array) $data, (array) $post);
    return $data;
}

/*
 * Returns all pages inside the archive
 */

function getArchive( $request_data ) {
    $args = array(
        'post_type' => array( 'post', 'page'),
        'posts_per_page'=>-1,
        'numberposts'=>-1
    );

    $the_query = new WP_Query( $args );
    $posts = $the_query->posts;

    $newPosts = array();
    foreach ($posts as $post) {
        $archiv = types_get_field_meta_value( 'archiv', $post->ID );
        if ( $archiv != "1" ) {
            continue;
        }
        $startBild = types_get_field_meta_value( 'start-bild', $post->ID );
        // Read before post_content is stripped below.
        $excerpt = ffw_excerpt( $post, 30 );
        $post->post_content = strip_tags($post->post_content);
        $data = (object) array(
            'archive' => $archiv,
            'startBild' => $startBild,
            // Added for the redesigned archive; the old fields stay as they were.
            'startBildAlt' => ffw_image_alt( $startBild ),
            'kategorie' => ffw_category( $post ),
            'excerpt' => $excerpt,
        );
        if($archiv == "1"){
            $newPosts[] = (object) array_merge((array) $post, (array) $data);
        }
    }

    return $newPosts;
}

/*
 * Returns data from selected page name
 */

function getPage( $request_data ) {
    $name = $request_data->get_params()['slug'];
    $args = array(
        'post_type' => 'page',
        'name' => $name,
        'posts_per_page'=>-1,
        'numberposts'=>-1
    );

    $the_query = new WP_Query( $args );
    $post = $the_query->post;

    $startBild = types_get_field_meta_value( 'start-bild', $post->ID );
    $attached_images = types_get_field_meta_value( 'medienbilder', $post->ID );
    $any_attached_images = types_get_field_meta_value( 'medienverfugbar', $post->ID );
    $visibleOnStart = types_get_field_meta_value( 'visibleonstart', $post->ID );
    $content = apply_filters('the_content', $post->post_content);
    $data = (object) array(
        'startBild' => $startBild,
        'visibleOnStart' => $visibleOnStart,
        'post_content' => $content,
        'attached_images' => $attached_images,
        'any_attached_images' => $any_attached_images,
    );

    $returnData = (object) array_merge((array) $post, (array) $data);
    return $returnData;
}



/*
 * Returns al posts from selected post type
 */

function getAllFromType( $request_data ) {
    $args = array(
        'post_type' => $request_data->get_params()['type'],
        'posts_per_page'=>-1,
        'numberposts'=>-1
    );

    $the_query = new WP_Query( $args );

    return $the_query;
}



/*
 * Returns sidebar Information
 */
function getSidebarInfo() {
    $latestAlarmArgs = array(
        'posts_per_page' => 1,
        'order'=> 'DESC',
        'post_type' => 'einsatz'
    );

    $latestAlarm = get_posts( $latestAlarmArgs );
    $alarmierungszeitpunkt = (object) array(
        'alarmTime' => types_get_field_meta_value( 'alarmierungszeitpunkt', $latestAlarm[0]->ID ),
        'einsatzart' => ffw_field( 'einsatzart', $latestAlarm[0]->ID ),
    );
    $latestAlarm =  (object) array('latestAlarm' => array_merge((array) $latestAlarm[0],(array) $alarmierungszeitpunkt));

    $nextThreeMeetingsArgs = array(
        'posts_per_page' => 3,
        'order'=> 'ASC',
        'post_type' => 'termin',
        'meta_key' => 'wpcf-zeitpunkt ',
        'orderby' => 'meta_value_num', );

    $nextThreeMeetingsPosts = get_posts( $nextThreeMeetingsArgs );
    $nextThreeMeetings = array();
    foreach ($nextThreeMeetingsPosts as $meeting) {
        $zeitpunkt = (object) array('date' => types_get_field_meta_value( 'zeitpunkt', $meeting->ID ));
        $data = (object) array_merge((array) ($meeting), (array) $zeitpunkt);
        $nextThreeMeetings[] = $data;
    }
    $nextThreeMeetings = (object) array('nextThreeMeetings' => $nextThreeMeetings);

    $sideBarPostArgs = array( 'posts_per_page' => -1,
        'numberposts'=>-1,
        'order'=> 'ASC',
        'orderby' => 'title',
        'post_type' =>  'SideBar' );

    $sideBarPosts = get_posts( $sideBarPostArgs );
    $sideBar = array();
    foreach ($sideBarPosts as $post) {
        $goto = types_get_field_meta_value( 'goto', $post->ID );
        $sideimg = types_get_field_meta_value( 'sideimg', $post->ID );
        $sideimgalt = types_get_field_meta_value( 'sideimgalt', $post->ID );
        $data = (object) array(
            'goto' => $goto,
            'sideimg' => $sideimg,
            'sideimgalt' => $sideimgalt,
        );
        $data = (object) array_merge((array) ($post), (array) $data);
        $sideBar[] = $data;
    }
    $sideBar = (object) array('sideBarPosts' => $sideBar);

    $returnData = (object) array_merge((array) $sideBar, (array) $nextThreeMeetings);
    $returnData = (object) array_merge((array) $returnData, (array) $latestAlarm);

    $returnData->alarmCountThisYear = ffw_alarm_count_this_year();
    $returnData->hero = ffw_start_hero();
    $returnData->mitmachen = ffw_mitmachen();

    return $returnData;
}

/*
 * Number of alarms whose alarm time is in the current year.
 */
function ffw_alarm_count_this_year() {
    $year = (int) date( 'Y' );
    // Only alarms posted since December of last year can be from this year;
    // this keeps the lookup small instead of reading every alarm ever.
    $alarmIds = get_posts( array(
        'post_type' => 'einsatz',
        'numberposts' => -1,
        'fields' => 'ids',
        'date_query' => array( array( 'after' => ( $year - 1 ) . '-12-01' ) ),
    ) );
    $count = 0;
    foreach ( $alarmIds as $alarmId ) {
        $time = ffw_field( 'alarmierungszeitpunkt', $alarmId );
        if ( is_array( $time ) && ! empty( $time['timestamp'] ) && (int) gmdate( 'Y', (int) $time['timestamp'] ) === $year ) {
            $count++;
        }
    }
    return $count;
}

/*
 * Hero image for the start page, maintained on the (private) page with the
 * slug "startseite": its "Start-Bild" Types field, or else its featured image.
 * Returns null when that page does not exist; the frontend then uses its
 * placeholder.
 */
function ffw_start_hero() {
    $pages = get_posts( array(
        'post_type' => 'page',
        'name' => 'startseite',
        'post_status' => array( 'publish', 'private' ),
        'numberposts' => 1,
    ) );
    if ( empty( $pages ) ) {
        return null;
    }
    $page = $pages[0];
    $image = ffw_field( 'start-bild', $page->ID );
    if ( ! $image ) {
        $image = get_the_post_thumbnail_url( $page->ID, 'large' );
    }
    if ( ! $image ) {
        return null;
    }
    return array(
        'image' => $image,
        'imageAlt' => ffw_image_alt( $image ),
    );
}

/*
 * "Mitmachen" teasers in the sidebar: the excerpt of each group's page.
 * 'to' is the frontend route; 'text' is '' until the page has an excerpt.
 */
function ffw_mitmachen() {
    $groups = array(
        array( 'slug' => 'info', 'title' => 'Wichtelwehr', 'to' => '/kindergruppen/wichtelwehr' ),
        array( 'slug' => 'info-2', 'title' => 'Jugendfeuerwehr', 'to' => '/kindergruppen/jugendfeuerwehr' ),
        array( 'slug' => 'einsatzabteilung', 'title' => 'Einsatzabteilung', 'to' => '/einsatzabteilung' ),
    );
    $entries = array();
    foreach ( $groups as $group ) {
        $pages = get_posts( array(
            'post_type' => 'page',
            'name' => $group['slug'],
            'numberposts' => 1,
        ) );
        $entries[] = array(
            'title' => $group['title'],
            'to' => $group['to'],
            'text' => ( ! empty( $pages ) && $pages[0]->post_excerpt ) ? wp_strip_all_tags( $pages[0]->post_excerpt ) : '',
        );
    }
    return $entries;
}



/*
 * Alarms per year, read from the alarm titles ("Einsatz 27 (2026) …"): the
 * highest number per year is the year's alarm count, also when one post
 * covers several alarms ("Einsatz 21-22"). Cached for 12 hours.
 */
function ffw_alarms_per_year() {
    $cached = get_transient( 'ffw_alarms_per_year' );
    if ( is_array( $cached ) ) {
        return $cached;
    }
    $titles = get_posts( array(
        'post_type' => 'einsatz',
        'numberposts' => -1,
        'fields' => 'ids',
    ) );
    $counts = array();
    foreach ( $titles as $alarmId ) {
        if ( preg_match( '/^Einsatz\s+(\d+)(?:\s*[-–]\s*(\d+))?\s*\((\d{4})\)/u', get_the_title( $alarmId ), $match ) ) {
            $number = ! empty( $match[2] ) ? (int) $match[2] : (int) $match[1];
            $year = (int) $match[3];
            $counts[ $year ] = max( isset( $counts[ $year ] ) ? $counts[ $year ] : 0, $number );
        }
    }
    ksort( $counts );
    set_transient( 'ffw_alarms_per_year', $counts, 12 * HOUR_IN_SECONDS );
    return $counts;
}

// New or edited alarms change the yearly counts.
function ffw_flush_alarm_counts( $postId ) {
    if ( get_post_type( $postId ) === 'einsatz' ) {
        delete_transient( 'ffw_alarms_per_year' );
    }
}
add_action( 'save_post', 'ffw_flush_alarm_counts' );

/*
 * Figures and contact for the Einsatzabteilung page, maintained as Types
 * fields on the page with the slug "einsatzabteilung" (group
 * "Einsatzabteilung"). Empty fields come back as null; the frontend then
 * keeps its built-in values.
 * "einsaetzeProJahr": the field "abt-einsaetze-pro-jahr" if filled in,
 * otherwise the range of the last three complete years ("38–52").
 */
function getEinsatzabteilung() {
    $pages = get_posts( array(
        'post_type' => 'page',
        'name' => 'einsatzabteilung',
        'numberposts' => 1,
    ) );
    $pageId = empty( $pages ) ? 0 : $pages[0]->ID;

    $number = function ( $slug ) use ( $pageId ) {
        $value = ffw_field( $slug, $pageId );
        return is_numeric( $value ) ? (int) $value : null;
    };
    $text = function ( $slug ) use ( $pageId ) {
        $value = ffw_field( $slug, $pageId );
        return is_string( $value ) && trim( $value ) !== '' ? trim( $value ) : null;
    };

    $alarms = $text( 'abt-einsaetze-pro-jahr' );
    $alarmYears = null;
    if ( ! $alarms ) {
        $counts = ffw_alarms_per_year();
        $lastYear = (int) date( 'Y' ) - 1;
        $recent = array();
        foreach ( array( $lastYear - 2, $lastYear - 1, $lastYear ) as $year ) {
            if ( ! empty( $counts[ $year ] ) ) {
                $recent[ $year ] = $counts[ $year ];
            }
        }
        if ( ! empty( $recent ) ) {
            $min = min( $recent );
            $max = max( $recent );
            $alarms = $min === $max ? (string) $min : $min . '–' . $max;
            $alarmYears = array_keys( $recent );
        }
    }

    $image = $text( 'abt-wehrfuehrung-bild' );

    return array(
        'aktive' => $number( 'abt-aktive' ),
        'feuerwehrfrauen' => $number( 'abt-feuerwehrfrauen' ),
        'einsaetzeProJahr' => $alarms,
        'einsaetzeJahre' => $alarmYears,
        'wehrfuehrung' => array(
            'namen' => $text( 'abt-wehrfuehrung' ),
            'email' => $text( 'abt-wehrfuehrung-email' ),
            'bild' => $image,
            'bildAlt' => $image ? ffw_image_alt( $image ) : null,
        ),
    );
}

/*
 * Returns all alarms from selected year
 */

function getAllAlarmsFromYear( $request_data ) {
    $alarmsArgs = array(
        'posts_per_page' => -1,
        'numberposts'=>-1,
        'post_type' => 'einsatz',
        'order' => 'DESC'
    );

    $alarmsPosts = get_posts( $alarmsArgs );
    $alarmsPost = [];
    $test = 0;
    foreach ($alarmsPosts as $post) {
        $alarmierungszeitpunkt = types_get_field_meta_value( 'alarmierungszeitpunkt', $post->ID );
        if(date("Y",$alarmierungszeitpunkt['timestamp']) == $request_data['year']){
            $einsatzicon = types_get_field_meta_value( 'einsatzicon', $post->ID );
            $data = (object) array(
                'alarmierungszeitpunkt' => $alarmierungszeitpunkt,
                'einsatzicon' => $einsatzicon,
            );
            $data = (object) array_merge((array) ($post), (array) $data);
            $test++;
            array_push($alarmsPost, $data);
        }

    }
    return $alarmsPost;
}



/*
 * Returns all meetings
 */

function getAllMeetings() {
    $meetingsArgs = array(
        'posts_per_page' => -1,
        'numberposts'=>-1,
        'order'=> 'ASC',
        'post_type' => 'termin',
        'meta_key' => 'wpcf-zeitpunkt ',
        'orderby' => 'meta_value_num' );

    $meetingsPosts = get_posts( $meetingsArgs );
    $meetings = array();
    foreach ($meetingsPosts as $meeting) {
        $zeitpunkt = types_get_field_meta_value( 'zeitpunkt', $meeting->ID );
        $time = types_get_field_meta_value( 'uhrzeit', $meeting->ID );
        $hasLink = types_get_field_meta_value( 'berichtlinkcheck', $meeting->ID );
        $link = types_get_field_meta_value( 'berichtlink', $meeting->ID );
        $data = (object) array(
            'zeitpunkt' => $zeitpunkt,
            'time' => $time,
            'hasLink' => $hasLink,
            'link' => $link,
        );
        $data = (object) array_merge((array) ($meeting), (array) $data);
        $meetings[] = $data;
    }
    return $meetings;
}



/*
 * Returns index Info
 */

function getIndexInfo() {
    $indexArgs = array(
        'posts_per_page' => -1,
        'numberposts'=>-1,
        'order'=> 'DESC',
        'post_type' =>  array( 'post', 'page'));

    $indexPosts = get_posts( $indexArgs );
    $posts = array();
    foreach ($indexPosts as $post) {
        $visibleonstart = types_get_field_meta_value( 'visibleonstart', $post->ID );
        if ( ! $visibleonstart ) {
            continue;
        }
        $startBild = types_get_field_meta_value( 'start-bild', $post->ID );
        $data = (object) array(
            'visibleonstart' => $visibleonstart,
            'startBild' => $startBild,
            'startBildAlt' => ffw_image_alt( $startBild ),
            'excerpt' => ffw_excerpt( $post ),
            'kategorie' => ffw_category( $post ),
            'veranstaltungsdatum' => ffw_date_field( 'veranstaltungsdatum', $post->ID ),
        );
        $post->post_content = strip_tags($post->post_content);
        $posts[] = (object) array_merge((array) ($post), (array) $data);
    }
    return $posts;
}

function login($request){
    $creds = array();
    $creds['user_login'] = $request["username"];
    $creds['user_password'] =  $request["password"];
    $creds['remember'] = true;
    $user = wp_signon( $creds, false );

    if ( is_wp_error($user) )
        echo $user->get_error_message();

    return $user;
}

add_action( 'rest_api_init', 'register_api_hooks' );

function register_api_hooks() {
    register_rest_route(
        'types/v1', '/login/',
        array(
            'methods'  => 'POST',
            'callback' => 'login',
        )
    );
    register_rest_route( 'types/v1', '/getAllFromType/', array(
        'methods' => 'GET',
        'callback' => 'getAllFromType'
    ));
    register_rest_route( 'types/v1', '/getSidebarInfo/', array(
        'methods' => 'GET',
        'callback' => 'getSidebarInfo'
    ));
    register_rest_route( 'types/v1', '/getIndexInfo/', array(
        'methods' => 'GET',
        'callback' => 'getIndexInfo'
    ));
    register_rest_route( 'types/v1', '/getAllMeetings/', array(
        'methods' => 'GET',
        'callback' => 'getAllMeetings'
    ));
    register_rest_route( 'types/v1', '/getAllAlarmsFromYear/', array(
        'methods' => 'GET',
        'callback' => 'getAllAlarmsFromYear'
    ));
    register_rest_route( 'types/v1', '/getAlarmPost/', array(
        'methods' => 'GET',
        'callback' => 'getAlarm'
    ));
    register_rest_route( 'types/v1', '/getPage/', array(
        'methods' => 'GET',
        'callback' => 'getPage'
    ));
    register_rest_route( 'types/v1', '/getArchive/', array(
        'methods' => 'GET',
        'callback' => 'getArchive'
    ));
    register_rest_route( 'types/v1', '/getEinsatzabteilung/', array(
        'methods' => 'GET',
        'callback' => 'getEinsatzabteilung'
    ));
}
