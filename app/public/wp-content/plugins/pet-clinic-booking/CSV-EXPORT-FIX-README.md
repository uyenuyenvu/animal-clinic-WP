# CSV Export Fix - Pet Clinic Booking Plugin

## Vấn đề đã được sửa

### 1. **Lỗi Headers Already Sent**
```
Warning: Cannot modify header information - headers already sent by (output started at C:\Users\Admin\Local Sites\animal-clinic\app\public\wp-includes\fonts\class-wp-font-face.php:121) in C:\Users\Admin\Local Sites\animal-clinic\app\public\wp-content\plugins\pet-clinic-booking\templates\admin\appointments-list.php on line 232
```

### 2. **Nguyên nhân**
- Logic CSV export được đặt trong template `appointments-list.php`
- WordPress đã gửi một số output trước khi đến phần CSV export
- Headers không thể được thiết lập sau khi đã có output

## Giải pháp đã thực hiện

### 1. **Tạo CSV Handler riêng biệt**
- File mới: `includes/csv-handler.php`
- Class `PCB_CSV_Handler` xử lý tất cả logic CSV export
- Hook vào `init` action với priority 1 để chạy sớm

### 2. **Output Buffering**
```php
// Prevent any output before headers
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();
```

### 3. **Early Hook**
```php
add_action('init', array($this, 'handle_csv_download'), 1);
```

### 4. **Permission Check**
```php
// Check permissions
if (!PCB_Permissions::can_export_csv()) {
    wp_die('Không có quyền xuất CSV.');
}
```

## Cấu trúc file mới

### 1. **includes/csv-handler.php**
```php
class PCB_CSV_Handler {
    public function __construct() {
        add_action('init', array($this, 'handle_csv_download'), 1);
    }
    
    public function handle_csv_download() {
        // Check if this is a CSV download request
        if (!isset($_GET['page']) || $_GET['page'] !== 'pcb-appointments') {
            return;
        }
        
        if (!isset($_GET['download']) || $_GET['download'] !== 'csv') {
            return;
        }
        
        // Check permissions
        if (!PCB_Permissions::can_export_csv()) {
            wp_die('Không có quyền xuất CSV.');
        }
        
        // Prevent any output before headers
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // ... CSV export logic ...
    }
}
```

### 2. **pet-clinic-booking.php**
```php
// Include admin pages and permissions
if (is_admin()) {
    require_once PCB_PLUGIN_PATH . 'includes/permissions.php';
    require_once PCB_PLUGIN_PATH . 'includes/admin-pages.php';
    require_once PCB_PLUGIN_PATH . 'includes/csv-handler.php';
}
```

### 3. **templates/admin/appointments-list.php**
- Đã xóa toàn bộ logic CSV export
- Chỉ giữ lại phần hiển thị nút export với permission check

## Tính năng mới

### 1. **Permission-based Export**
- Chỉ Administrator và Subscriber có thể export CSV
- Clear error message nếu không có quyền

### 2. **Filter Support**
- CSV export hỗ trợ tất cả filter hiện tại
- Hospital name, owner name, doctor name, department

### 3. **Proper Headers**
```php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
```

### 4. **UTF-8 BOM**
```php
// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
```

## Cách hoạt động

### 1. **Request Flow**
```
User clicks "Export CSV" 
→ URL: admin.php?page=pcb-appointments&download=csv
→ PCB_CSV_Handler::handle_csv_download()
→ Check permissions
→ Clean output buffer
→ Set headers
→ Generate CSV
→ Download file
```

### 2. **Permission Check**
```php
if (!PCB_Permissions::can_export_csv()) {
    wp_die('Không có quyền xuất CSV.');
}
```

### 3. **Output Buffer Management**
```php
// Clean any existing output
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();
```

## Benefits

### 1. **No More Header Errors**
- ✅ Không còn lỗi "headers already sent"
- ✅ Clean output buffer trước khi set headers
- ✅ Early hook để xử lý trước khi có output

### 2. **Better Security**
- ✅ Permission check trước khi export
- ✅ Sanitize input parameters
- ✅ Proper error handling

### 3. **Maintainability**
- ✅ Separated concerns - CSV logic riêng biệt
- ✅ Reusable CSV handler
- ✅ Easy to modify export format

### 4. **User Experience**
- ✅ Clear error messages
- ✅ Proper file naming với date
- ✅ UTF-8 support với BOM

## Testing

### 1. **Test Cases**
- ✅ Export CSV với Administrator
- ✅ Export CSV với Subscriber
- ✅ Deny export với Editor/Author/Contributor
- ✅ Export với filters
- ✅ Export không có filters

### 2. **Expected Results**
- ✅ File download với tên: `appointments_YYYY-MM-DD.csv`
- ✅ UTF-8 encoding với Japanese characters
- ✅ All data included
- ✅ Proper CSV format

## Future Enhancements

1. **Custom Export Formats**: Support cho Excel, PDF
2. **Scheduled Exports**: Auto export theo lịch
3. **Email Export**: Gửi CSV qua email
4. **Export Templates**: Custom CSV templates
5. **Bulk Export**: Export nhiều loại data cùng lúc 