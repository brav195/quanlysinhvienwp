# 🎓 Student Manager – WordPress Plugin

> Plugin quản lý sinh viên cho WordPress  
> **Thực hành ngày: 24/04/2026 | Lớp: N1 + N2**

---

## 📋 Mô tả

**Student Manager** là một WordPress plugin cho phép quản lý thông tin sinh viên ngay trong trang quản trị WordPress, bao gồm:

- Đăng ký **Custom Post Type** "Sinh Viên"  
- **Custom Meta Boxes** với các trường MSSV, Lớp/Chuyên ngành, Ngày sinh  
- **Shortcode** `[danh_sach_sinh_vien]` hiển thị danh sách dưới dạng bảng HTML có style đẹp

---

## 📁 Cấu trúc thư mục

```
student-manager/
├── student-manager.php              ← File chính, plugin header
├── includes/
│   ├── class-sm-post-type.php       ← Đăng ký Custom Post Type
│   ├── class-sm-meta-box.php        ← Meta Box + lưu dữ liệu an toàn
│   └── class-sm-shortcode.php       ← Shortcode [danh_sach_sinh_vien]
├── assets/
│   └── style.css                    ← CSS cho bảng hiển thị frontend
└── README.md                        ← Tài liệu này
```

---

## ⚙️ Cài đặt

1. Tải file `.zip` về máy  
2. Vào **WordPress Admin → Plugins → Add New → Upload Plugin**  
3. Chọn file `student-manager.zip` và nhấn **Install Now**  
4. Kích hoạt plugin (**Activate**)

---

## 🚀 Hướng dẫn sử dụng

### 1. Thêm sinh viên

Vào **WordPress Admin → Sinh Viên → Thêm Mới**

Điền các thông tin:
| Trường | Mô tả |
|--------|-------|
| Tiêu đề (Title) | Họ và tên sinh viên |
| Nội dung (Editor) | Tiểu sử / Ghi chú |
| Mã số sinh viên (MSSV) | Ví dụ: `SV2024001` |
| Lớp / Chuyên ngành | Chọn từ dropdown |
| Ngày sinh | Chọn ngày từ date picker |

### 2. Hiển thị danh sách

Tạo hoặc chỉnh sửa một **Page**, chèn shortcode sau vào nội dung:

```
[danh_sach_sinh_vien]
```

**Tùy chọn mở rộng (optional):**

```
[danh_sach_sinh_vien so_luong="10" sap_xep="DESC"]
```

| Tham số | Giá trị mặc định | Mô tả |
|---------|-----------------|-------|
| `so_luong` | `-1` (tất cả) | Số lượng sinh viên hiển thị |
| `sap_xep` | `ASC` | Thứ tự sắp xếp: `ASC` hoặc `DESC` |

---

## 🔒 Bảo mật

Plugin áp dụng đầy đủ các tiêu chuẩn bảo mật WordPress:

| Biện pháp | Mô tả |
|-----------|-------|
| **Nonce** | Xác thực form khi lưu meta box (`wp_nonce_field` + `wp_verify_nonce`) |
| **Capability Check** | Kiểm tra quyền `edit_post` trước khi lưu |
| **Sanitize** | `sanitize_text_field()`, `wp_unslash()` cho text |
| **Whitelist Validation** | Trường Lớp chỉ chấp nhận giá trị trong danh sách định sẵn |
| **Date Validation** | Kiểm tra định dạng `YYYY-MM-DD` hợp lệ qua `DateTime::createFromFormat` |
| **Autosave Guard** | Bỏ qua lưu khi WordPress đang autosave |
| **Direct Access Block** | `if (!defined('ABSPATH')) exit;` ở đầu mỗi file |
| **Escape Output** | `esc_html()`, `esc_attr()` toàn bộ output HTML |

---

## 🎨 Danh sách Chuyên ngành

| Key | Tên hiển thị |
|-----|-------------|
| `CNTT` | Công nghệ thông tin |
| `KTPM` | Kỹ thuật phần mềm |
| `KHMT` | Khoa học máy tính |
| `ATTT` | An toàn thông tin |
| `Kinh_te` | Kinh tế |
| `QTKD` | Quản trị kinh doanh |
| `Marketing` | Marketing |
| `Ke_toan` | Kế toán - Kiểm toán |
| `Luat` | Luật |
| `Ngoai_ngu` | Ngoại ngữ |

---


### Màn hình Frontend – Shortcode [danh_sach_sinh_vien]

```

> 📷 **Lưu ý:** Thay thế các ảnh ASCII art trên bằng ảnh chụp màn hình thực tế  
> sau khi cài đặt plugin vào WordPress.

---

## 🛠️ Yêu cầu hệ thống

- WordPress **5.0+** (hỗ trợ Gutenberg)
- PHP **7.4+**
- MySQL **5.6+**

---

## 👨‍💻 Thông tin tác giả

| | |
|---|---|
| **Lớp** | N1 / N2 |
| **Ngày thực hành** | 24/04/2026 |
| **Plugin version** | 1.0.0 |
| **License** | GPL-2.0+ |

---

## 📄 License

This plugin is licensed under the [GPL-2.0+](https://www.gnu.org/licenses/gpl-2.0.txt).
