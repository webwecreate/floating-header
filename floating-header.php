<?php
/**
 * Plugin Name: Floating Header
 * Description: Display floating logo images with animation behind a centered title.
 * Version:     1.0.1
 * License:     GPLv2
 * Text Domain: floating-header
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FH_VERSION',  '1.0.4' );
define( 'FH_DIR',      plugin_dir_path( __FILE__ ) );
define( 'FH_URL',      plugin_dir_url( __FILE__ ) );
define( 'FH_INCLUDES', FH_DIR . 'includes/' );
define( 'FH_ASSETS',   FH_URL . 'assets/' );

$fh_required = [
    FH_INCLUDES . 'cpt.php',
    FH_INCLUDES . 'options-page.php',
    FH_INCLUDES . 'shortcode.php',
];

$fh_missing = array_filter( $fh_required, fn( $f ) => ! file_exists( $f ) );

if ( ! empty( $fh_missing ) ) {
    add_action( 'admin_notices', function () use ( $fh_missing ) {
        $list = implode( ', ', array_map(
            fn( $f ) => '<code>' . esc_html( str_replace( FH_DIR, '', $f ) ) . '</code>',
            $fh_missing
        ) );
        echo '<div class="notice notice-error"><p>'
            . '<strong>Floating Header:</strong> ไม่พบไฟล์ที่จำเป็น: ' . $list
            . '</p></div>';
    } );
    return;
}

foreach ( $fh_required as $fh_file ) {
    require_once $fh_file;
}
