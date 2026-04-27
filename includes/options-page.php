<?php
/**
 * Options Page — Floating Header Plugin
 *
 * @version     1.0.0
 * @package     FloatingHeader
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FH_VERSION' ) ) {
	exit;
}

add_action( 'admin_menu', 'fh_register_options_page' );

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

add_action( 'admin_init', 'fh_register_settings' );

function fh_register_settings() {
	register_setting(
		'fh_options_group',
		'fh_title',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	register_setting(
		'fh_options_group',
		'fh_subtitle',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'wp_kses_post',
			'default'           => '',
		)
	);
}

function fh_render_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'floating-header' ) );
	}

	if ( isset( $_POST['fh_options_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fh_options_nonce'] ) ), 'fh_save_options' ) ) {
		$fh_title = isset( $_POST['fh_title'] ) ? sanitize_text_field( wp_unslash( $_POST['fh_title'] ) ) : '';
		update_option( 'fh_title', $fh_title );

		$fh_subtitle = isset( $_POST['fh_subtitle'] ) ? wp_kses_post( wp_unslash( $_POST['fh_subtitle'] ) ) : '';
		update_option( 'fh_subtitle', $fh_subtitle );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'floating-header' ) . '</p></div>';
	}

	$fh_title    = get_option( 'fh_title', '' );
	$fh_subtitle = get_option( 'fh_subtitle', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Header Settings', 'floating-header' ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'fh_save_options', 'fh_options_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="fh_title"><?php esc_html_e( 'Title', 'floating-header' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="fh_title"
								name="fh_title"
								value="<?php echo esc_attr( $fh_title ); ?>"
								class="regular-text"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="fh_subtitle"><?php esc_html_e( 'Subtitle', 'floating-header' ); ?></label>
						</th>
						<td>
							<?php
							wp_editor(
								$fh_subtitle,
								'fh_subtitle',
								array(
									'textarea_name' => 'fh_subtitle',
									'media_buttons' => false,
									'textarea_rows' => 8,
									'teeny'         => true,
								)
							);
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( __( 'Save Settings', 'floating-header' ) ); ?>
		</form>
	</div>
	<?php
}
