<?php
/**
 * Floating Header — Shortcode + Layout Logic
 *
 * @package FloatingHeader
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
	exit;
}

add_shortcode( 'floating_header', 'fh_render_shortcode' );

function fh_render_shortcode() {
	wp_enqueue_style( 'fh-style', FH_ASSETS . 'style.css', [], FH_VERSION );

	$logos = get_posts( [
		'post_type'      => 'fh_logo',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	] );

	$title    = get_option( 'fh_title', '' );
	$subtitle = get_option( 'fh_subtitle', '' );
	$total    = count( $logos );

	$tier_class = '';
	if ( $total >= 7 && $total <= 12 ) {
		$tier_class = ' fh-tier-3';
	} elseif ( $total >= 13 ) {
		$tier_class = ' fh-tier-4';
	}

	ob_start();
	?>
	<section class="fh-wrapper<?php echo esc_attr( $tier_class ); ?>" data-logo-count="<?php echo esc_attr( $total ); ?>">
		<div class="fh-logo-layer">
			<?php foreach ( $logos as $i => $logo ) :
				$index    = $i + 1;
				$thumb_id = get_post_thumbnail_id( $logo->ID );

				if ( ! $thumb_id ) {
					continue;
				}

				$img_url = wp_get_attachment_image_url( $thumb_id, 'medium' );

				if ( ! $img_url ) {
					continue;
				}

				$alt       = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
				$alt       = $alt ? $alt : get_the_title( $logo->ID );
				$direction = ( $index % 2 !== 0 ) ? 'fh-float-up' : 'fh-float-down';
				$delay     = $index * 0.5;
				$duration  = 3 + ( $index % 3 ) * 0.5;
				$pos       = fh_calc_logo_position( $index, $total );
			?>
				<div class="fh-logo <?php echo esc_attr( $direction ); ?>"
				     style="--fh-x:<?php echo esc_attr( $pos['x'] ); ?>%;--fh-y:<?php echo esc_attr( $pos['y'] ); ?>%;--fh-delay:<?php echo esc_attr( $delay ); ?>s;--fh-duration:<?php echo esc_attr( $duration ); ?>s">
					<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
				</div>
			<?php endforeach; ?>
		</div>

		<div class="fh-title-layer">
			<?php if ( $title ) : ?>
				<h1 class="fh-title"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<div class="fh-subtitle"><?php echo wp_kses_post( $subtitle ); ?></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function fh_calc_logo_position( $index, $total ) {
	if ( $total <= 3 ) {
		$grids = [
			1 => [ [ 50, 50 ] ],
			2 => [ [ 25, 50 ], [ 75, 50 ] ],
			3 => [ [ 15, 50 ], [ 50, 50 ], [ 85, 50 ] ],
		];
		$grid = $grids[ $total ] ?? [ [ 50, 50 ] ];
		$pos  = $grid[ $index - 1 ] ?? [ 50, 50 ];

		return [ 'x' => $pos[0], 'y' => $pos[1] ];
	}

	if ( $total <= 6 ) {
		$x_cols = [ 15, 50, 85 ];
		$y_rows = [ 30, 70 ];
		$col    = ( $index - 1 ) % 3;
		$row    = intval( ( $index - 1 ) / 3 );

		return [
			'x' => $x_cols[ $col ] ?? 50,
			'y' => $y_rows[ $row ] ?? 50,
		];
	}

	if ( $total <= 12 ) {
		$x_cols = [ 12, 35, 62, 85 ];
		$y_rows = [ 18, 50, 82 ];
		$col    = ( $index - 1 ) % 4;
		$row    = intval( ( $index - 1 ) / 4 );
		$x_off  = ( $index % 2 === 0 ) ? 4 : -4;
		$y_off  = ( $index % 3 === 0 ) ? 5 : 0;

		return [
			'x' => ( $x_cols[ $col ] ?? 50 ) + $x_off,
			'y' => ( $y_rows[ $row ] ?? 50 ) + $y_off,
		];
	}

	$x = ( ( $index * 37 + 13 ) % 78 ) + 8;
	$y = ( ( $index * 53 + 7  ) % 78 ) + 8;

	return [ 'x' => $x, 'y' => $y ];
}
