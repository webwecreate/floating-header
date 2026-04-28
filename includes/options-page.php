<?php
/**
 * Floating Header — options-page.php
 * Version: 1.0.3
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'FH_VERSION' ) ) {
    exit;
}

// ─── Layout definitions (shared between settings UI and shortcode preview) ───

function fh_get_layouts() {
    return [
        'frame'   => [
            'label' => 'Frame',
            'desc'  => 'กระจายรอบขอบ (แนะนำ)',
            'svg'   => '<svg width="88" height="54" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="88" height="54" rx="5" fill="#f1f0fd"/>
                <rect x="20" y="14" width="48" height="26" rx="3" fill="#e0dffb" stroke="#c7c4f8" stroke-width="0.5" stroke-dasharray="3 2"/>
                <circle cx="10" cy="10" r="4" fill="#6d62e8"/><circle cx="30" cy="6"  r="4" fill="#6d62e8"/>
                <circle cx="58" cy="6"  r="4" fill="#6d62e8"/><circle cx="78" cy="10" r="4" fill="#6d62e8"/>
                <circle cx="78" cy="27" r="4" fill="#6d62e8"/>
                <circle cx="78" cy="44" r="4" fill="#6d62e8"/><circle cx="58" cy="48" r="4" fill="#6d62e8"/>
                <circle cx="30" cy="48" r="4" fill="#6d62e8"/><circle cx="10" cy="44" r="4" fill="#6d62e8"/>
                <circle cx="10" cy="27" r="4" fill="#6d62e8"/>
            </svg>',
        ],
        'lr'      => [
            'label' => 'Left / Right',
            'desc'  => 'สองคอลัมน์ ซ้าย-ขวา',
            'svg'   => '<svg width="88" height="54" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="88" height="54" rx="5" fill="#f1f0fd"/>
                <rect x="20" y="14" width="48" height="26" rx="3" fill="#e0dffb" stroke="#c7c4f8" stroke-width="0.5" stroke-dasharray="3 2"/>
                <circle cx="9"  cy="12" r="4" fill="#6d62e8"/><circle cx="9"  cy="27" r="4" fill="#6d62e8"/>
                <circle cx="9"  cy="42" r="4" fill="#6d62e8"/>
                <circle cx="79" cy="12" r="4" fill="#6d62e8"/><circle cx="79" cy="27" r="4" fill="#6d62e8"/>
                <circle cx="79" cy="42" r="4" fill="#6d62e8"/>
            </svg>',
        ],
        'tb'      => [
            'label' => 'Top / Bottom',
            'desc'  => 'สองแถว บน-ล่าง',
            'svg'   => '<svg width="88" height="54" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="88" height="54" rx="5" fill="#f1f0fd"/>
                <rect x="20" y="14" width="48" height="26" rx="3" fill="#e0dffb" stroke="#c7c4f8" stroke-width="0.5" stroke-dasharray="3 2"/>
                <circle cx="14" cy="7"  r="4" fill="#6d62e8"/><circle cx="30" cy="7"  r="4" fill="#6d62e8"/>
                <circle cx="44" cy="7"  r="4" fill="#6d62e8"/><circle cx="58" cy="7"  r="4" fill="#6d62e8"/>
                <circle cx="74" cy="7"  r="4" fill="#6d62e8"/>
                <circle cx="14" cy="47" r="4" fill="#6d62e8"/><circle cx="30" cy="47" r="4" fill="#6d62e8"/>
                <circle cx="44" cy="47" r="4" fill="#6d62e8"/><circle cx="58" cy="47" r="4" fill="#6d62e8"/>
                <circle cx="74" cy="47" r="4" fill="#6d62e8"/>
            </svg>',
        ],
        'corners' => [
            'label' => 'Corners',
            'desc'  => 'กระจาย 4 มุม',
            'svg'   => '<svg width="88" height="54" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="88" height="54" rx="5" fill="#f1f0fd"/>
                <rect x="20" y="14" width="48" height="26" rx="3" fill="#e0dffb" stroke="#c7c4f8" stroke-width="0.5" stroke-dasharray="3 2"/>
                <circle cx="9"  cy="9"  r="4" fill="#6d62e8"/><circle cx="17" cy="9"  r="3" fill="#6d62e8" opacity=".6"/>
                <circle cx="79" cy="9"  r="4" fill="#6d62e8"/><circle cx="71" cy="9"  r="3" fill="#6d62e8" opacity=".6"/>
                <circle cx="9"  cy="45" r="4" fill="#6d62e8"/><circle cx="17" cy="45" r="3" fill="#6d62e8" opacity=".6"/>
                <circle cx="79" cy="45" r="4" fill="#6d62e8"/><circle cx="71" cy="45" r="3" fill="#6d62e8" opacity=".6"/>
            </svg>',
        ],
        'zigzag'  => [
            'label' => 'Zigzag',
            'desc'  => 'แถวเดียว สลับบน/ล่าง title',
            'svg'   => '<svg width="88" height="54" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="88" height="54" rx="5" fill="#f1f0fd"/>
                <rect x="8" y="21" width="72" height="12" rx="3" fill="#e0dffb" stroke="#c7c4f8" stroke-width="0.5" stroke-dasharray="3 2"/>
                <circle cx="10" cy="13" r="4" fill="#6d62e8"/>
                <circle cx="26" cy="38" r="4" fill="#6d62e8"/>
                <circle cx="42" cy="13" r="4" fill="#6d62e8"/>
                <circle cx="58" cy="38" r="4" fill="#6d62e8"/>
                <circle cx="74" cy="13" r="4" fill="#6d62e8"/>
                <circle cx="84" cy="38" r="3" fill="#6d62e8" opacity=".6"/>
            </svg>',
        ],
    ];
}

// ─── Menu Registration ────────────────────────────────────────────────────────

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

// ─── Settings Registration ────────────────────────────────────────────────────

function fh_register_settings() {
    register_setting( 'fh_options_group', 'fh_title', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ] );
    register_setting( 'fh_options_group', 'fh_subtitle', [
        'sanitize_callback' => 'wp_kses_post',
        'default'           => '',
    ] );
    register_setting( 'fh_options_group', 'fh_layout', [
        'sanitize_callback' => 'sanitize_key',
        'default'           => 'frame',
    ] );
}
add_action( 'admin_init', 'fh_register_settings' );

// ─── Settings Page Render ─────────────────────────────────────────────────────

function fh_render_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $saved   = false;
    $layouts = fh_get_layouts();

    if (
        isset( $_POST['fh_options_nonce'] )
        && wp_verify_nonce( $_POST['fh_options_nonce'], 'fh_save_options' )
    ) {
        update_option( 'fh_title',    sanitize_text_field( $_POST['fh_title'] ?? '' ) );
        update_option( 'fh_subtitle', wp_kses_post( $_POST['fh_subtitle'] ?? '' ) );
        $posted_layout = sanitize_key( $_POST['fh_layout'] ?? 'frame' );
        update_option( 'fh_layout',   array_key_exists( $posted_layout, $layouts ) ? $posted_layout : 'frame' );
        $saved = true;
    }

    $title    = get_option( 'fh_title', '' );
    $subtitle = get_option( 'fh_subtitle', '' );
    $layout   = get_option( 'fh_layout', 'frame' );

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
                        <label for="fh_title"><?php esc_html_e( 'Title', 'floating-header' ); ?></label>
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

                <tr>
                    <th scope="row">
                        <label><?php esc_html_e( 'Logo Layout', 'floating-header' ); ?></label>
                        <p class="description" style="font-weight:400;margin-top:6px;">
                            <?php esc_html_e( 'เลือก pattern การวาง logo รอบ title', 'floating-header' ); ?>
                        </p>
                    </th>
                    <td>
                        <div class="fh-layout-grid">
                            <?php foreach ( $layouts as $key => $info ) : ?>
                                <label class="fh-layout-card <?php echo $layout === $key ? 'is-selected' : ''; ?>">
                                    <input
                                        type="radio"
                                        name="fh_layout"
                                        value="<?php echo esc_attr( $key ); ?>"
                                        <?php checked( $layout, $key ); ?>
                                        data-layout="<?php echo esc_attr( $key ); ?>"
                                    >
                                    <?php echo $info['svg']; ?>
                                    <strong><?php echo esc_html( $info['label'] ); ?></strong>
                                    <span><?php echo esc_html( $info['desc'] ); ?></span>
                                    <?php if ( $key === 'frame' ) : ?>
                                        <span class="fh-badge-recommend"><?php esc_html_e( 'แนะนำ', 'floating-header' ); ?></span>
                                    <?php endif; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>

            </table>
            <?php submit_button( __( 'Save Settings', 'floating-header' ) ); ?>
        </form>

        <!-- ── Shortcode Preview ──────────────────────────────────────── -->
        <div class="fh-shortcode-preview">
            <div class="fh-shortcode-preview__label">
                <?php esc_html_e( 'Shortcode ของคุณ', 'floating-header' ); ?>
            </div>
            <p class="fh-shortcode-preview__hint">
                <?php esc_html_e( 'Copy shortcode นี้ไปวางใน Elementor → Shortcode widget, Gutenberg, หรือ Classic Editor', 'floating-header' ); ?>
            </p>
            <div class="fh-shortcode-box">
                <code id="fh-shortcode-output">[floating_header layout="<?php echo esc_attr( $layout ); ?>"]</code>
                <button type="button" id="fh-copy-btn" class="fh-copy-btn">
                    <?php esc_html_e( 'Copy', 'floating-header' ); ?>
                </button>
            </div>
            <p class="fh-shortcode-preview__note">
                <?php esc_html_e( '* shortcode อัปเดตทันทีเมื่อเลือก layout ด้านบน กด Save เพื่อบันทึกการตั้งค่า', 'floating-header' ); ?>
            </p>
        </div>

    </div>

    <script>
    (function () {
        // Live preview: เปลี่ยน shortcode output ทันทีเมื่อเลือก layout
        var radios  = document.querySelectorAll('input[name="fh_layout"]');
        var output  = document.getElementById('fh-shortcode-output');
        var cards   = document.querySelectorAll('.fh-layout-card');
        var copyBtn = document.getElementById('fh-copy-btn');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                output.textContent = '[floating_header layout="' + this.value + '"]';
                cards.forEach(function (c) { c.classList.remove('is-selected'); });
                this.closest('.fh-layout-card').classList.add('is-selected');
            });
        });

        copyBtn.addEventListener('click', function () {
            var text = output.textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function () {
                    copyBtn.textContent = '✓ Copied!';
                    copyBtn.classList.add('is-copied');
                    setTimeout(function () {
                        copyBtn.textContent = 'Copy';
                        copyBtn.classList.remove('is-copied');
                    }, 2000);
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                copyBtn.textContent = '✓ Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy'; }, 2000);
            }
        });
    })();
    </script>
    <?php
}

// ─── Help Page ───────────────────────────────────────────────────────────────

function fh_register_help_page() {
    add_submenu_page(
        'edit.php?post_type=fh_logo',
        __( 'คู่มือการใช้งาน', 'floating-header' ),
        __( '📖 คู่มือ', 'floating-header' ),
        'edit_posts',
        'floating-header-help',
        'fh_render_help_page'
    );
}
add_action( 'admin_menu', 'fh_register_help_page' );

function fh_render_help_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    wp_enqueue_style( 'fh-admin-css', FH_ASSETS . 'admin.css', [], FH_VERSION );
    ?>
    <div class="wrap fh-help-wrap">
    <style>
        .fh-help-wrap { max-width: 860px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .fh-help-header { background: #1a1a2e; color: #fff; padding: 32px 36px; border-radius: 10px; margin-bottom: 24px; position: relative; overflow: hidden; }
        .fh-help-header::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse 60% 120% at 50% 130%, #4f46e540, transparent); }
        .fh-help-header h1 { font-size: 24px; margin: 0 0 6px; position: relative; }
        .fh-help-header p { color: #a5b4fc; margin: 0; position: relative; font-size: 14px; }
        .fh-help-badge { display: inline-block; background: #4f46e5; color: #fff; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: 3px 10px; border-radius: 20px; margin-bottom: 12px; position: relative; }
        .fh-section { background: #fff; border: 1px solid #e5e7f0; border-radius: 10px; padding: 28px 32px; margin-bottom: 16px; }
        .fh-section h2 { display: flex; align-items: center; gap: 10px; font-size: 17px; margin: 0 0 18px; padding-bottom: 14px; border-bottom: 1px solid #e5e7f0; }
        .fh-num { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; background: #4f46e5; color: #fff; font-size: 12px; font-weight: 700; border-radius: 6px; flex-shrink: 0; font-family: monospace; }
        .fh-steps { list-style: none; margin: 0; padding: 0; counter-reset: s; }
        .fh-steps li { counter-increment: s; display: grid; grid-template-columns: 28px 1fr; gap: 0 12px; padding: 12px 0; border-bottom: 1px dashed #e5e7f0; font-size: 14px; }
        .fh-steps li:last-child { border-bottom: none; }
        .fh-steps li::before { content: counter(s); display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #eef2ff; color: #4f46e5; font-size: 12px; font-weight: 700; border-radius: 50%; margin-top: 1px; }
        .fh-steps li strong { display: block; margin-bottom: 3px; }
        .fh-steps li span { color: #5a5a7a; line-height: 1.5; }
        code { font-family: monospace; font-size: 13px; background: #eef2ff; color: #4f46e5; padding: 2px 7px; border-radius: 4px; }
        .fh-code { background: #1a1a2e; color: #a5b4fc; font-family: monospace; font-size: 15px; padding: 16px 20px; border-radius: 8px; margin: 12px 0; letter-spacing: .02em; }
        .fh-tip { display: flex; gap: 12px; padding: 14px 18px; border-radius: 8px; margin: 14px 0; font-size: 14px; line-height: 1.6; }
        .fh-tip.green { background: #ecfdf5; border: 1px solid #a7f3d0; }
        .fh-tip.blue { background: #eef2ff; border: 1px solid #c7d2fe; }
        .fh-tip.yellow { background: #fffbeb; border: 1px solid #fcd34d; }
        .fh-tip-icon { font-size: 16px; flex-shrink: 0; margin-top: 2px; }
        .fh-tip p { margin: 0; }
        .fh-table { width: 100%; border-collapse: collapse; font-size: 14px; margin: 14px 0; }
        .fh-table th { background: #eef2ff; color: #4f46e5; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; padding: 9px 12px; text-align: left; border-bottom: 2px solid #c7d2fe; }
        .fh-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7f0; color: #2d2d4e; vertical-align: top; }
        .fh-table tr:last-child td { border-bottom: none; }
        .fh-toc { background: #f8f9ff; border: 1px solid #e5e7f0; border-left: 4px solid #4f46e5; border-radius: 10px; padding: 22px 28px; margin-bottom: 20px; }
        .fh-toc-title { font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #4f46e5; margin-bottom: 12px; }
        .fh-toc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 24px; list-style: none; margin: 0; padding: 0; counter-reset: toc; }
        .fh-toc-grid li { counter-increment: toc; display: flex; gap: 8px; font-size: 14px; align-items: baseline; }
        .fh-toc-grid li::before { content: counter(toc, decimal-leading-zero); font-family: monospace; font-size: 11px; color: #c7d2fe; flex-shrink: 0; }
        .fh-toc-grid a { color: #1a1a2e; text-decoration: none; }
        .fh-toc-grid a:hover { color: #4f46e5; }
    </style>

        <div class="fh-help-header">
            <div class="fh-help-badge">WordPress Plugin</div>
            <h1>Floating Header <span style="background:#312e81;color:#c7d2fe;font-family:monospace;font-size:13px;padding:2px 10px;border-radius:4px;margin-left:8px;">v<?php echo esc_html( FH_VERSION ); ?></span></h1>
            <p>คู่มือการติดตั้งและใช้งานสำหรับ Admin</p>
        </div>

        <nav class="fh-toc">
            <div class="fh-toc-title">สารบัญ</div>
            <ul class="fh-toc-grid">
                <li><a href="#fh-s1">โครงสร้าง Plugin</a></li>
                <li><a href="#fh-s2">การติดตั้ง</a></li>
                <li><a href="#fh-s3">เพิ่ม Logo รูปภาพ</a></li>
                <li><a href="#fh-s4">จัดเรียงลำดับ Logo</a></li>
                <li><a href="#fh-s5">ตั้งค่า Title &amp; Subtitle</a></li>
                <li><a href="#fh-s6">เลือก Layout Pattern</a></li>
                <li><a href="#fh-s7">ใช้งานใน Elementor</a></li>
                <li><a href="#fh-s8">Troubleshooting</a></li>
            </ul>
        </nav>

        <div class="fh-section" id="fh-s1">
            <h2><span class="fh-num">1</span> โครงสร้าง Plugin</h2>
            <div class="fh-code">
floating-header/<br>
├── floating-header.php<br>
├── includes/<br>
│ &nbsp;&nbsp;├── cpt.php<br>
│ &nbsp;&nbsp;├── options-page.php<br>
│ &nbsp;&nbsp;└── shortcode.php<br>
└── assets/<br>
&nbsp;&nbsp;&nbsp;&nbsp;├── style.css<br>
&nbsp;&nbsp;&nbsp;&nbsp;└── admin.css
            </div>
        </div>

        <div class="fh-section" id="fh-s2">
            <h2><span class="fh-num">2</span> การติดตั้ง</h2>
            <ul class="fh-steps">
                <li><strong>Upload ไฟล์</strong><span>วาง folder <code>floating-header</code> ที่ <code>wp-content/plugins/floating-header/</code></span></li>
                <li><strong>Activate</strong><span>Plugins → หา Floating Header → Activate</span></li>
                <li><strong>ตรวจสอบเมนู</strong><span>จะมีเมนู <strong>Floating Logos</strong> พร้อม submenu <strong>Header Settings</strong> และ <strong>📖 คู่มือ</strong></span></li>
            </ul>
        </div>

        <div class="fh-section" id="fh-s3">
            <h2><span class="fh-num">3</span> เพิ่ม Logo รูปภาพ</h2>
            <ul class="fh-steps">
                <li><strong>Floating Logos → Add New</strong><span>คลิกเมนู แล้วกด Add New Logo</span></li>
                <li><strong>ตั้งชื่อ (Title)</strong><span>ใส่ชื่อ logo — ใช้เป็น alt text ถ้าไม่ได้ตั้งแยก</span></li>
                <li><strong>Set Featured Image</strong><span>คลิก <strong>Set Featured Image</strong> ด้านขวา → เลือกรูปจาก Media Library</span></li>
                <li><strong>Publish</strong><span><code>publish</code> = แสดง, <code>draft</code> = ซ่อน</span></li>
            </ul>
            <div class="fh-tip yellow"><span class="fh-tip-icon">⚠️</span><p>Logo ที่<strong>ไม่มี Featured Image</strong> จะถูก skip อัตโนมัติ</p></div>
        </div>

        <div class="fh-section" id="fh-s4">
            <h2><span class="fh-num">4</span> จัดเรียงลำดับ Logo</h2>
            <ul class="fh-steps">
                <li><strong>ไปที่ Floating Logos (หน้า list)</strong><span>คลิกเมนู Floating Logos</span></li>
                <li><strong>ลาก icon ⠿</strong><span>คลิกค้างที่คอลัมน์ Image แล้วลาก</span></li>
                <li><strong>ปล่อยเพื่อบันทึก</strong><span>บันทึก AJAX อัตโนมัติ ไม่ต้องกด Save</span></li>
            </ul>
        </div>

        <div class="fh-section" id="fh-s5">
            <h2><span class="fh-num">5</span> ตั้งค่า Title &amp; Subtitle</h2>
            <p style="font-size:14px;color:#5a5a7a;margin-bottom:14px;">ไปที่ <strong>Floating Logos → Header Settings</strong></p>
            <table class="fh-table">
                <thead><tr><th>Field</th><th>ประเภท</th><th>คำอธิบาย</th></tr></thead>
                <tbody>
                    <tr><td><code>fh_title</code></td><td>Text</td><td>Title หลัก แสดงเป็น <code>&lt;h1&gt;</code> — ปล่อยว่างไม่แสดง</td></tr>
                    <tr><td><code>fh_subtitle</code></td><td>HTML</td><td>Subtitle รองรับ HTML ผ่าน TinyMCE</td></tr>
                </tbody>
            </table>
        </div>

        <div class="fh-section" id="fh-s6">
            <h2><span class="fh-num">6</span> เลือก Layout Pattern</h2>
            <p style="font-size:14px;color:#5a5a7a;margin-bottom:14px;">เลือกได้ที่ <strong>Header Settings → Logo Layout</strong> มี 4 แบบ</p>
            <table class="fh-table">
                <thead><tr><th>Pattern</th><th>Shortcode attribute</th><th>เหมาะกับ</th></tr></thead>
                <tbody>
                    <tr><td><strong>Frame</strong> ✦ แนะนำ</td><td><code>layout="frame"</code></td><td>ทุกกรณี กระจายรอบขอบ 4 ด้าน</td></tr>
                    <tr><td>Left / Right</td><td><code>layout="lr"</code></td><td>Logo 4–10 ตัว สมมาตรซ้าย-ขวา</td></tr>
                    <tr><td>Top / Bottom</td><td><code>layout="tb"</code></td><td>Hero banner แนวนอน</td></tr>
                    <tr><td>Corners</td><td><code>layout="corners"</code></td><td>Logo 13+ ตัว dynamic</td></tr>
                    <tr><td>Zigzag</td><td><code>layout="zigzag"</code></td><td>แถวเดียว สลับบน/ล่าง title</td></tr>
                </tbody>
            </table>
            <div class="fh-tip blue"><span class="fh-tip-icon">💡</span><p>หลัง Save จะได้ shortcode พร้อม attribute ที่ถูกต้องให้ copy ทันที ดูได้ที่ด้านล่าง form</p></div>
        </div>

        <div class="fh-section" id="fh-s7">
            <h2><span class="fh-num">7</span> ใช้งานใน Elementor</h2>
            <ul class="fh-steps">
                <li><strong>เปิด Elementor Editor</strong><span>Edit ด้วย Elementor บน Page ที่ต้องการ</span></li>
                <li><strong>เพิ่ม Widget "Shortcode"</strong><span>ค้นหา Shortcode widget แล้วลากวาง</span></li>
                <li><strong>Paste shortcode จาก Header Settings</strong><span>Copy จาก section "Shortcode ของคุณ" แล้ว paste</span></li>
                <li><strong>Update / Publish</strong><span>logo จะลอยขึ้น-ลงพร้อม title</span></li>
            </ul>
            <div class="fh-tip blue"><span class="fh-tip-icon">ℹ️</span><p>ถ้าต้องการใช้หลาย layout ในหน้าเดียวกัน ใส่ attribute ได้โดยตรง เช่น <code>[floating_header layout="lr"]</code></p></div>
        </div>

        <div class="fh-section" id="fh-s8">
            <h2><span class="fh-num">8</span> Troubleshooting</h2>
            <table class="fh-table">
                <thead><tr><th>ปัญหา</th><th>สาเหตุ</th><th>วิธีแก้</th></tr></thead>
                <tbody>
                    <tr><td>Logo ไม่แสดง</td><td>ไม่มี Featured Image หรือ status ไม่ใช่ publish</td><td>Edit post → Set Featured Image → Update</td></tr>
                    <tr><td>มีแค่ Title ไม่มีรูป</td><td>data-logo-count="0" ใน HTML</td><td>ตรวจสอบ Featured Image ทุก logo</td></tr>
                    <tr><td>Layout ไม่เปลี่ยน</td><td>ยังไม่ได้ Save Settings</td><td>กด Save Settings ใน Header Settings</td></tr>
                    <tr><td>CSS ไม่โหลด</td><td>ไฟล์ assets/style.css หาย</td><td>Re-upload plugin ให้ครบ</td></tr>
                </tbody>
            </table>
        </div>

    </div>
    <?php
}
