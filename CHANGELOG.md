# CHANGELOG — Floating Header Plugin
> ห้ามเขียนทับ — เพิ่มรายการใหม่ด้านบนเสมอ (newest first)

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
