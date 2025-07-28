<?php
/**
 * Template for booking list
 */
get_header();
?>

<div class="pcb-container">
    <div class="pcb-booking-list">
        <h1>Danh Sách Lịch Hẹn</h1>

        <div class="pcb-search-form">
            <h3>Tìm Kiếm Lịch Hẹn</h3>
            <form id="pcb-search-form">
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="search_email">Email</label>
                        <input type="email" id="search_email" name="search_email" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="search_phone">Số điện thoại</label>
                        <input type="tel" id="search_phone" name="search_phone" required>
                    </div>
                </div>
                <button type="submit" class="pcb-btn pcb-btn-primary">Tìm Kiếm</button>
            </form>
        </div>

        <div id="pcb-bookings-container" class="pcb-bookings-container" style="display: none;">
            <h3>Kết Quả Tìm Kiếm</h3>
            <div id="pcb-bookings-list" class="pcb-bookings-list">
                <!-- Bookings will be loaded here -->
            </div>
        </div>

        <div class="pcb-actions">
            <a href="<?php echo home_url('/dat-phong-kham'); ?>" class="pcb-btn pcb-btn-primary">Đặt Phòng Mới</a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#pcb-search-form').on('submit', function(e) {
        e.preventDefault();

        var email = $('#search_email').val();
        var phone = $('#search_phone').val();

        if (!email || !phone) {
            alert('Vui lòng nhập đầy đủ email và số điện thoại.');
            return;
        }

        $.ajax({
            url: pcb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcb_get_bookings',
                customer_email: email,
                customer_phone: phone,
                nonce: pcb_ajax.nonce
            },
            beforeSend: function() {
                $('.pcb-btn-primary').prop('disabled', true).text('Đang tìm kiếm...');
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displayBookings(response.data);
                } else {
                    alert('Không tìm thấy lịch hẹn nào với thông tin đã nhập.');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $('.pcb-btn-primary').prop('disabled', false).text('Tìm Kiếm');
            }
        });
    });

    function displayBookings(bookings) {
        var html = '';

        bookings.forEach(function(booking) {
            var statusClass = 'pcb-status-' + booking.status;
            var statusText = getStatusText(booking.status);
            var appointmentDate = new Date(booking.appointment_date).toLocaleDateString('vi-VN');

            html += '<div class="pcb-booking-item">';
            html += '<div class="pcb-booking-header">';
            html += '<h4>Lịch hẹn #' + booking.id + '</h4>';
            html += '<span class="pcb-status ' + statusClass + '">' + statusText + '</span>';
            html += '</div>';
            html += '<div class="pcb-booking-details">';
            html += '<p><strong>Khách hàng:</strong> ' + booking.customer_name + '</p>';
            html += '<p><strong>Thú cưng:</strong> ' + booking.pet_name + ' (' + booking.pet_type + ')</p>';
            html += '<p><strong>Ngày hẹn:</strong> ' + appointmentDate + ' lúc ' + booking.appointment_time + '</p>';
            if (booking.doctor_name) {
                html += '<p><strong>Bác sĩ:</strong> ' + booking.doctor_name + '</p>';
            }
            html += '</div>';
            html += '<div class="pcb-booking-actions">';
            html += '<a href="<?php echo home_url("/chi-tiet-dat-phong/"); ?>' + booking.id + '" class="pcb-btn pcb-btn-secondary">Xem Chi Tiết</a>';
            html += '</div>';
            html += '</div>';
        });

        $('#pcb-bookings-list').html(html);
        $('#pcb-bookings-container').show();
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
