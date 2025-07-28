-- Cập nhật bảng wp_pcb_bookings với các trường mới theo giao diện Nhật
-- Chạy file này nếu bảng đã tồn tại và cần thêm các trường mới
-- Thêm các trường mới
ALTER TABLE wp_pcb_bookings
ADD COLUMN booking_id varchar(20) NOT NULL
AFTER id,
    ADD COLUMN department varchar(100) NOT NULL
AFTER booking_id,
    ADD COLUMN preferred_date_1 date
AFTER department,
    ADD COLUMN preferred_time_1 time
AFTER preferred_date_1,
    ADD COLUMN preferred_date_2 date
AFTER preferred_time_1,
    ADD COLUMN preferred_time_2 time
AFTER preferred_date_2,
    ADD COLUMN referral_hospital varchar(200)
AFTER preferred_time_2,
    ADD COLUMN hospital_director varchar(100)
AFTER referral_hospital,
    ADD COLUMN assigned_doctor varchar(100)
AFTER hospital_director,
    ADD COLUMN customer_address text NOT NULL
AFTER pet_name,
    ADD COLUMN emergency_contact varchar(20)
AFTER customer_phone,
    ADD COLUMN pet_birth_date date
AFTER pet_age,
    ADD COLUMN pet_gender enum('male', 'female') NOT NULL
AFTER pet_birth_date,
    ADD COLUMN living_environment text
AFTER pet_weight,
    ADD COLUMN vaccination_history text
AFTER living_environment,
    ADD COLUMN disease_details text
AFTER vaccination_history,
    ADD COLUMN treatment_reference_data text
AFTER disease_details,
    ADD COLUMN personal_info_consent enum('agreed', 'not_agreed') DEFAULT 'agreed'
AFTER treatment_reference_data;
-- Thêm unique key cho booking_id
ALTER TABLE wp_pcb_bookings
ADD UNIQUE KEY booking_id (booking_id);
-- Cập nhật dữ liệu mẫu cho các trường mới
UPDATE wp_pcb_bookings
SET booking_id = CONCAT(
        'R',
        DATE_FORMAT(created_at, '%Y%m%d'),
        LPAD(id, 3, '0')
    ),
    department = '一般診療科',
    pet_gender = 'male',
    personal_info_consent = 'agreed'
WHERE booking_id IS NULL
    OR booking_id = '';
-- Thêm index cho các trường thường được tìm kiếm
ALTER TABLE wp_pcb_bookings
ADD INDEX idx_booking_id (booking_id),
    ADD INDEX idx_department (department),
    ADD INDEX idx_customer_phone (customer_phone),
    ADD INDEX idx_customer_email (customer_email);
