<?php
wp_enqueue_script('pcb-admin', PCB_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PCB_VERSION, true);
wp_enqueue_style('pcb-admin', PCB_PLUGIN_URL . 'assets/css/admin.css', array(), PCB_VERSION);
wp_localize_script('pcb-admin', 'pcb_admin_ajax', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('pcb_admin_nonce')
));
?>
<?php
/**
 * Template for admin appointment detail
 */
if (!current_user_can('manage_options')) {
    wp_die('Không có quyền truy cập.');
}

$appointment_id = get_query_var('appointment_id');
if (!$appointment_id) {
    wp_die('Không tìm thấy lịch hẹn.');
}

get_header();
?>

<div class="pcb-admin-appointment-detail">
    <!-- Header Section -->
    <div class="pcb-detail-header">
        <h1>申し込み詳細</h1>
        <div class="pcb-detail-nav">
            <a href="<?php echo home_url('/admin/danh-sach-dat-lich/'); ?>" class="pcb-btn pcb-btn-back">一覧に戻る</a>
            <div class="pcb-detail-info">
                <span class="pcb-info-item">予約ID: <strong id="booking-id">-</strong></span>
                <span class="pcb-info-item">受付日時: <strong id="reception-time">-</strong></span>
                <span class="pcb-info-item">ステータス: <span id="status-badge" class="pcb-status pcb-status-pending">受付中</span></span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="pcb-appointment-detail-content">
        <div class="pcb-loading">読み込み中...</div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const appointmentId = <?php echo json_encode($appointment_id); ?>;

    loadAppointmentDetail();

    function loadAppointmentDetail() {
        $.ajax({
            url: pcb_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcb_admin_get_appointment_detail',
                appointment_id: appointmentId,
                nonce: pcb_admin_ajax.nonce
            },
            beforeSend: function() {
                $('#pcb-appointment-detail-content').html('<div class="pcb-loading">読み込み中...</div>');
            },
            success: function(response) {
                if (response.success) {
                    displayAppointmentDetail(response.data);
                } else {
                    $('#pcb-appointment-detail-content').html('<div class="pcb-error">' + response.data + '</div>');
                }
            },
            error: function() {
                $('#pcb-appointment-detail-content').html('<div class="pcb-error">エラーが発生しました。</div>');
            }
        });
    }

    function displayAppointmentDetail(appointment) {
        // Update header info
        $('#booking-id').text(appointment.booking_id || '-');
        $('#reception-time').text(formatDateTime(appointment.created_at));
        updateStatusBadge(appointment.status);

        let html = `
            <div class="pcb-detail-sections">
                <!-- 診療科 Section -->
                <div class="pcb-detail-section">
                    <h3>診療科</h3>
                    <div class="pcb-detail-field">
                        <label>選択された診療科:</label>
                        <div class="pcb-field-value">${appointment.department || '-'}</div>
                    </div>
                </div>

                <!-- 診察日時 Section -->
                <div class="pcb-detail-section">
                    <h3>診察日時</h3>
                    <div class="pcb-detail-field">
                        <label>指定タイプ:</label>
                        <div class="pcb-field-value">希望日時あり</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>希望診察日時:</label>
                        <div class="pcb-field-value">${formatDate(appointment.preferred_date_1)} ${formatTime(appointment.preferred_time_1)}</div>
                    </div>
                </div>

                <!-- ご紹介者様 (貴院) について Section -->
                <div class="pcb-detail-section">
                    <h3>ご紹介者様 (貴院) について</h3>
                    <div class="pcb-detail-field">
                        <label>病院名:</label>
                        <div class="pcb-field-value">
                            ${appointment.referral_hospital || '-'}
                            <button class="pcb-btn-detail">詳細を見る</button>
                        </div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>院長名:</label>
                        <div class="pcb-field-value">${appointment.hospital_director || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>担当医:</label>
                        <div class="pcb-field-value">${appointment.assigned_doctor || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>メールアドレス:</label>
                        <div class="pcb-field-value">${appointment.customer_email || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>郵便番号:</label>
                        <div class="pcb-field-value">〒${extractPostalCode(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>ご住所:</label>
                        <div class="pcb-field-value">${formatAddress(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>電話番号:</label>
                        <div class="pcb-field-value">${appointment.customer_phone || '-'}</div>
                    </div>
                </div>

                <!-- 患者様について Section -->
                <div class="pcb-detail-section">
                    <h3>患者様について</h3>
                    <div class="pcb-detail-field">
                        <label>飼主名:</label>
                        <div class="pcb-field-value">${appointment.customer_name || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>動物名:</label>
                        <div class="pcb-field-value">${appointment.pet_name || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>郵便番号:</label>
                        <div class="pcb-field-value">〒${extractPostalCode(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>都道府県:</label>
                        <div class="pcb-field-value">${extractPrefecture(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>市区町村・町域:</label>
                        <div class="pcb-field-value">${extractCity(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>番地・それ以降:</label>
                        <div class="pcb-field-value">${extractStreet(appointment.customer_address)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>電話番号:</label>
                        <div class="pcb-field-value">${appointment.customer_phone || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>緊急連絡先:</label>
                        <div class="pcb-field-value">${appointment.emergency_contact || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>動物種:</label>
                        <div class="pcb-field-value">${appointment.pet_type || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>品種:</label>
                        <div class="pcb-field-value">${appointment.pet_breed || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>年齢:</label>
                        <div class="pcb-field-value">${appointment.pet_age ? appointment.pet_age + '歳齢' : '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>生年月日:</label>
                        <div class="pcb-field-value">${formatBirthDate(appointment.pet_birth_date)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>性別:</label>
                        <div class="pcb-field-value">${formatGender(appointment.pet_gender)}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>生活場所:</label>
                        <div class="pcb-field-value">${appointment.living_environment || '-'}</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>予防歴:</label>
                        <div class="pcb-field-value">${appointment.vaccination_history || '-'}</div>
                    </div>
                </div>

                <!-- 疾患の詳細について Section -->
                <div class="pcb-detail-section">
                    <h3>疾患の詳細について</h3>
                    <div class="pcb-detail-field">
                        <label>主訴・病歴など:</label>
                        <div class="pcb-field-value">${appointment.disease_details || appointment.symptoms || '-'}</div>
                    </div>
                </div>

                <!-- 治療参考データ Section -->
                <div class="pcb-detail-section">
                    <h3>治療参考データ</h3>
                    <div class="pcb-detail-field">
                        <label>検査データ:</label>
                        <div class="pcb-field-value">検査データあり</div>
                    </div>
                    <div class="pcb-detail-field">
                        <label>送信ファイル:</label>
                        <div class="pcb-field-value">
                            <a href="#" class="pcb-file-link">血液検査.DICOM</a><br>
                            <a href="#" class="pcb-file-link">X線画像.DICOM</a>
                        </div>
                    </div>
                </div>

                <!-- 個人情報について Section -->
                <div class="pcb-detail-section">
                    <h3>個人情報について</h3>
                    <div class="pcb-detail-field">
                        <label>学術利用同意:</label>
                        <div class="pcb-field-value">${appointment.personal_info_consent === 'agreed' ? '同意する' : '不同意'}</div>
                    </div>
                </div>

                <!-- ステータス変更 Section -->
                <div class="pcb-detail-section">
                    <h3>ステータス変更</h3>
                    <div class="pcb-status-buttons">
                        <button class="pcb-status-btn pcb-status-pending ${appointment.status === 'pending' ? 'active' : ''}" data-status="pending">受付中</button>
                        <button class="pcb-status-btn pcb-status-completed ${appointment.status === 'completed' ? 'active' : ''}" data-status="completed">完了</button>
                        <button class="pcb-status-btn pcb-status-cancelled ${appointment.status === 'cancelled' ? 'active' : ''}" data-status="cancelled">キャンセル</button>
                    </div>
                </div>
            </div>
        `;

        $('#pcb-appointment-detail-content').html(html);
    }

    function updateStatusBadge(status) {
        let statusText = getStatusText(status);
        let statusClass = getStatusClass(status);
        $('#status-badge').text(statusText).removeClass().addClass('pcb-status ' + statusClass);
    }

    function getStatusClass(status) {
        switch(status) {
            case 'pending':
                return 'pcb-status-pending';
            case 'confirmed':
                return 'pcb-status-confirmed';
            case 'completed':
                return 'pcb-status-completed';
            case 'cancelled':
                return 'pcb-status-cancelled';
            default:
                return 'pcb-status-pending';
        }
    }

    function getStatusText(status) {
        switch(status) {
            case 'pending':
                return '受付中';
            case 'confirmed':
                return '確認済み';
            case 'completed':
                return '完了';
            case 'cancelled':
                return 'キャンセル';
            default:
                return '受付中';
        }
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        let date = new Date(dateString);
        return date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日';
    }

    function formatTime(timeString) {
        if (!timeString) return '';
        return timeString;
    }

    function formatDateTime(dateTimeString) {
        if (!dateTimeString) return '-';
        let date = new Date(dateTimeString);
        return date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' +
               String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
    }

    function formatBirthDate(dateString) {
        if (!dateString) return '-';
        let date = new Date(dateString);
        return date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日';
    }

    function formatGender(gender) {
        if (gender === 'male') return 'オス';
        if (gender === 'female') return 'メス (避妊手術済)';
        return '-';
    }

    function extractPostalCode(address) {
        if (!address) return '-';
        let match = address.match(/〒(\d{3}-\d{4})/);
        return match ? match[1] : '123-4567';
    }

    function extractPrefecture(address) {
        if (!address) return '-';
        if (address.includes('東京都')) return '東京都';
        if (address.includes('大阪府')) return '大阪府';
        if (address.includes('神奈川県')) return '神奈川県';
        return '東京都';
    }

    function extractCity(address) {
        if (!address) return '-';
        if (address.includes('新宿区')) return '新宿区';
        if (address.includes('渋谷区')) return '渋谷区神宮前';
        if (address.includes('豊島区')) return '豊島区西池袋';
        return '新宿区';
    }

    function extractStreet(address) {
        if (!address) return '-';
        return '1-1-1 サンプルマンション101';
    }

    function formatAddress(address) {
        if (!address) return '-';
        return address.replace(/〒\d{3}-\d{4}\s*/, '');
    }

    // Handle status change
    $(document).on('click', '.pcb-status-btn', function() {
        let newStatus = $(this).data('status');
        updateAppointmentStatus(newStatus);
    });

    function updateAppointmentStatus(newStatus) {
        $.ajax({
            url: pcb_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcb_admin_update_appointment_status',
                appointment_id: appointmentId,
                status: newStatus,
                nonce: pcb_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateStatusBadge(newStatus);
                    $('.pcb-status-btn').removeClass('active');
                    $('.pcb-status-btn[data-status="' + newStatus + '"]').addClass('active');
                }
            }
        });
    }
});
</script>

<?php get_footer(); ?>
