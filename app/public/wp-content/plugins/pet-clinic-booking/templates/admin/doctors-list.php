<?php
/**
 * Template for admin doctors list
 */
if (!current_user_can('manage_options')) {
    wp_die('Không có quyền truy cập.');
}

get_header();
?>

<div class="pcb-container">
    <div class="pcb-admin-doctors-list">
        <h1>Danh Sách Bác Sĩ</h1>

        <div class="pcb-admin-actions">
            <a href="<?php echo home_url('/admin/them-bac-si'); ?>" class="pcb-btn pcb-btn-primary">Thêm Bác Sĩ Mới</a>
        </div>

        <div id="pcb-doctors-container">
            <div class="pcb-loading">Đang tải danh sách bác sĩ...</div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load doctors list
    $.ajax({
        url: pcb_admin_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'pcb_admin_get_doctors',
            nonce: pcb_admin_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                displayDoctors(response.data);
            } else {
                $('#pcb-doctors-container').html('<div class="pcb-error">' + response.data + '</div>');
            }
        },
        error: function() {
            $('#pcb-doctors-container').html('<div class="pcb-error">Có lỗi xảy ra khi tải danh sách bác sĩ.</div>');
        }
    });

    function displayDoctors(doctors) {
        if (doctors.length === 0) {
            $('#pcb-doctors-container').html('<div class="pcb-empty">Chưa có bác sĩ nào được thêm.</div>');
            return;
        }

        var html = '<div class="pcb-doctors-grid">';

        doctors.forEach(function(doctor) {
            var statusClass = 'pcb-status-' + doctor.status;
            var statusText = getStatusText(doctor.status);
            var createdDate = new Date(doctor.created_at).toLocaleDateString('vi-VN');

            html += '<div class="pcb-doctor-card">';
            html += '<div class="pcb-doctor-header">';
            html += '<h3>' + doctor.name + '</h3>';
            html += '<span class="pcb-status ' + statusClass + '">' + statusText + '</span>';
            html += '</div>';
            html += '<div class="pcb-doctor-details">';
            html += '<p><strong>Chuyên khoa:</strong> ' + doctor.specialization + '</p>';
            html += '<p><strong>Số điện thoại:</strong> ' + doctor.phone + '</p>';
            html += '<p><strong>Email:</strong> ' + doctor.email + '</p>';
            html += '<p><strong>Ngày thêm:</strong> ' + createdDate + '</p>';
            html += '</div>';
            html += '<div class="pcb-doctor-actions">';
            html += '<button class="pcb-btn pcb-btn-secondary" onclick="editDoctor(' + doctor.id + ')">Sửa</button>';
            html += '<button class="pcb-btn pcb-btn-danger" onclick="deleteDoctor(' + doctor.id + ')">Xóa</button>';
            html += '</div>';
            html += '</div>';
        });

        html += '</div>';
        $('#pcb-doctors-container').html(html);
    }

    function getStatusText(status) {
        switch(status) {
            case 'active':
                return 'Hoạt động';
            case 'inactive':
                return 'Không hoạt động';
            default:
                return status;
        }
    }
});

function editDoctor(doctorId) {
    // TODO: Implement edit functionality
    alert('Chức năng sửa bác sĩ sẽ được phát triển sau.');
}

function deleteDoctor(doctorId) {
    if (confirm('Bạn có chắc chắn muốn xóa bác sĩ này?')) {
        // TODO: Implement delete functionality
        alert('Chức năng xóa bác sĩ sẽ được phát triển sau.');
    }
}
</script>

<?php get_footer(); ?>
