<?php
/**
 * Template for booking detail
 */
get_header();

$booking_id = get_query_var('booking_id');
?>

<div class="pcb-container">
    <div class="pcb-booking-detail">
        <h1>Chi Tiết Lịch Hẹn</h1>

        <div id="pcb-booking-detail-container">
            <div class="pcb-loading">Đang tải thông tin...</div>
        </div>

        <div class="pcb-actions">
            <a href="<?php echo home_url('/dat-phong-kham'); ?>" class="pcb-btn pcb-btn-primary">Đặt Phòng Mới</a>
            <a href="<?php echo home_url('/danh-sach-dat-phong'); ?>" class="pcb-btn pcb-btn-secondary">Danh Sách Lịch Hẹn</a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var bookingId = '<?php echo $booking_id; ?>';

    // Load booking details
    $.ajax({
        url: pcb_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'pcb_get_booking_detail',
            booking_id: bookingId,
            nonce: pcb_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                displayBookingDetail(response.data);
            } else {
                $('#pcb-booking-detail-container').html('<div class="pcb-error">' + response.data + '</div>');
            }
        },
        error: function() {
            $('#pcb-booking-detail-container').html('<div class="pcb-error">Có lỗi xảy ra khi tải thông tin.</div>');
        }
    });

    function displayBookingDetail(booking) {
        var statusClass = 'pcb-status-' + booking.status;
        var statusText = getStatusText(booking.status);
        var appointmentDate = new Date(booking.appointment_date).toLocaleDateString('vi-VN');
        var createdDate = new Date(booking.created_at).toLocaleDateString('vi-VN');

        var html = '<div class="pcb-booking-detail-card">';
        html += '<div class="pcb-booking-header">';
        html += '<h2>Lịch hẹn #' + booking.id + '</h2>';
        html += '<span class="pcb-status ' + statusClass + '">' + statusText + '</span>';
        html += '</div>';

        html += '<div class="pcb-booking-sections">';

        // Thông tin khách hàng
        html += '<div class="pcb-section">';
        html += '<h3>Thông Tin Khách Hàng</h3>';
        html += '<div class="pcb-info-grid">';
        html += '<div class="pcb-info-item"><strong>Họ và tên:</strong> ' + booking.customer_name + '</div>';
        html += '<div class="pcb-info-item"><strong>Số điện thoại:</strong> ' + booking.customer_phone + '</div>';
        html += '<div class="pcb-info-item"><strong>Email:</strong> ' + booking.customer_email + '</div>';
        html += '</div>';
        html += '</div>';

        // Thông tin thú cưng
        html += '<div class="pcb-section">';
        html += '<h3>Thông Tin Thú Cưng</h3>';
        html += '<div class="pcb-info-grid">';
        html += '<div class="pcb-info-item"><strong>Tên thú cưng:</strong> ' + booking.pet_name + '</div>';
        html += '<div class="pcb-info-item"><strong>Loại:</strong> ' + booking.pet_type + '</div>';
        if (booking.pet_breed) {
            html += '<div class="pcb-info-item"><strong>Giống:</strong> ' + booking.pet_breed + '</div>';
        }
        if (booking.pet_age) {
            html += '<div class="pcb-info-item"><strong>Tuổi:</strong> ' + booking.pet_age + ' tháng</div>';
        }
        if (booking.pet_weight) {
            html += '<div class="pcb-info-item"><strong>Cân nặng:</strong> ' + booking.pet_weight + ' kg</div>';
        }
        html += '</div>';
        if (booking.symptoms) {
            html += '<div class="pcb-info-item"><strong>Triệu chứng:</strong><br>' + booking.symptoms + '</div>';
        }
        html += '</div>';

        // Thông tin lịch hẹn
        html += '<div class="pcb-section">';
        html += '<h3>Thông Tin Lịch Hẹn</h3>';
        html += '<div class="pcb-info-grid">';
        html += '<div class="pcb-info-item"><strong>Ngày hẹn:</strong> ' + appointmentDate + '</div>';
        html += '<div class="pcb-info-item"><strong>Giờ hẹn:</strong> ' + booking.appointment_time + '</div>';
        if (booking.doctor_name) {
            html += '<div class="pcb-info-item"><strong>Bác sĩ:</strong> ' + booking.doctor_name + '</div>';
            if (booking.specialization) {
                html += '<div class="pcb-info-item"><strong>Chuyên khoa:</strong> ' + booking.specialization + '</div>';
            }
        }
        html += '</div>';
        if (booking.notes) {
            html += '<div class="pcb-info-item"><strong>Ghi chú:</strong><br>' + booking.notes + '</div>';
        }
        html += '</div>';

        // Thông tin hệ thống
        html += '<div class="pcb-section">';
        html += '<h3>Thông Tin Hệ Thống</h3>';
        html += '<div class="pcb-info-grid">';
        html += '<div class="pcb-info-item"><strong>Ngày tạo:</strong> ' + createdDate + '</div>';
        html += '<div class="pcb-info-item"><strong>Trạng thái:</strong> ' + statusText + '</div>';
        html += '</div>';
        html += '</div>';

        html += '</div>'; // End pcb-booking-sections
        html += '</div>'; // End pcb-booking-detail-card

        $('#pcb-booking-detail-container').html(html);
    }

    function getStatusText(status) {
        switch(status) {
            case 'pending':
                return 'Chờ xác nhận';
            case 'confirmed':
                return 'Đã xác nhận';
            case 'completed':
                return 'Đã hoàn thành';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return status;
        }
    }
});
</script>

<?php get_footer(); ?>
