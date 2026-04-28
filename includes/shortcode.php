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
// Safety zone: x 20–80%, y 25–75%
// Inset ทุก pattern เพิ่มขึ้นเพื่อรองรับ float animation โดยไม่ชนขอบ

function fh_calc_logo_position( $index, $total, $layout = 'frame' ) {
    switch ( $layout ) {
        case 'lr':      return fh_pos_lr( $index, $total );
        case 'tb':      return fh_pos_tb( $index, $total );
        case 'corners': return fh_pos_corners( $index, $total );
        case 'frame':
        default:        return fh_pos_frame( $index, $total );
    }
}

// Pattern: Frame — interleave (0→top 1→right 2→bottom 3→left วนซ้ำ)
function fh_pos_frame( $index, $total ) {
    $zone = $index % 4;

    if ( $total <= $zone ) {
        return [ 'x' => 50, 'y' => 50 ];
    }

    $zone_count = (int) floor( ( $total - $zone - 1 ) / 4 ) + 1;
    $slot       = (int) floor( $index / 4 );
    $t          = $zone_count > 1 ? $slot / ( $zone_count - 1 ) : 0.5;
    $s          = ( $slot % 2 === 0 ) ? 5 : 0;

    switch ( $zone ) {
        case 0: return [ 'x' => 10 + $t * 80, 'y' => 10 + $s ];      // top
        case 1: return [ 'x' => 86 + $s,       'y' => 20 + $t * 56 ]; // right
        case 2: return [ 'x' => 90 - $t * 80,  'y' => 87 + $s ];      // bottom
        case 3: return [ 'x' => 6  + $s,       'y' => 76 - $t * 56 ]; // left
    }
    return [ 'x' => 50, 'y' => 50 ];
}

// Pattern: Left / Right columns — inset x เพิ่มจาก 7/93 → 10/90
function fh_pos_lr( $index, $total ) {
    $side  = $index % 2;
    $slot  = (int) floor( $index / 2 );
    $slots = max( 1, (int) ceil( $total / 2 ) );
    $t     = $slots > 1 ? $slot / ( $slots - 1 ) : 0.5;
    $sx    = ( $slot % 2 === 0 ) ? 4 : 0;

    return [
        'x' => $side === 0 ? 10 + $sx : 90 - $sx,
        'y' => 10 + $t * 80,
    ];
}

// Pattern: Top / Bottom rows — inset y เพิ่มจาก 7/93 → 10/90
function fh_pos_tb( $index, $total ) {
    $row   = $index % 2;
    $slot  = (int) floor( $index / 2 );
    $slots = max( 1, (int) ceil( $total / 2 ) );
    $t     = $slots > 1 ? $slot / ( $slots - 1 ) : 0.5;
    $sy    = ( $slot % 2 === 0 ) ? 6 : 0;

    return [
        'x' => 10 + $t * 80,
        'y' => $row === 0 ? 10 + $sy : 90 - $sy,
    ];
}

// Pattern: Corners — predefined offsets ป้องกัน logo ซ้อนกันเมื่อ 4–8 ตัว
function fh_pos_corners( $index, $total ) {
    $corner  = $index % 4;
    $slot    = (int) floor( $index / 4 );

    $offsets = [
        [  0,  0 ],
        [  9,  0 ],
        [  0, 11 ],
        [  9, 11 ],
        [  4,  5 ],
    ];
    $off = $offsets[ min( $slot, 4 ) ];

    $map = [
        0 => [ 'x' => 10 + $off[0], 'y' => 10 + $off[1] ], // TL
        1 => [ 'x' => 86 - $off[0], 'y' => 10 + $off[1] ], // TR
        2 => [ 'x' => 10 + $off[0], 'y' => 86 - $off[1] ], // BL
        3 => [ 'x' => 86 - $off[0], 'y' => 86 - $off[1] ], // BR
    ];
    return $map[ $corner ] ?? [ 'x' => 50, 'y' => 50 ];
}
