<?php
/**
 * Template for booking form
 */
get_header();
?>

<div class="pcb-container">
    <div class="pcb-booking-form">
        <h1>Đặt Phòng Khám Thú Y</h1>

        <form id="pcb-booking-form">
            <div class="pcb-form-section">
                <h3>Thông Tin Khách Hàng</h3>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="customer_name">Họ và tên *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="customer_phone">Số điện thoại *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>
                </div>
                <div class="pcb-form-group">
                    <label for="customer_email">Email *</label>
                    <input type="email" id="customer_email" name="customer_email" required>
                </div>
            </div>

            <div class="pcb-form-section">
                <h3>Thông Tin Thú Cưng</h3>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="pet_name">Tên thú cưng *</label>
                        <input type="text" id="pet_name" name="pet_name" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="pet_type">Loại thú cưng *</label>
                        <select id="pet_type" name="pet_type" required>
                            <option value="">Chọn loại thú cưng</option>
                            <option value="Chó">Chó</option>
                            <option value="Mèo">Mèo</option>
                            <option value="Chim">Chim</option>
                            <option value="Thỏ">Thỏ</option>
                            <option value="Hamster">Hamster</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="pet_breed">Giống</label>
                        <input type="text" id="pet_breed" name="pet_breed">
                    </div>
                    <div class="pcb-form-group">
                        <label for="pet_age">Tuổi (tháng)</label>
                        <input type="number" id="pet_age" name="pet_age" min="1" max="300">
                    </div>
                </div>
                <div class="pcb-form-group">
                    <label for="pet_weight">Cân nặng (kg)</label>
                    <input type="number" id="pet_weight" name="pet_weight" step="0.1" min="0.1">
                </div>
                <div class="pcb-form-group">
                    <label for="symptoms">Triệu chứng</label>
                    <textarea id="symptoms" name="symptoms" rows="4" placeholder="Mô tả các triệu chứng của thú cưng..."></textarea>
                </div>
            </div>

            <div class="pcb-form-section">
                <h3>Thông Tin Lịch Hẹn</h3>
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="appointment_date">Ngày hẹn *</label>
                        <input type="date" id="appointment_date" name="appointment_date" required>
                    </div>
                    <div class="pcb-form-group">
                        <label for="appointment_time">Giờ hẹn *</label>
                        <select id="appointment_time" name="appointment_time" required>
                            <option value="">Chọn giờ</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                </div>
                <div class="pcb-form-group">
                    <label for="doctor_id">Bác sĩ (tùy chọn)</label>
                    <select id="doctor_id" name="doctor_id">
                        <option value="">Chọn bác sĩ</option>
                        <!-- Sẽ được load bằng AJAX -->
                    </select>
                </div>
                <div class="pcb-form-group">
                    <label for="notes">Ghi chú</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>

            <div class="pcb-form-actions">
                <button type="submit" class="pcb-btn pcb-btn-primary">Đặt Phòng</button>
                <a href="<?php echo home_url('/danh-sach-dat-phong'); ?>" class="pcb-btn pcb-btn-secondary">Xem Lịch Hẹn</a>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load doctors
    $.ajax({
        url: pcb_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'pcb_admin_get_doctors',
            nonce: pcb_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                var doctorSelect = $('#doctor_id');
                response.data.forEach(function(doctor) {
                    doctorSelect.append('<option value="' + doctor.id + '">' + doctor.name + ' - ' + doctor.specialization + '</option>');
                });
            }
        }
    });

    // Handle form submission
    $('#pcb-booking-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=pcb_create_booking&nonce=' + pcb_ajax.nonce;

        $.ajax({
            url: pcb_ajax.ajax_url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('.pcb-btn-primary').prop('disabled', true).text('Đang xử lý...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = '<?php echo home_url("/chi-tiet-dat-phong/"); ?>' + response.data.booking_id;
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $('.pcb-btn-primary').prop('disabled', false).text('Đặt Phòng');
            }
        });
    });

    // Set minimum date to today
    var today = new Date().toISOString().split('T')[0];
    $('#appointment_date').attr('min', today);
});
</script>

<?php get_footer(); ?>
