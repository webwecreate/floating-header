<?php
/**
 * Plugin Name: Floating Header
 * Plugin URI:  https://example.com/floating-header
 * Description: แสดง Logo ลอยขึ้น-ลง พร้อม Title / Subtitle ผ่าน Shortcode [floating_header]
 * Version:     1.0.0
 * Author:      Your Name
 * Text Domain: floating-header
 * License:     GPLv2
 * Requires at least: 5.8
 * Tested up to: 6.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FH_VERSION',  '1.0.0' );
define( 'FH_DIR',      plugin_dir_path( __FILE__ ) );
define( 'FH_URL',      plugin_dir_url( __FILE__ ) );
define( 'FH_INCLUDES', FH_DIR . 'includes/' );
define( 'FH_ASSETS',   FH_URL . 'assets/' );

$fh_required_files = array(
    FH_INCLUDES . 'cpt.php',
    FH_INCLUDES . 'options-page.php',
    FH_INCLUDES . 'shortcode.php',
);

$fh_missing = array();

foreach ( $fh_required_files as $fh_file ) {
    if ( file_exists( $fh_file ) ) {
        require_once $fh_file;
    } else {
        $fh_missing[] = str_replace( FH_DIR, '', $fh_file );
    }
}

if ( ! empty( $fh_missing ) ) {
    add_action( 'admin_notices', function() use ( $fh_missing ) {
        $list = implode( ', ', array_map( 'esc_html', $fh_missing ) );
        echo '<div class="notice notice-error"><p>';
        echo '<strong>Floating Header:</strong> ไฟล์ต่อไปนี้หายไป — plugin ทำงานไม่ได้จนกว่าจะติดตั้งครบ: <code>' . $list . '</code>';
        echo '</p></div>';
    } );
}
