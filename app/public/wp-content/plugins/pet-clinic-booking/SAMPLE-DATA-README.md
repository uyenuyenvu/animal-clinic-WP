# Dữ liệu mẫu - Pet Clinic Booking System

## 📋 Tổng quan

Các file SQL này chứa dữ liệu mẫu để test hệ thống đặt phòng khám thú y. Dữ liệu bao gồm:

- **10 bản ghi đặt phòng** với đầy đủ thông tin theo giao diện Nhật
- **15 bác sĩ** với các chuyên khoa khác nhau
- Dữ liệu thực tế với tên tiếng Nhật và địa chỉ Nhật Bản

## 📁 Files

### 1. `sample-data.sql`

Dữ liệu mẫu cho bảng `wp_pcb_bookings` với 10 bản ghi:

| Booking ID   | Khách hàng | Thú cưng             | Chuyên khoa  | Trạng thái |
| ------------ | ---------- | -------------------- | ------------ | ---------- |
| R20250709001 | 山田太郎   | ポチ (Shiba)         | 皮膚科       | confirmed  |
| R20250709002 | 佐藤花子   | ミー (Mèo)           | 内科         | pending    |
| R20250709003 | 田中次郎   | ラッキー (Golden)    | 外科         | confirmed  |
| R20250709004 | 中村美咲   | チョコ (Mèo)         | 歯科         | pending    |
| R20250709005 | 木村健一   | ピー (Chihuahua)     | 循環器科     | confirmed  |
| R20250709006 | 高橋愛     | ソラ (Mèo)           | 腫瘍科       | pending    |
| R20250709007 | 佐々木大輔 | ハチ (Border Collie) | 神経科       | confirmed  |
| R20250709008 | 山本由美   | ルナ (Mèo)           | 産科         | pending    |
| R20250709009 | 渡辺正男   | マックス (Labrador)  | アレルギー科 | confirmed  |
| R20250709010 | 伊藤恵子   | トム (Mèo)           | 泌尿器科     | pending    |

### 2. `sample-doctors.sql`

Dữ liệu mẫu cho bảng `wp_pcb_doctors` với 15 bác sĩ:

| ID  | Tên bác sĩ | Chuyên khoa                      |
| --- | ---------- | -------------------------------- |
| 1   | 佐藤医師   | 皮膚科・アレルギー科             |
| 2   | 高橋医師   | 内科・消化器科                   |
| 3   | 伊藤医師   | 外科・整形外科                   |
| 4   | 渡辺医師   | 歯科・口腔外科                   |
| 5   | 小林医師   | 循環器科・心臓病科               |
| 6   | 斎藤医師   | 腫瘍科・がん治療科               |
| 7   | 井上医師   | 神経科・脳外科                   |
| 8   | 田中医師   | 産科・生殖医療科                 |
| 9   | 吉田医師   | アレルギー科・免疫科             |
| 10  | 中村医師   | 泌尿器科・腎臓科                 |
| 11  | 山田医師   | 眼科・視覚医療科                 |
| 12  | 佐々木医師 | 耳鼻咽喉科・聴覚医療科           |
| 13  | 松本医師   | 画像診断科・放射線科             |
| 14  | 加藤医師   | 救急科・集中治療科               |
| 15  | 木村医師   | リハビリテーション科・理学療法科 |

## 🚀 Cách sử dụng

### Bước 1: Kích hoạt plugin

1. Copy thư mục `pet-clinic-booking` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin
3. Plugin sẽ tự động tạo các bảng database

### Bước 2: Thêm dữ liệu mẫu

1. **Thêm bác sĩ trước:**

   ```sql
   -- Chạy file sample-doctors.sql trong phpMyAdmin hoặc MySQL client
   ```

2. **Thêm dữ liệu đặt phòng:**
   ```sql
   -- Chạy file sample-data.sql trong phpMyAdmin hoặc MySQL client
   ```

### Bước 3: Test hệ thống

1. **Truy cập trang đặt phòng:** `yoursite.com/dat-phong-kham`
2. **Truy cập trang danh sách:** `yoursite.com/danh-sach-dat-phong`
3. **Test tìm kiếm** với các email mẫu:
   - `yamada@example.com`
   - `sato@example.com`
   - `tanaka@example.com`

## 📊 Thông tin chi tiết

### Dữ liệu đặt phòng bao gồm:

- ✅ Thông tin khách hàng đầy đủ (tên, địa chỉ, SĐT, email)
- ✅ Thông tin thú cưng chi tiết (tên, loại, giống, tuổi, cân nặng)
- ✅ Lịch sử tiêm phòng và bệnh tật
- ✅ Triệu chứng và chẩn đoán
- ✅ Dữ liệu tham khảo điều trị
- ✅ Thông tin bệnh viện giới thiệu
- ✅ Ngày giờ hẹn mong muốn
- ✅ Trạng thái đặt phòng

### Các chuyên khoa có sẵn:

- 🏥 **皮膚科** (Da liễu) - Bệnh ngoài da, dị ứng
- 🏥 **内科** (Nội khoa) - Bệnh nội khoa, tiêu hóa
- 🏥 **外科** (Phẫu thuật) - Phẫu thuật, chỉnh hình
- 🏥 **歯科** (Nha khoa) - Răng miệng, nha khoa
- 🏥 **循環器科** (Tim mạch) - Bệnh tim, tuần hoàn
- 🏥 **腫瘍科** (Ung thư) - Khối u, ung thư
- 🏥 **神経科** (Thần kinh) - Bệnh thần kinh, não
- 🏥 **産科** (Sản khoa) - Sinh sản, thai sản
- 🏥 **アレルギー科** (Dị ứng) - Dị ứng, miễn dịch
- 🏥 **泌尿器科** (Tiết niệu) - Thận, tiết niệu

## 🔍 Test cases

### Test tìm kiếm lịch hẹn:

1. **Email:** `yamada@example.com`
   **Phone:** `03-1234-5678`
   → Kết quả: Lịch hẹn R20250709001 (ポチ - 皮膚科)

2. **Email:** `sato@example.com`
   **Phone:** `03-2345-6789`
   → Kết quả: Lịch hẹn R20250709002 (ミー - 内科)

3. **Email:** `tanaka@example.com`
   **Phone:** `03-3456-7890`
   → Kết quả: Lịch hẹn R20250709003 (ラッキー - 外科)

### Test admin:

1. **Truy cập:** `yoursite.com/admin/danh-sach-bac-si`
2. **Xem danh sách bác sĩ** với 15 bác sĩ
3. **Thêm bác sĩ mới** tại `yoursite.com/admin/them-bac-si`

## 📝 Lưu ý

- Dữ liệu mẫu sử dụng **tiếng Nhật** phù hợp với giao diện
- **Booking ID** được tạo theo format: R + YYYYMMDD + 3 số
- **Trạng thái** bao gồm: pending, confirmed, completed, cancelled
- **Thời gian** được set ngẫu nhiên trong 30 ngày gần đây
- Tất cả **personal_info_consent** đều là 'agreed'

## 🛠️ Troubleshooting

### Nếu gặp lỗi:

1. **Kiểm tra bảng đã tồn tại chưa:**

   ```sql
   SHOW TABLES LIKE 'wp_pcb_bookings';
   SHOW TABLES LIKE 'wp_pcb_doctors';
   ```

2. **Kiểm tra cấu trúc bảng:**

   ```sql
   DESCRIBE wp_pcb_bookings;
   DESCRIBE wp_pcb_doctors;
   ```

3. **Nếu bảng chưa có các trường mới:**
   - Chạy file `update-database.sql` trước
   - Sau đó chạy các file sample data

### Reset dữ liệu:

```sql
TRUNCATE TABLE wp_pcb_bookings;
TRUNCATE TABLE wp_pcb_doctors;
```

Sau đó chạy lại các file sample data.
