<?php
/**
 * Floating Header — shortcode.php
 * Version: 1.0.3
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
    exit;
}

function fh_register_frontend_style() {
    wp_register_style( 'fh-style', FH_ASSETS . 'style.css', [], FH_VERSION );
}
add_action( 'wp_enqueue_scripts', 'fh_register_frontend_style' );

add_shortcode( 'floating_header', 'fh_render_shortcode' );

function fh_render_shortcode( $atts ) {
    $atts = shortcode_atts(
        [ 'layout' => get_option( 'fh_layout', 'frame' ) ],
        $atts,
        'floating_header'
    );
    $layout = sanitize_key( $atts['layout'] );

    wp_enqueue_style( 'fh-style' );

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
        $thumbnail_id = get_post_thumbnail_id( $logo->ID );
        if ( ! $thumbnail_id ) {
            continue;
        }
        $url = wp_get_attachment_image_url( $thumbnail_id, 'medium' )
            ?: wp_get_attachment_image_url( $thumbnail_id, 'full' );
        if ( ! $url ) {
            continue;
        }
        $alt     = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true )
                ?: get_the_title( $logo->ID );
        $logos[] = [ 'url' => $url, 'alt' => $alt ];
    }

    $total      = count( $logos );
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
        data-layout="<?php echo esc_attr( $layout ); ?>"
    >
        <div class="fh-logo-layer">
            <?php foreach ( $logos as $index => $item ) :
                $pos       = fh_calc_logo_position( $index, $total, $layout );
                $direction = ( $index % 2 === 0 ) ? 'fh-float-up' : 'fh-float-down';
                $delay     = round( $index * 0.5, 1 );
                $duration  = round( 3 + ( $index % 3 * 0.5 ), 1 );
            ?>
                <div
                    class="fh-logo <?php echo esc_attr( $direction ); ?>"
                    style="--fh-x:<?php echo (float) $pos['x']; ?>%;--fh-y:<?php echo (float) $pos['y']; ?>%;--fh-delay:<?php echo $delay; ?>s;--fh-duration:<?php echo $duration; ?>s;"
                >
                    <img
                        src="<?php echo esc_url( $item['url'] ); ?>"
                        alt="<?php echo esc_attr( $item['alt'] ); ?>"
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

// ─── Position Calculators ─────────────────────────────────────────────────────
// Safety zone: x 20–80%, y 25–75% — ทุก pattern วาง logo นอก zone นี้

function fh_calc_logo_position( $index, $total, $layout = 'frame' ) {
    switch ( $layout ) {
        case 'lr':      return fh_pos_lr( $index, $total );
        case 'tb':      return fh_pos_tb( $index, $total );
        case 'corners': return fh_pos_corners( $index, $total );
        case 'frame':
        default:        return fh_pos_frame( $index, $total );
    }
}

// Pattern: Frame — กระจายรอบขอบ 4 ด้าน (แนะนำ)
function fh_pos_frame( $index, $total ) {
    $per_zone   = max( 1, (int) ceil( $total / 4 ) );
    $zone       = min( 3, (int) floor( $index / $per_zone ) );
    $slot       = $index % $per_zone;
    $zone_count = max( 1, min( $per_zone, $total - $zone * $per_zone ) );

    $t = $zone_count > 1 ? $slot / ( $zone_count - 1 ) : 0.5;
    $s = ( $slot % 2 === 0 ) ? 5 : 0; // stagger ให้ไม่เรียงกันเป๊ะ

    switch ( $zone ) {
        case 0: return [ 'x' => 8  + $t * 84, 'y' => 6  + $s ];      // top
        case 1: return [ 'x' => 84 + $s,       'y' => 20 + $t * 60 ]; // right
        case 2: return [ 'x' => 92 - $t * 84,  'y' => 84 + $s ];      // bottom
        case 3: return [ 'x' => 4  + $s,       'y' => 80 - $t * 60 ]; // left
    }
    return [ 'x' => 50, 'y' => 50 ];
}

// Pattern: Left / Right columns
function fh_pos_lr( $index, $total ) {
    $side  = $index % 2; // 0=left, 1=right
    $slot  = (int) floor( $index / 2 );
    $slots = max( 1, (int) ceil( $total / 2 ) );
    $t     = $slots > 1 ? $slot / ( $slots - 1 ) : 0.5;
    $sx    = ( $slot % 2 === 0 ) ? 4 : 0;

    return [
        'x' => $side === 0 ? 7 + $sx : 93 - $sx,
        'y' => 10 + $t * 80,
    ];
}

// Pattern: Top / Bottom rows
function fh_pos_tb( $index, $total ) {
    $row   = $index % 2; // 0=top, 1=bottom
    $slot  = (int) floor( $index / 2 );
    $slots = max( 1, (int) ceil( $total / 2 ) );
    $t     = $slots > 1 ? $slot / ( $slots - 1 ) : 0.5;
    $sy    = ( $slot % 2 === 0 ) ? 6 : 0;

    return [
        'x' => 8 + $t * 84,
        'y' => $row === 0 ? 7 + $sy : 93 - $sy,
    ];
}

// Pattern: Corners — กระจาย 4 มุม
function fh_pos_corners( $index, $total ) {
    $corner = $index % 4; // 0=TL 1=TR 2=BL 3=BR
    $slot   = (int) floor( $index / 4 );
    $sx     = ( $slot % 3 ) * 6;
    $sy     = (int) floor( $slot / 3 ) * 8;

    $map = [
        0 => [ 'x' => 6  + $sx, 'y' => 6  + $sy ],
        1 => [ 'x' => 88 - $sx, 'y' => 6  + $sy ],
        2 => [ 'x' => 6  + $sx, 'y' => 84 - $sy ],
        3 => [ 'x' => 88 - $sx, 'y' => 84 - $sy ],
    ];
    return $map[ $corner ] ?? [ 'x' => 50, 'y' => 50 ];
}
