# Database Fields - Pet Clinic Booking System

## Bảng `wp_pcb_bookings` - Các trường mới theo giao diện Nhật

### Thông tin đặt lịch (予約情報)

- `booking_id` (varchar(20)) - ID đặt lịch (ví dụ: R20250709002)
- `department` (varchar(100)) - Chuyên khoa (診療科) - ví dụ: 皮膚科, 一般診療科
- `preferred_date_1` (date) - Ngày mong muốn thứ 1 (第 1 希望日)
- `preferred_time_1` (time) - Giờ mong muốn thứ 1 (第 1 希望時間)
- `preferred_date_2` (date) - Ngày mong muốn thứ 2 (第 2 希望日)
- `preferred_time_2` (time) - Giờ mong muốn thứ 2 (第 2 希望時間)

### Thông tin bệnh viện giới thiệu (紹介病院)

- `referral_hospital` (varchar(200)) - Bệnh viện giới thiệu (紹介病院)
- `hospital_director` (varchar(100)) - Tên giám đốc bệnh viện (院長名)
- `assigned_doctor` (varchar(100)) - Bác sĩ phụ trách (担当医)

### Thông tin khách hàng (飼主情報)

- `customer_name` (varchar(100)) - Tên chủ nuôi (飼主名)
- `customer_address` (text) - Địa chỉ (住所)
- `customer_phone` (varchar(20)) - Số điện thoại (電話番号)
- `customer_email` (varchar(100)) - Email (メールアドレス)
- `emergency_contact` (varchar(20)) - Liên hệ khẩn cấp (緊急連絡先)

### Thông tin thú cưng (動物情報)

- `pet_name` (varchar(100)) - Tên thú cưng (動物名)
- `pet_type` (varchar(50)) - Loại thú cưng (動物種) - ví dụ: 犬, 猫
- `pet_breed` (varchar(100)) - Giống thú cưng (品種) - ví dụ: 柴犬, チワワ
- `pet_age` (int(3)) - Tuổi (年齢) - tính bằng tháng
- `pet_birth_date` (date) - Ngày sinh (生年月日)
- `pet_gender` (enum('male','female')) - Giới tính (性別) - オス/メス
- `pet_weight` (decimal(5,2)) - Cân nặng (体重) - kg

### Thông tin môi trường và lịch sử

- `living_environment` (text) - Nơi sống (生活場所)
- `vaccination_history` (text) - Lịch sử tiêm phòng (予防歴)
- `disease_details` (text) - Chi tiết bệnh (疾患詳細)
- `symptoms` (text) - Triệu chứng (症状)

### Dữ liệu tham khảo điều trị (治療参考データ)

- `treatment_reference_data` (text) - Dữ liệu tham khảo điều trị
- `personal_info_consent` (enum('agreed','not_agreed')) - Đồng ý thông tin cá nhân (個人情報同意)

### Thông tin lịch hẹn

- `doctor_id` (mediumint(9)) - ID bác sĩ (foreign key)
- `appointment_date` (date) - Ngày hẹn
- `appointment_time` (time) - Giờ hẹn
- `status` (varchar(20)) - Trạng thái (pending/confirmed/completed/cancelled)
- `notes` (text) - Ghi chú

### Thông tin hệ thống

- `created_at` (datetime) - Ngày tạo
- `updated_at` (datetime) - Ngày cập nhật

## Mapping với giao diện Nhật

| Giao diện Nhật | Database Field                     | Mô tả                |
| -------------- | ---------------------------------- | -------------------- |
| 予約 ID        | booking_id                         | ID đặt lịch          |
| 診療科         | department                         | Chuyên khoa          |
| 希望日時       | preferred_date_1, preferred_time_1 | Thời gian mong muốn  |
| 紹介病院       | referral_hospital                  | Bệnh viện giới thiệu |
| 院長名         | hospital_director                  | Tên giám đốc         |
| 担当医         | assigned_doctor                    | Bác sĩ phụ trách     |
| 飼主名         | customer_name                      | Tên chủ nuôi         |
| 動物名         | pet_name                           | Tên thú cưng         |
| 住所           | customer_address                   | Địa chỉ              |
| 電話番号       | customer_phone                     | Số điện thoại        |
| 緊急連絡先     | emergency_contact                  | Liên hệ khẩn cấp     |
| 動物種・品種   | pet_type, pet_breed                | Loại/Giống thú cưng  |
| 年齢・生年月日 | pet_age, pet_birth_date            | Tuổi/Ngày sinh       |
| 性別           | pet_gender                         | Giới tính            |
| 生活場所       | living_environment                 | Nơi sống             |
| 予防歴         | vaccination_history                | Lịch sử tiêm phòng   |
| 疾患詳細       | disease_details                    | Chi tiết bệnh        |
| 治療参考データ | treatment_reference_data           | Dữ liệu tham khảo    |
| 個人情報同意   | personal_info_consent              | Đồng ý thông tin     |

## Cách sử dụng

1. **Kích hoạt plugin** - Plugin sẽ tự động tạo bảng với cấu trúc mới
2. **Nếu bảng đã tồn tại** - Chạy file `update-database.sql` để thêm các trường mới
3. **Cập nhật form** - Cập nhật form đặt phòng để sử dụng các trường mới

## Lưu ý

- Tất cả các trường mới đều có thể NULL trừ `booking_id`, `department`, `customer_name`, `pet_name`, `customer_address`, `customer_phone`, `customer_email`, `pet_type`, `pet_gender`
- `booking_id` được tự động tạo theo format: R + YYYYMMDD + 3 số ngẫu nhiên
- `personal_info_consent` mặc định là 'agreed'
- Các trường text có thể chứa dữ liệu dài như lịch sử tiêm phòng, chi tiết bệnh
