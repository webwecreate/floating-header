<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
    exit;
}

function fh_register_cpt() {
    register_post_type( 'fh_logo', [
        'labels' => [
            'name'               => __( 'Floating Logos', 'floating-header' ),
            'singular_name'      => __( 'Floating Logo', 'floating-header' ),
            'add_new_item'       => __( 'Add New Logo', 'floating-header' ),
            'edit_item'          => __( 'Edit Logo', 'floating-header' ),
            'not_found'          => __( 'No logos found.', 'floating-header' ),
            'not_found_in_trash' => __( 'No logos found in Trash.', 'floating-header' ),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-format-gallery',
        'supports'        => [ 'title', 'thumbnail', 'page-attributes' ],
        'rewrite'         => false,
        'capability_type' => 'post',
    ] );
}
add_action( 'init', 'fh_register_cpt' );

function fh_set_default_order( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( $query->get( 'post_type' ) !== 'fh_logo' ) {
        return;
    }
    if ( ! $query->get( 'orderby' ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'fh_set_default_order' );

function fh_add_thumbnail_column( $columns ) {
    $reordered = [];
    foreach ( $columns as $key => $label ) {
        if ( $key === 'title' ) {
            $reordered['fh_thumbnail'] = __( 'Image', 'floating-header' );
        }
        $reordered[ $key ] = $label;
    }
    return $reordered;
}
add_filter( 'manage_fh_logo_posts_columns', 'fh_add_thumbnail_column' );

function fh_render_thumbnail_column( $column, $post_id ) {
    if ( $column !== 'fh_thumbnail' ) {
        return;
    }
    $thumb = get_the_post_thumbnail( $post_id, [ 60, 60 ] );
    echo $thumb ?: '<span style="color:#aaa;">—</span>';
}
add_action( 'manage_fh_logo_posts_custom_column', 'fh_render_thumbnail_column', 10, 2 );

function fh_enqueue_sortable( $hook ) {
    if ( $hook !== 'edit.php' ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'fh_logo' ) {
        return;
    }
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_style( 'fh-admin-css', FH_ASSETS . 'admin.css', [], FH_VERSION );
    wp_add_inline_script( 'jquery-ui-sortable', fh_sortable_inline_script() );
}
add_action( 'admin_enqueue_scripts', 'fh_enqueue_sortable' );

function fh_sortable_inline_script() {
    $nonce = wp_create_nonce( 'fh_sort_nonce' );
    $ajax  = esc_url( admin_url( 'admin-ajax.php' ) );

    return <<<JS
jQuery(function($){
    var \$tbody = $('#the-list');

    \$tbody.sortable({
        items: 'tr',
        axis: 'y',
        handle: '.fh-drag-handle',
        placeholder: 'fh-sort-placeholder',
        helper: function(e, tr) {
            tr.children().each(function() {
                $(this).width($(this).width());
            });
            return tr;
        },
        stop: function() {
            var order = [];
            \$tbody.find('tr').each(function() {
                var id = $(this).find('input[name="post[]"]').val()
                    || $(this).attr('id').replace('post-', '');
                if (id) order.push(id);
            });
            $.post('{$ajax}', {
                action: 'fh_save_sort_order',
                order: order,
                nonce: '{$nonce}'
            });
        }
    });

    \$tbody.find('td.fh_thumbnail').prepend('<span class="fh-drag-handle">⠿</span>');
});
JS;
}

function fh_save_sort_order_ajax() {
    check_ajax_referer( 'fh_sort_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Unauthorized', 403 );
    }

    $order = array_map( 'absint', (array) ( $_POST['order'] ?? [] ) );

    foreach ( $order as $position => $post_id ) {
        if ( $post_id > 0 ) {
            wp_update_post( [ 'ID' => $post_id, 'menu_order' => $position ] );
        }
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_fh_save_sort_order', 'fh_save_sort_order_ajax' );
