<?php
/**
 * Template for admin add doctor
 */
if (!current_user_can('manage_options')) {
    wp_die('Không có quyền truy cập.');
}

get_header();
?>

<div class="pcb-container">
    <div class="pcb-admin-add-doctor">
        <h1>Thêm Bác Sĩ Mới</h1>

        <form id="pcb-add-doctor-form">
            <div class="pcb-form-section">
                <h3>Thông Tin Bác Sĩ</h3>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="doctor_name">Họ và tên *</label>
                        <input type="text" id="doctor_name" name="name" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="doctor_specialization">Chuyên khoa *</label>
                        <input type="text" id="doctor_specialization" name="specialization" required>
                    </div>
                </div>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="doctor_phone">Số điện thoại *</label>
                        <input type="tel" id="doctor_phone" name="phone" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="doctor_email">Email *</label>
                        <input type="email" id="doctor_email" name="email" required>
                    </div>
                </div>
            </div>

            <div class="pcb-form-actions">
                <button type="submit" class="pcb-btn pcb-btn-primary">Thêm Bác Sĩ</button>
                <a href="<?php echo home_url('/admin/danh-sach-bac-si'); ?>" class="pcb-btn pcb-btn-secondary">Danh Sách Bác Sĩ</a>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#pcb-add-doctor-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=pcb_admin_add_doctor&nonce=' + pcb_admin_ajax.nonce;

        $.ajax({
            url: pcb_admin_ajax.ajax_url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('.pcb-btn-primary').prop('disabled', true).text('Đang xử lý...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    window.location.href = '<?php echo home_url("/admin/danh-sach-bac-si"); ?>';
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $('.pcb-btn-primary').prop('disabled', false).text('Thêm Bác Sĩ');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
