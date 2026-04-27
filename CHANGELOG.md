# CHANGELOG — Floating Header Plugin
> ห้ามเขียนทับ — เพิ่มรายการใหม่ด้านบนเสมอ (newest first)

---

## [1.0.0-part4] — 2026-04-27
### Added — Part 4: Shortcode + Layout Logic
- `includes/shortcode.php` v1.0.0 — สร้างใหม่ทั้งไฟล์
- File guard ครบ: `ABSPATH` check + `FH_VERSION` check ป้องกัน direct access และ orphan include
- `add_shortcode( 'floating_header', 'fh_render_shortcode' )` — Register shortcode
- `fh_render_shortcode()` — Enqueue `style.css` ผ่าน `FH_ASSETS`, query CPT `fh_logo` (publish, menu_order ASC), return `ob_get_clean()`
- Query options: `get_option('fh_title')` + `get_option('fh_subtitle')` พร้อม default empty string
- Logo loop: ข้าม post ที่ไม่มี thumbnail หรือ URL ไม่ได้ (guard `continue`)
- Float direction: index คี่ → `fh-float-up`, index คู่ → `fh-float-down`
- Animation: `--fh-delay` = `$index * 0.5s`, `--fh-duration` = `3 + ($index % 3 * 0.5)s`
- Alt text: ดึง `_wp_attachment_image_alt` meta → fallback `get_the_title()` ของ logo post
- Tier CSS class: `fh-tier-3` (7–12 logos), `fh-tier-4` (13+ logos) บน `.fh-wrapper`
- `data-logo-count` attribute บน `.fh-wrapper` สำหรับ CSS tier targeting
- `fh_calc_logo_position( $index, $total )` — คืน array `['x' => %, 'y' => %]`
  - Tier 1 (1–3): predefined grid 1 ชั้น horizontal center (15/50/85%)
  - Tier 2 (4–6): 2 rows × 3 cols, x=[15,50,85], y=[30,70]
  - Tier 3 (7–12): 3 rows × 4 cols + offset ±4% X / ±5% Y สลับตาม index
  - Tier 4 (13+): deterministic scatter ด้วย `($index * 37+13) % 78 + 8` / `($index * 53+7) % 78 + 8`
- Output HTML ตรงตาม Master spec: `fh-wrapper > fh-logo-layer + fh-title-layer`
- Title: `<h1 class="fh-title">` แสดงเมื่อ option ไม่ว่าง
- Subtitle: `<div class="fh-subtitle">` ผ่าน `wp_kses_post()` แสดงเมื่อ option ไม่ว่าง

---

## [1.0.0-part3] — 2026-04-27
### Added — Part 3: CSS Animation + Admin Styles
- `assets/style.css` v1.0.0 — สร้างใหม่ทั้งไฟล์
- `assets/admin.css` v1.0.0 — สร้างใหม่ทั้งไฟล์
- `.fh-wrapper` — position: relative, min-height 400px, overflow: hidden
- `.fh-logo-layer` — position: absolute inset:0, z-index: 1, pointer-events: none
- `.fh-logo` — position: absolute ใช้ CSS custom properties `--fh-x` / `--fh-y` จาก PHP
- `@keyframes fh-float-up` — translateY 0 → -20px → 0 (infinite ease-in-out)
- `@keyframes fh-float-down` — translateY 0 → +20px → 0 (infinite ease-in-out)
- `.fh-float-up` / `.fh-float-down` — ใช้ `--fh-duration` + `--fh-delay` custom props
- `@media (prefers-reduced-motion: reduce)` — ปิด animation อัตโนมัติ
- `.fh-title-layer` — position: absolute, top/left 50%, transform translate(-50%,-50%), z-index: 10
- Layout tiers: `data-logo-count` attribute (1-3, 4-6) + `.fh-tier-3` / `.fh-tier-4` class
- Tier 3 (7–12 logos): even items opacity 0.75
- Tier 4 (13+ logos): items n+7 opacity 0.5
- Responsive breakpoints: 768px (min-height 280px, translateY ±12px), 480px (min-height 220px)
- `assets/admin.css` — `.column-fh_thumbnail` width 80px, img 60×60 object-fit cover
- jQuery UI Sortable styles: grab cursor, placeholder dashed blue, helper box-shadow
- Drag handle hint `⠿` แสดงบน thumbnail column เมื่อ hover row
- Options page `.fh-options-wrap` max-width 780px, form-table th width 180px
- `.fh-notice-saved` — admin notice border-left green

---

## [1.0.0-part2] — 2026-04-27
### Added — Part 2: Options Page
- `includes/options-page.php` v1.0.0 — สร้างใหม่ทั้งไฟล์
- File guard ครบ: `ABSPATH` check + `FH_VERSION` check ป้องกัน direct access และ orphan include
- `fh_register_options_page()` — เพิ่ม Submenu "Header Settings" ใต้ CPT `fh_logo` ผ่าน `add_submenu_page()`
- `fh_register_settings()` — Register `fh_title` (sanitize: `sanitize_text_field`) และ `fh_subtitle` (sanitize: `wp_kses_post`) ผ่าน Settings API
- `fh_render_options_page()` — Render form พร้อม:
  - `current_user_can( 'manage_options' )` check
  - Nonce `fh_save_options` / `fh_options_nonce` สำหรับ POST handling
  - Title field: `<input type="text">` class `regular-text`
  - Subtitle field: `wp_editor()` TinyMCE (`teeny=true`, `media_buttons=false`, 8 rows)
  - Save logic ใน render function เอง (ไม่ใช้ `options.php` action) เพื่อรองรับ TinyMCE POST
  - Admin notice "Settings saved." หลัง save สำเร็จ

---

## [1.0.0-part1] — 2026-04-27
### Added — Part 1: Plugin Bootstrap + CPT
- `floating-header.php` v1.0.0 — Plugin bootstrap พร้อม constants: FH_VERSION, FH_DIR, FH_URL, FH_INCLUDES, FH_ASSETS
- `floating-header.php` — File guard loop ตรวจสอบ includes ทุกไฟล์ก่อน require; แสดง admin notice รายชื่อไฟล์ที่หายถ้าไม่ครบ
- `includes/cpt.php` v1.0.0 — Register CPT `fh_logo` (supports: title, thumbnail, page-attributes)
- `includes/cpt.php` — Default ordering ด้วย `menu_order ASC` บน list screen
- `includes/cpt.php` — AJAX endpoint `fh_save_sort_order` พร้อม nonce + capability check
- `includes/cpt.php` — jQuery UI Sortable drag-sort บน list table (inline script, ไม่ใช้ไฟล์แยก)
- `includes/cpt.php` — Custom column แสดง Thumbnail (60×60) บน list screen
- Guard ทุกไฟล์: `ABSPATH` check + `FH_VERSION` check ป้องกัน direct access และการ include แบบ orphan

---

## [1.0.0] — 2026-04-27
### Added
- วางแผน Master Architecture เสร็จสมบูรณ์
- กำหนด File Structure: 6 ไฟล์หลัก
- กำหนด CPT spec: `fh_logo`, Featured Image, menu_order
- กำหนด Options Page spec: `fh_title`, `fh_subtitle` (TinyMCE)
- กำหนด Shortcode: `[floating_header]` ไม่มี attributes
- กำหนด Layout Auto-Calculation Logic (1–3 / 4–6 / 7–12 / 13+)
- กำหนด CSS Animation spec: floatUp / floatDown keyframes
- กำหนด Z-index layers: logo=1, title=10
- แบ่งงานเป็น 5 Parts สำหรับแต่ละ Chat
