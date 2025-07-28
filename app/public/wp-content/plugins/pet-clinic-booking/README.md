# Pet Clinic Booking System

Hệ thống đặt phòng khám thú y cho WordPress

## Tính năng

### Cho khách hàng (không cần đăng nhập):

1. **Đặt phòng khám** - `/dat-phong-kham`

   - Form đăng ký lịch hẹn với thông tin khách hàng và thú cưng
   - Chọn bác sĩ (tùy chọn)
   - Chọn ngày và giờ hẹn
   - Mô tả triệu chứng

2. **Danh sách lịch hẹn** - `/danh-sach-dat-phong`

   - Tìm kiếm lịch hẹn bằng email và số điện thoại
   - Hiển thị danh sách lịch hẹn của khách hàng

3. **Chi tiết lịch hẹn** - `/chi-tiet-dat-phong/{id}`
   - Xem chi tiết thông tin lịch hẹn
   - Thông tin khách hàng, thú cưng và lịch hẹn

### Cho admin:

1. **Thêm bác sĩ** - `/admin/them-bac-si`

   - Form thêm bác sĩ mới với thông tin cá nhân và chuyên khoa

2. **Danh sách bác sĩ** - `/admin/danh-sach-bac-si`
   - Xem danh sách tất cả bác sĩ
   - Chức năng sửa/xóa (sẽ phát triển sau)

## Cài đặt

1. Copy thư mục `pet-clinic-booking` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin
3. Plugin sẽ tự động tạo các bảng database cần thiết

## Cấu trúc Database

### Bảng `wp_pcb_doctors`

- `id` - ID bác sĩ
- `name` - Tên bác sĩ
- `specialization` - Chuyên khoa
- `phone` - Số điện thoại
- `email` - Email
- `status` - Trạng thái (active/inactive)
- `created_at` - Ngày tạo
- `updated_at` - Ngày cập nhật

### Bảng `wp_pcb_bookings`

- `id` - ID lịch hẹn
- `booking_id` - ID đặt lịch (format: R20250709002)
- `department` - Chuyên khoa (診療科)
- `preferred_date_1` - Ngày mong muốn thứ 1 (第1希望日)
- `preferred_time_1` - Giờ mong muốn thứ 1 (第1希望時間)
- `preferred_date_2` - Ngày mong muốn thứ 2 (第2希望日)
- `preferred_time_2` - Giờ mong muốn thứ 2 (第2希望時間)
- `referral_hospital` - Bệnh viện giới thiệu (紹介病院)
- `hospital_director` - Tên giám đốc bệnh viện (院長名)
- `assigned_doctor` - Bác sĩ phụ trách (担当医)
- `customer_name` - Tên chủ nuôi (飼主名)
- `pet_name` - Tên thú cưng (動物名)
- `customer_address` - Địa chỉ (住所)
- `customer_phone` - Số điện thoại (電話番号)
- `customer_email` - Email (メールアドレス)
- `emergency_contact` - Liên hệ khẩn cấp (緊急連絡先)
- `pet_type` - Loại thú cưng (動物種)
- `pet_breed` - Giống thú cưng (品種)
- `pet_age` - Tuổi thú cưng (tháng)
- `pet_birth_date` - Ngày sinh thú cưng (生年月日)
- `pet_gender` - Giới tính thú cưng (性別)
- `pet_weight` - Cân nặng thú cưng (kg)
- `living_environment` - Nơi sống (生活場所)
- `vaccination_history` - Lịch sử tiêm phòng (予防歴)
- `disease_details` - Chi tiết bệnh (疾患詳細)
- `symptoms` - Triệu chứng (症状)
- `treatment_reference_data` - Dữ liệu tham khảo điều trị (治療参考データ)
- `personal_info_consent` - Đồng ý thông tin cá nhân (個人情報同意)
- `doctor_id` - ID bác sĩ (foreign key)
- `appointment_date` - Ngày hẹn
- `appointment_time` - Giờ hẹn
- `status` - Trạng thái (pending/confirmed/completed/cancelled)
- `notes` - Ghi chú
- `created_at` - Ngày tạo
- `updated_at` - Ngày cập nhật

## URL Routes

### Frontend (Khách hàng):

- `/dat-phong-kham` - Form đặt phòng
- `/danh-sach-dat-phong` - Danh sách lịch hẹn
- `/chi-tiet-dat-phong/{id}` - Chi tiết lịch hẹn

### Admin:

- `/admin/them-bac-si` - Thêm bác sĩ
- `/admin/danh-sach-bac-si` - Danh sách bác sĩ

## Tính năng kỹ thuật

- **AJAX**: Tất cả form và tìm kiếm sử dụng AJAX
- **Security**: Nonce verification cho tất cả AJAX requests
- **Responsive**: Giao diện responsive cho mobile
- **Validation**: Client-side và server-side validation
- **Error Handling**: Xử lý lỗi và thông báo người dùng

## Tùy chỉnh

### CSS

- File CSS chính: `/assets/css/frontend.css`
- File CSS admin: `/assets/css/admin.css`

### JavaScript

- File JS frontend: `/assets/js/frontend.js`
- File JS admin: `/assets/js/admin.js`

### Templates

- Booking form: `/templates/booking-form.php`
- Booking list: `/templates/booking-list.php`
- Booking detail: `/templates/booking-detail.php`
- Admin add doctor: `/templates/admin/add-doctor.php`
- Admin doctors list: `/templates/admin/doctors-list.php`

## Phát triển tiếp

### Tính năng có thể thêm:

1. Chức năng sửa/xóa bác sĩ
2. Quản lý trạng thái lịch hẹn
3. Email notification
4. Calendar view
5. Export data
6. Multi-language support
7. Payment integration
8. SMS notification

### Cải thiện:

1. Thêm validation cho ngày/giờ hẹn
2. Kiểm tra trùng lịch
3. Thêm captcha
4. Rate limiting
5. Logging system

## Hỗ trợ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng liên hệ developer.
