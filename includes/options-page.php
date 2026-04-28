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

// ─── Help Page ──────────────────────────────────────────────

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
                <li><a href="#fh-s6">ใช้งานใน Elementor</a></li>
                <li><a href="#fh-s7">ตาราง Layout Reference</a></li>
                <li><a href="#fh-s8">Troubleshooting</a></li>
            </ul>
        </nav>

        <div class="fh-section" id="fh-s1">
            <h2><span class="fh-num">1</span> โครงสร้าง Plugin</h2>
            <div class="fh-code">
floating-header/<br>
├── floating-header.php &nbsp;&nbsp;<span style="color:#6b7280">← Plugin หลัก</span><br>
├── includes/<br>
│ &nbsp;&nbsp;├── cpt.php &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280">← Custom Post Type + drag sort</span><br>
│ &nbsp;&nbsp;├── options-page.php &nbsp;<span style="color:#6b7280">← Settings + คู่มือนี้</span><br>
│ &nbsp;&nbsp;└── shortcode.php &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280">← Render + layout คำนวณเอง</span><br>
└── assets/<br>
&nbsp;&nbsp;&nbsp;&nbsp;├── style.css &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280">← Animation + responsive</span><br>
&nbsp;&nbsp;&nbsp;&nbsp;└── admin.css &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280">← Admin panel styling</span>
            </div>
            <div class="fh-tip blue">
                <span class="fh-tip-icon">ℹ️</span>
                <p>ถ้าไฟล์ใดหายไป Plugin จะ<strong>ไม่ crash</strong> แต่จะแสดง Admin Notice แจ้งชื่อไฟล์ที่ขาดแทน</p>
            </div>
        </div>

        <div class="fh-section" id="fh-s2">
            <h2><span class="fh-num">2</span> การติดตั้ง</h2>
            <ul class="fh-steps">
                <li><strong>Upload ไฟล์</strong><span>วาง folder <code>floating-header</code> ที่ <code>wp-content/plugins/floating-header/</code></span></li>
                <li><strong>Activate Plugin</strong><span>ไปที่ Plugins → หา Floating Header → กด <strong>Activate</strong></span></li>
                <li><strong>ตรวจสอบเมนู</strong><span>จะมีเมนู <strong>Floating Logos</strong> ใน sidebar พร้อม submenu <strong>Header Settings</strong> และ <strong>คู่มือ</strong></span></li>
            </ul>
            <div class="fh-tip green"><span class="fh-tip-icon">✅</span><p>ต้องการ WordPress <strong>5.8+</strong> ไม่ต้องติดตั้ง Plugin เพิ่ม ทำงานกับ Elementor ได้ทันที</p></div>
        </div>

        <div class="fh-section" id="fh-s3">
            <h2><span class="fh-num">3</span> เพิ่ม Logo รูปภาพ</h2>
            <ul class="fh-steps">
                <li><strong>Floating Logos → Add New</strong><span>คลิกเมนู Floating Logos แล้วกด Add New Logo</span></li>
                <li><strong>ตั้งชื่อ (Title)</strong><span>ใส่ชื่อ logo เช่น "Partner A" — ใช้เป็น alt text ถ้าไม่ได้ตั้งแยก</span></li>
                <li><strong>Set Featured Image</strong><span>คลิก <strong>Set Featured Image</strong> ด้านขวา → อัพโหลดหรือเลือกจาก Media Library</span></li>
                <li><strong>Publish</strong><span>กด Publish — <code>publish</code> = แสดง, <code>draft</code> = ซ่อน</span></li>
            </ul>
            <div class="fh-tip green"><span class="fh-tip-icon">🖼️</span><p><strong>แนะนำ:</strong> PNG transparent หรือ SVG ขนาด 200×100px ขึ้นไป</p></div>
            <div class="fh-tip yellow"><span class="fh-tip-icon">⚠️</span><p>Logo ที่<strong>ไม่มี Featured Image</strong> จะถูก skip อัตโนมัติ ไม่ error แต่ไม่แสดงบน Frontend</p></div>
        </div>

        <div class="fh-section" id="fh-s4">
            <h2><span class="fh-num">4</span> จัดเรียงลำดับ Logo</h2>
            <ul class="fh-steps">
                <li><strong>ไปที่ Floating Logos (หน้า list)</strong><span>คลิกเมนู Floating Logos เพื่อดูรายการ</span></li>
                <li><strong>ลาก icon ⠿</strong><span>icon สีเทาปรากฏที่คอลัมน์ Image — คลิกค้างแล้วลากขึ้น/ลง</span></li>
                <li><strong>ปล่อยเพื่อบันทึก</strong><span>บันทึกอัตโนมัติผ่าน AJAX ไม่ต้องกด Save</span></li>
            </ul>
        </div>

        <div class="fh-section" id="fh-s5">
            <h2><span class="fh-num">5</span> ตั้งค่า Title &amp; Subtitle</h2>
            <p style="font-size:14px;color:#5a5a7a;margin-bottom:14px;">ไปที่ <strong>Floating Logos → Header Settings</strong></p>
            <table class="fh-table">
                <thead><tr><th>Field</th><th>ประเภท</th><th>คำอธิบาย</th></tr></thead>
                <tbody>
                    <tr><td><code>fh_title</code></td><td>Text</td><td>Title หลัก แสดงเป็น <code>&lt;h1&gt;</code> — ปล่อยว่างไม่แสดง</td></tr>
                    <tr><td><code>fh_subtitle</code></td><td>HTML</td><td>Subtitle รองรับ HTML ผ่าน TinyMCE — ปล่อยว่างไม่แสดง</td></tr>
                </tbody>
            </table>
        </div>

        <div class="fh-section" id="fh-s6">
            <h2><span class="fh-num">6</span> ใช้งาน Shortcode ใน Elementor</h2>
            <ul class="fh-steps">
                <li><strong>เปิด Elementor Editor</strong><span>Edit ด้วย Elementor บน Page ที่ต้องการ</span></li>
                <li><strong>เพิ่ม Widget "Shortcode"</strong><span>ค้นหา widget ชื่อ <strong>Shortcode</strong> แล้วลากวางใน column</span></li>
                <li><strong>พิมพ์ shortcode</strong><span><div class="fh-code" style="margin:8px 0;">[floating_header]</div></span></li>
                <li><strong>กด Update / Publish</strong><span>logo จะลอยขึ้น-ลงพร้อม title กลางจอทันที</span></li>
            </ul>
            <div class="fh-tip blue"><span class="fh-tip-icon">💡</span><p>แนะนำใส่ใน Section ที่มี min-height ตั้งไว้ เช่น 400px เพื่อให้ logo มีพื้นที่</p></div>

            <h3 style="font-size:15px;font-weight:700;margin:20px 0 10px;">Gutenberg / Classic Editor</h3>
            <ul class="fh-steps">
                <li><strong>Gutenberg</strong><span>เพิ่ม Block → ค้นหา <strong>Shortcode</strong> → พิมพ์ <code>[floating_header]</code></span></li>
                <li><strong>Classic Editor</strong><span>พิมพ์ <code>[floating_header]</code> ลงใน Content ได้เลย</span></li>
                <li><strong>PHP Template</strong><span><code>&lt;?php echo do_shortcode('[floating_header]'); ?&gt;</code></span></li>
            </ul>
        </div>

        <div class="fh-section" id="fh-s7">
            <h2><span class="fh-num">7</span> ตาราง Layout Reference</h2>
            <table class="fh-table">
                <thead><tr><th>จำนวน Logo</th><th>Layout</th><th>Class</th></tr></thead>
                <tbody>
                    <tr><td>1 – 3</td><td>1 แถว กระจาย horizontal</td><td>—</td></tr>
                    <tr><td>4 – 6</td><td>2 แถว × 3 คอลัมน์</td><td>—</td></tr>
                    <tr><td>7 – 12</td><td>3 แถว × 4 คอลัมน์ + offset</td><td><code>fh-tier-3</code></td></tr>
                    <tr><td>13+</td><td>Scatter เต็มพื้นที่</td><td><code>fh-tier-4</code></td></tr>
                </tbody>
            </table>
            <table class="fh-table">
                <thead><tr><th>Breakpoint</th><th>min-height</th><th>Logo max-width</th><th>Float distance</th></tr></thead>
                <tbody>
                    <tr><td>Desktop (&gt;768px)</td><td>400px</td><td>120px</td><td>20px</td></tr>
                    <tr><td>Tablet (≤768px)</td><td>280px</td><td>80px</td><td>12px</td></tr>
                    <tr><td>Mobile (≤480px)</td><td>220px</td><td>56px</td><td>8px</td></tr>
                </tbody>
            </table>
        </div>

        <div class="fh-section" id="fh-s8">
            <h2><span class="fh-num">8</span> Troubleshooting</h2>
            <table class="fh-table">
                <thead><tr><th>ปัญหา</th><th>สาเหตุ</th><th>วิธีแก้</th></tr></thead>
                <tbody>
                    <tr>
                        <td>Logo ไม่แสดงบน Frontend</td>
                        <td>Post ไม่ได้ Publish หรือยังไม่ได้ตั้ง Featured Image</td>
                        <td>ตรวจสอบ status และ Featured Image ทุก logo</td>
                    </tr>
                    <tr>
                        <td>มีแค่ Title/Subtitle ไม่มีรูป</td>
                        <td>Featured Image ยังไม่ถูก Set บน fh_logo post</td>
                        <td>Floating Logos → Edit แต่ละ post → Set Featured Image</td>
                    </tr>
                    <tr>
                        <td>CSS ไม่โหลด (ไม่มี animation)</td>
                        <td>style.css ไม่อยู่ใน <code>assets/</code></td>
                        <td>ตรวจสอบว่าไฟล์ <code>assets/style.css</code> อยู่ครบ</td>
                    </tr>
                    <tr>
                        <td>Admin Notice "Missing files"</td>
                        <td>ไฟล์ใน <code>includes/</code> หาย</td>
                        <td>Re-upload plugin ให้ครบทุกไฟล์</td>
                    </tr>
                    <tr>
                        <td>Logo ซ้อนทับ Title</td>
                        <td>logo z-index (1) ต่ำกว่า title (10) แล้ว แต่ Logo ใหญ่เกินไป</td>
                        <td>ลดขนาดรูปหรือปรับ <code>max-width</code> ใน style.css</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <?php
}
