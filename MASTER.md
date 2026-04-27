# MASTER ARCHITECTURE — Floating Header Plugin
> Version: 1.0.0 | Last Updated: 2026-04-27 | Status: Planning

---

## ⚠️ IMPORTANT RULES — อ่านก่อนทำทุกครั้ง!

### กฎสำหรับการพัฒนา
1. ✅ **อ่าน Master นี้ก่อนเริ่มทำงานทุกครั้ง**
2. ✅ **ใช้ชื่อไฟล์ / class / function ตาม Master เท่านั้น**
3. ✅ **เพิ่ม version header ทุกครั้งที่แก้ไฟล์**
4. ✅ **สรุป changelog หลังแก้เสร็จ และอัปเดตไฟล์ CHANGELOG.md (ห้ามเขียนทับ)**
5. ✅ **ถาม user ถ้าไม่แน่ใจเรื่อง version**
6. ✅ **🔴 กฎ Version Control (สำคัญมาก):**
   - **ก่อนแก้ไขไฟล์ใดๆ** → บอก user ว่าต้องการไฟล์ไหน → รอ user ส่งเวอร์ชันล่าสุดมาก่อน
   - **ห้ามอ้างอิงไฟล์จาก context/memory** ของ Claude เพราะอาจเป็นเวอร์ชันเก่า
   - **ถ้าสร้างไฟล์ใหม่ทั้งหมด** → ไม่ต้องขอ (ไม่มี version conflict)
   - **ถ้าแก้ไขไฟล์ที่มีอยู่** → ต้องขอเวอร์ชันล่าสุดจาก user ก่อนเสมอ
   - เหตุผล: หลาย Chat ทำงานแยกกัน → ไฟล์อาจถูกแก้ใน Chat อื่นแล้ว → Claude ไม่รู้

### เมื่อเริ่ม Chat ใหม่
```
1. บอก Claude: "อ่าน Master Architecture ก่อน"
2. ระบุว่าจะทำงานไฟล์ไหน (ดู Section 9: Chat Splitting Guide)
3. ตรวจสอบ version ปัจจุบันจาก Master
4. 🔴 ถ้าจะแก้ไขไฟล์ที่มีอยู่ → บอก user ว่าต้องการไฟล์ไหน → รอรับก่อนเริ่ม
5. จบ Chat → สรุป changelog สำหรับอัปเดต Master
```

---

## 1. Project Overview

**Plugin Name:** Floating Header  
**Plugin Slug:** `floating-header`  
**Text Domain:** `floating-header`  
**Requires:** WordPress 5.8+, Elementor (for shortcode widget)  
**Tested up to:** WordPress 6.5  
**License:** GPLv2  

### สิ่งที่ Plugin ทำ
- Admin อัพโหลด Logo รูปภาพหลายรูปผ่าน Custom Post Type
- Frontend แสดงรูปลอยขึ้น-ลง (float animation) เป็น background
- Title + Subtitle แสดงตรงกลาง ด้านหน้ารูปทั้งหมด
- ใช้งานผ่าน Shortcode `[floating_header]` ใน Elementor

---

## 2. File Structure

```
wp-content/plugins/floating-header/
│
├── floating-header.php          v1.0.0  ← Plugin bootstrap
│
├── includes/
│   ├── cpt.php                  v1.0.0  ← Register CPT + drag sort
│   ├── options-page.php         v1.0.0  ← Settings: Title + Subtitle (TinyMCE)
│   └── shortcode.php            v1.0.0  ← Render logic + layout calc
│
└── assets/
    ├── style.css                v1.0.0  ← Float animation + layout CSS
    └── admin.css                v1.0.0  ← Admin panel styling (optional)
```

---

## 3. Custom Post Type Spec

**Post Type Key:** `fh_logo`  
**Menu Label:** `Floating Logos`  
**Menu Icon:** `dashicons-format-gallery`  
**Supports:** `title`, `thumbnail`  
**Order Field:** `menu_order` (drag-to-sort)  
**Status:** `publish` = แสดง, `draft` = ซ่อน  

### Meta / Fields
| Field | Storage | หมายเหตุ |
|---|---|---|
| Logo Image | `_thumbnail_id` (Featured Image) | WP built-in |
| Display Order | `menu_order` | ลาก sort ได้ |
| Active/Inactive | `post_status` | publish/draft |

---

## 4. Options Page Spec

**Menu Slug:** `floating-header-settings`  
**Menu Position:** Under `Floating Logos` CPT  
**Menu Label:** `Header Settings`  

### Options Keys
| Key | Type | Field | หมายเหตุ |
|---|---|---|---|
| `fh_title` | string | `<input type="text">` | Title หลัก |
| `fh_subtitle` | string | `wp_editor()` TinyMCE | Subtitle (HTML allowed) |

---

## 5. Shortcode Spec

**Shortcode:** `[floating_header]`  
**No attributes needed** — ดึงข้อมูลจาก Options + CPT อัตโนมัติ  

### Render Output Structure
```html
<section class="fh-wrapper">
  <div class="fh-logo-layer">
    <div class="fh-logo fh-float-up"   style="--fh-x:15%; --fh-y:20%; --fh-delay:0s; --fh-duration:3s">
      <img src="..." alt="...">
    </div>
    <div class="fh-logo fh-float-down" style="--fh-x:70%; --fh-y:40%; --fh-delay:0.5s; --fh-duration:4s">
      <img src="..." alt="...">
    </div>
    <!-- ... more logos ... -->
  </div>
  <div class="fh-title-layer">
    <h1 class="fh-title">Title Text</h1>
    <div class="fh-subtitle">Subtitle HTML content</div>
  </div>
</section>
```

---

## 6. Layout Auto-Calculation Logic

### Float Direction (ใน shortcode.php)
```
$index คี่  (1, 3, 5...) → class="fh-float-up"
$index คู่  (2, 4, 6...) → class="fh-float-down"
```

### Position Grid Logic
| จำนวน Logo | Layout |
|---|---|
| 1–3 | กระจาย horizontal ตรงกลาง |
| 4–6 | 2 แถว, สลับ position |
| 7–12 | 3 แถว + random offset |
| 13+ | Scatter เต็ม + ลด opacity บางส่วน |

### Animation Delay Per Logo
```
--fh-delay: ($index * 0.5)s
--fh-duration: 3s + ($index % 3 * 0.5)s
```

---

## 7. CSS Animation Spec

### Keyframes
```css
@keyframes fh-float-up {
  0%, 100% { transform: translateY(0px); }
  50%       { transform: translateY(-20px); }
}

@keyframes fh-float-down {
  0%, 100% { transform: translateY(0px); }
  50%       { transform: translateY(20px); }
}
```

### Z-Index Layers
| Layer | z-index | Element |
|---|---|---|
| Logo layer | 1 | `.fh-logo-layer` |
| Title layer | 10 | `.fh-title-layer` |

### Title Position
```css
.fh-title-layer {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  z-index: 10;
}
```

---

## 8. Version Table (Current)

| File | Version | Status |
|---|---|---|
| `floating-header.php` | 1.0.0 | 🔴 Not created |
| `includes/cpt.php` | 1.0.0 | 🔴 Not created |
| `includes/options-page.php` | 1.0.0 | 🔴 Not created |
| `includes/shortcode.php` | 1.0.0 | 🔴 Not created |
| `assets/style.css` | 1.0.0 | 🔴 Not created |
| `assets/admin.css` | 1.0.0 | 🔴 Not created |

---

## 9. Chat Splitting Guide

### Part 1 — Plugin Bootstrap + CPT
**ไฟล์:** `floating-header.php`, `includes/cpt.php`  
**เป้าหมาย:** Activate plugin ได้, มีเมนู Floating Logos ใน Admin  
**Chat prompt:** "อ่าน Master Architecture ก่อน เราจะทำ Part 1: Plugin Bootstrap + CPT"

### Part 2 — Options Page
**ไฟล์:** `includes/options-page.php`  
**เป้าหมาย:** หน้า Settings มี Title input + Subtitle TinyMCE บันทึกได้  
**Chat prompt:** "อ่าน Master Architecture ก่อน เราจะทำ Part 2: Options Page"

### Part 3 — CSS Animation
**ไฟล์:** `assets/style.css`, `assets/admin.css`  
**เป้าหมาย:** Animation float up/down, layout wrapper, title center  
**Chat prompt:** "อ่าน Master Architecture ก่อน เราจะทำ Part 3: CSS Animation"

### Part 4 — Shortcode + Layout Logic
**ไฟล์:** `includes/shortcode.php`  
**เป้าหมาย:** Render HTML ครบ, layout คำนวณเอง, ดึง options ได้  
**Chat prompt:** "อ่าน Master Architecture ก่อน เราจะทำ Part 4: Shortcode + Layout Logic"

### Part 5 — Testing + Fine-tune
**ไฟล์:** ทุกไฟล์ (แก้ไข)  
**เป้าหมาย:** ทดสอบใน Elementor, ปรับ animation, responsive  
**Chat prompt:** "อ่าน Master Architecture ก่อน เราจะทำ Part 5: Testing + Fine-tune"

---

## 10. Dependencies

| ต้องการ | วิธี | ต้องติดตั้งเพิ่ม |
|---|---|---|
| Custom Post Type | `register_post_type()` | ❌ |
| Image Upload | Featured Image | ❌ |
| Text Editor | `wp_editor()` TinyMCE | ❌ |
| Options Page | `add_menu_page()` | ❌ |
| Animation | Pure CSS `@keyframes` | ❌ |
| Drag Sort | `menu_order` WP built-in | ❌ |
| Elementor | Shortcode Widget | ❌ |

**ไม่ต้องติดตั้ง plugin เพิ่มใดๆ ทั้งสิ้น**
