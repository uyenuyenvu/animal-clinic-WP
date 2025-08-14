# Pet Clinic Booking - Troubleshooting Guide

## Lỗi "Sorry, you are not allowed to access this page"

### Nguyên nhân có thể:

1. **User chưa đăng nhập vào WordPress admin**
2. **User role không có quyền truy cập**
3. **Plugin chưa được kích hoạt đúng cách**

### Cách khắc phục:

#### 1. Kiểm tra đăng nhập
- Đảm bảo bạn đã đăng nhập vào WordPress admin
- Truy cập: `your-site.com/wp-admin`
- Đăng nhập với tài khoản có quyền admin

#### 2. Kiểm tra User Role
Plugin chỉ cho phép các role sau truy cập:
- **Administrator** (Quản trị viên)
- **Subscriber** (Người đăng ký)

Để thay đổi user role:
1. Vào **Users > Your Profile**
2. Chọn role phù hợp trong dropdown "Role"
3. Click **Update Profile**

#### 3. Kiểm tra Plugin
1. Vào **Plugins > Installed Plugins**
2. Tìm "Pet Clinic Booking System"
3. Đảm bảo plugin đã được **Activated**

#### 4. Debug Permissions (Chỉ khi WP_DEBUG = true)
Nếu bạn đã bật WP_DEBUG, có thể truy cập:
- **Pet Clinic > Debug Permissions** trong admin menu

### Các bước kiểm tra nhanh:

1. **Đăng nhập WordPress admin**
2. **Kiểm tra user role** (phải là Administrator hoặc Subscriber)
3. **Kích hoạt plugin** nếu chưa kích hoạt
4. **Truy cập lại trang appointments**

### Nếu vẫn gặp lỗi:

1. Kiểm tra file log WordPress
2. Bật WP_DEBUG trong wp-config.php
3. Liên hệ admin để kiểm tra quyền truy cập

### Cấu hình WP_DEBUG:

Thêm vào file `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Liên hệ hỗ trợ:

Nếu vẫn gặp vấn đề, vui lòng cung cấp:
- WordPress version
- Plugin version
- User role hiện tại
- Thông báo lỗi chi tiết
