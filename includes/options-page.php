<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
    exit;
}

function fh_register_options_page() {
    add_submenu_page(
        'edit.php?post_type=fh_logo',
        __( 'Header Settings', 'floating-header' ),
        __( 'Header Settings', 'floating-header' ),
        'manage_options',
        'floating-header-settings',
        'fh_render_options_page'
    );
}
add_action( 'admin_menu', 'fh_register_options_page' );

function fh_register_settings() {
    register_setting( 'fh_options_group', 'fh_title', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ] );
    register_setting( 'fh_options_group', 'fh_subtitle', [
        'sanitize_callback' => 'wp_kses_post',
        'default'           => '',
    ] );
}
add_action( 'admin_init', 'fh_register_settings' );

function fh_render_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $saved = false;

    if (
        isset( $_POST['fh_options_nonce'] )
        && wp_verify_nonce( $_POST['fh_options_nonce'], 'fh_save_options' )
    ) {
        update_option( 'fh_title',    sanitize_text_field( $_POST['fh_title'] ?? '' ) );
        update_option( 'fh_subtitle', wp_kses_post( $_POST['fh_subtitle'] ?? '' ) );
        $saved = true;
    }

    $title    = get_option( 'fh_title', '' );
    $subtitle = get_option( 'fh_subtitle', '' );

    wp_enqueue_style( 'fh-admin-css', FH_ASSETS . 'admin.css', [], FH_VERSION );
    ?>
    <div class="wrap fh-options-wrap">
        <h1><?php esc_html_e( 'Header Settings', 'floating-header' ); ?></h1>

        <?php if ( $saved ) : ?>
            <div class="notice notice-success fh-notice-saved is-dismissible">
                <p><?php esc_html_e( 'Settings saved.', 'floating-header' ); ?></p>
            </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'fh_save_options', 'fh_options_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="fh_title">
                            <?php esc_html_e( 'Title', 'floating-header' ); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="fh_title"
                            name="fh_title"
                            class="regular-text"
                            value="<?php echo esc_attr( $title ); ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php esc_html_e( 'Subtitle', 'floating-header' ); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor( $subtitle, 'fh_subtitle', [
                            'textarea_name' => 'fh_subtitle',
                            'teeny'         => true,
                            'media_buttons' => false,
                            'textarea_rows' => 8,
                        ] );
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Save Settings', 'floating-header' ) ); ?>
        </form>
    </div>
    <?php
}
