<?php
/**
 * File:    includes/cpt.php
 * Version: 1.0.0
 * Part:    Custom Post Type — fh_logo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'FH_VERSION' ) ) {
    exit;
}

function fh_register_cpt() {

    $labels = array(
        'name'               => __( 'Floating Logos', 'floating-header' ),
        'singular_name'      => __( 'Floating Logo', 'floating-header' ),
        'add_new'            => __( 'Add New Logo', 'floating-header' ),
        'add_new_item'       => __( 'Add New Floating Logo', 'floating-header' ),
        'edit_item'          => __( 'Edit Floating Logo', 'floating-header' ),
        'new_item'           => __( 'New Floating Logo', 'floating-header' ),
        'view_item'          => __( 'View Floating Logo', 'floating-header' ),
        'search_items'       => __( 'Search Floating Logos', 'floating-header' ),
        'not_found'          => __( 'No logos found', 'floating-header' ),
        'not_found_in_trash' => __( 'No logos found in Trash', 'floating-header' ),
        'menu_name'          => __( 'Floating Logos', 'floating-header' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => false,
        'show_in_admin_bar'  => false,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
        'has_archive'        => false,
        'rewrite'            => false,
        'query_var'          => false,
        'menu_icon'          => 'dashicons-format-gallery',
        'menu_position'      => 25,
    );

    register_post_type( 'fh_logo', $args );
}
add_action( 'init', 'fh_register_cpt' );

function fh_set_default_order( $query ) {
    if ( ! is_admin() ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || 'edit-fh_logo' !== $screen->id ) {
        return;
    }

    if ( ! $query->is_main_query() ) {
        return;
    }

    if ( empty( $query->get( 'orderby' ) ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order',   'ASC' );
    }
}
add_action( 'pre_get_posts', 'fh_set_default_order' );

function fh_save_sort_order() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( -1 );
    }

    if ( ! check_ajax_referer( 'fh_sort_nonce', 'nonce', false ) ) {
        wp_die( -1 );
    }

    $order = isset( $_POST['order'] ) ? array_map( 'intval', $_POST['order'] ) : array();

    if ( empty( $order ) ) {
        wp_send_json_error( 'No order data' );
    }

    foreach ( $order as $position => $post_id ) {
        wp_update_post( array(
            'ID'         => $post_id,
            'menu_order' => $position,
        ) );
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_fh_save_sort_order', 'fh_save_sort_order' );

function fh_enqueue_sort_script( $hook ) {
    if ( 'edit.php' !== $hook ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || 'edit-fh_logo' !== $screen->id ) {
        return;
    }

    wp_enqueue_script( 'jquery-ui-sortable' );

    wp_add_inline_script( 'jquery-ui-sortable', fh_get_sort_script() );

    if ( file_exists( FH_DIR . 'assets/admin.css' ) ) {
        wp_enqueue_style( 'fh-admin', FH_ASSETS . 'admin.css', array(), FH_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', 'fh_enqueue_sort_script' );

function fh_get_sort_script() {
    $nonce = wp_create_nonce( 'fh_sort_nonce' );
    return <<<JS
jQuery(function($){
    var \$tbody = $('table.wp-list-table tbody');
    if ( ! \$tbody.length ) return;

    \$tbody.sortable({
        items: 'tr',
        cursor: 'move',
        axis: 'y',
        containment: 'parent',
        update: function(){
            var order = [];
            \$tbody.find('tr').each(function(){
                var id = parseInt( $(this).find('input[name="post[]"]').val(), 10 );
                if ( id ) order.push( id );
            });
            $.post( ajaxurl, {
                action : 'fh_save_sort_order',
                nonce  : '{$nonce}',
                order  : order
            });
        }
    });

    \$tbody.find('tr').css('cursor','move');
});
JS;
}

function fh_add_thumbnail_column( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        if ( 'title' === $key ) {
            $new['fh_thumbnail'] = __( 'Image', 'floating-header' );
        }
        $new[ $key ] = $value;
    }
    return $new;
}
add_filter( 'manage_fh_logo_posts_columns', 'fh_add_thumbnail_column' );

function fh_render_thumbnail_column( $column, $post_id ) {
    if ( 'fh_thumbnail' !== $column ) {
        return;
    }

    $thumb = get_the_post_thumbnail( $post_id, array( 60, 60 ) );
    echo $thumb ? $thumb : '<span aria-hidden="true">—</span>';
}
add_action( 'manage_fh_logo_posts_custom_column', 'fh_render_thumbnail_column', 10, 2 );
