<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
    exit;
}

add_shortcode( 'floating_header', 'fh_render_shortcode' );

function fh_render_shortcode() {
    wp_enqueue_style( 'fh-style', FH_ASSETS . 'style.css', [], FH_VERSION );

    $title    = get_option( 'fh_title', '' );
    $subtitle = get_option( 'fh_subtitle', '' );

    $raw_logos = get_posts( [
        'post_type'      => 'fh_logo',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );

    $logos = [];
    foreach ( $raw_logos as $logo ) {
        if ( ! has_post_thumbnail( $logo->ID ) ) {
            continue;
        }
        $url = get_the_post_thumbnail_url( $logo->ID, 'medium' );
        if ( ! $url ) {
            continue;
        }
        $logos[] = [ 'post' => $logo, 'url' => $url ];
    }

    $total = count( $logos );

    $tier_class = '';
    if ( $total >= 13 ) {
        $tier_class = 'fh-tier-4';
    } elseif ( $total >= 7 ) {
        $tier_class = 'fh-tier-3';
    }

    ob_start();
    ?>
    <section
        class="fh-wrapper <?php echo esc_attr( $tier_class ); ?>"
        data-logo-count="<?php echo esc_attr( $total ); ?>"
    >
        <div class="fh-logo-layer">
            <?php foreach ( $logos as $index => $item ) :
                $logo      = $item['post'];
                $url       = $item['url'];
                $alt_meta  = get_post_meta( $logo->ID, '_wp_attachment_image_alt', true );
                $alt       = $alt_meta ?: get_the_title( $logo->ID );
                $pos       = fh_calc_logo_position( $index, $total );
                $direction = ( $index % 2 === 0 ) ? 'fh-float-up' : 'fh-float-down';
                $delay     = $index * 0.5;
                $duration  = 3 + ( $index % 3 * 0.5 );
            ?>
                <div
                    class="fh-logo <?php echo esc_attr( $direction ); ?>"
                    style="--fh-x:<?php echo (float) $pos['x']; ?>%; --fh-y:<?php echo (float) $pos['y']; ?>%; --fh-delay:<?php echo (float) $delay; ?>s; --fh-duration:<?php echo (float) $duration; ?>s;"
                >
                    <img
                        src="<?php echo esc_url( $url ); ?>"
                        alt="<?php echo esc_attr( $alt ); ?>"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            <?php endforeach; ?>
        </div>

        <div class="fh-title-layer">
            <?php if ( $title !== '' ) : ?>
                <h1 class="fh-title"><?php echo esc_html( $title ); ?></h1>
            <?php endif; ?>
            <?php if ( $subtitle !== '' ) : ?>
                <div class="fh-subtitle"><?php echo wp_kses_post( $subtitle ); ?></div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function fh_calc_logo_position( $index, $total ) {
    if ( $total <= 3 ) {
        $map = [
            [ 'x' => 15, 'y' => 50 ],
            [ 'x' => 50, 'y' => 50 ],
            [ 'x' => 85, 'y' => 50 ],
        ];
        return $map[ $index ] ?? [ 'x' => 50, 'y' => 50 ];
    }

    if ( $total <= 6 ) {
        $x_cols = [ 15, 50, 85 ];
        $y_rows = [ 30, 70 ];
        return [
            'x' => $x_cols[ $index % 3 ],
            'y' => $y_rows[ (int) floor( $index / 3 ) ] ?? 50,
        ];
    }

    if ( $total <= 12 ) {
        $x_cols   = [ 15, 38, 62, 85 ];
        $y_rows   = [ 20, 50, 80 ];
        $x_offset = ( $index % 2 === 0 ) ? 4 : -4;
        $y_offset = ( $index % 2 === 0 ) ? 5 : -5;
        return [
            'x' => ( $x_cols[ $index % 4 ] ?? 50 ) + $x_offset,
            'y' => ( $y_rows[ (int) floor( $index / 4 ) ] ?? 50 ) + $y_offset,
        ];
    }

    return [
        'x' => ( $index * 37 + 13 ) % 78 + 8,
        'y' => ( $index * 53 + 7  ) % 78 + 8,
    ];
}
