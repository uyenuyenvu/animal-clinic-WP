# Pet Clinic Booking System

Hệ thống đặt phòng khám thú y cho WordPress

## Mô tả

Plugin này cung cấp hệ thống quản lý đặt phòng khám thú y với các tính năng:
- Quản lý danh sách đặt phòng
- Xuất dữ liệu CSV
- Quản lý bác sĩ
- Tìm kiếm và lọc dữ liệu
- Phân quyền truy cập

## Cài đặt

1. Upload plugin vào thư mục `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress admin
3. Truy cập menu "Pet Clinic" trong admin

## Yêu cầu hệ thống

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

## Quyền truy cập

Plugin chỉ cho phép các user role sau truy cập:
- **Administrator** (Quản trị viên)
- **Subscriber** (Người đăng ký)

## Tính năng chính

### 1. Quản lý đặt phòng
- Xem danh sách tất cả đặt phòng
- Tìm kiếm theo nhiều tiêu chí
- Phân trang dữ liệu
- Cập nhật trạng thái đặt phòng

### 2. Tìm kiếm và lọc
- Tìm theo tên bệnh viện
- Tìm theo tên chủ thú cưng
- Tìm theo tên bác sĩ
- Tìm theo khoa khám

### 3. Xuất dữ liệu
- Xuất danh sách đặt phòng ra CSV
- Bao gồm tất cả thông tin chi tiết

### 4. Quản lý bác sĩ (Chỉ Administrator)
- Thêm bác sĩ mới
- Chỉnh sửa thông tin bác sĩ
- Xóa bác sĩ

## Cấu trúc thư mục

```
pet-clinic-booking/
├── includes/
│   ├── permissions.php      # Quản lý quyền truy cập
│   ├── admin-pages.php      # Trang admin
│   └── csv-handler.php      # Xử lý xuất CSV
├── templates/
│   └── admin/
│       ├── appointments-list.php
│       ├── appointment-detail.php
│       ├── hospital-detail.php
│       └── add-doctor.php
├── assets/
│   ├── css/
│   └── js/
├── pet-clinic-booking.php   # File chính
└── README.md
```

## Khắc phục sự cố

Nếu gặp lỗi "Sorry, you are not allowed to access this page":

1. **Kiểm tra đăng nhập**: Đảm bảo đã đăng nhập WordPress admin
2. **Kiểm tra user role**: Phải là Administrator hoặc Subscriber
3. **Kích hoạt plugin**: Đảm bảo plugin đã được kích hoạt

Xem file `TROUBLESHOOTING.md` để biết thêm chi tiết.

## Debug

Để debug quyền truy cập:
1. Bật WP_DEBUG trong wp-config.php
2. Truy cập menu "Pet Clinic > Debug Permissions"

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra file TROUBLESHOOTING.md
2. Bật WP_DEBUG để xem lỗi chi tiết
3. Liên hệ admin để được hỗ trợ

## Phiên bản

- Version: 1.0.0
- Tương thích: WordPress 5.0+
- Tác giả: Your Name
