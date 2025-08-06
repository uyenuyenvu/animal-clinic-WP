<?php
// Get appointment ID from URL parameter
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if (!$appointment_id) {
    echo '<div class="wrap"><h1>エラー</h1><p>申し込みIDが指定されていません。</p></div>';
    return;
}

// Get appointment data
global $wpdb;
$table_name = $wpdb->prefix . 'pcb_bookings';

$appointment = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE id = %d",
    $appointment_id
));

if (!$appointment) {
    echo '<div class="wrap"><h1>エラー</h1><p>指定された申し込みが見つかりません。</p></div>';
    return;
}

// Helper functions
function getStatusClass($status) {
    switch($status) {
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

function getStatusText($status) {
    switch($status) {
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

function formatDateTime($dateTimeString) {
    if (empty($dateTimeString)) return '-';
    $date = new DateTime($dateTimeString);
    return $date->format('Y-m-d H:i:s');
}

function formatDate($dateString) {
    if (empty($dateString)) return '-';
    $date = new DateTime($dateString);
    return $date->format('Y-m-d');
}

function formatTime($timeString) {
    if (empty($timeString)) return '-';
    $time = new DateTime($timeString);
    return $time->format('H:i');
}
?>

<div class="wrap pcb-admin-appointment-detail">
    <div class="pcb-detail-header">
        <h1>申し込み詳細 #<?php echo $appointment->booking_id; ?></h1>
        <div class="pcb-detail-nav">
            <a href="<?php echo admin_url('admin.php?page=pcb-appointments'); ?>" class="pcb-btn-back">← 一覧に戻る</a>
            <div class="pcb-detail-info">
                <span class="pcb-info-item"><strong>申し込みID:</strong> <?php echo esc_html($appointment->booking_id); ?></span>
                <span class="pcb-info-item"><strong>ステータス:</strong> <span class="pcb-status <?php echo getStatusClass($appointment->status); ?>"><?php echo getStatusText($appointment->status); ?></span></span>
                <span class="pcb-info-item"><strong>受付日時:</strong> <?php echo formatDateTime($appointment->created_at); ?></span>
            </div>
        </div>
    </div>

    <div class="pcb-detail-sections">
        <!-- 予約情報 Section -->
        <div class="pcb-detail-section">
            <h3>予約情報</h3>
            <div class="pcb-detail-field">
                <label>診療科:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->department); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>第1希望日:</label>
                <div class="pcb-field-value"><?php echo formatDate($appointment->preferred_date_1); ?> <?php echo formatTime($appointment->preferred_time_1); ?></div>
            </div>
            <?php if ($appointment->preferred_date_2): ?>
            <div class="pcb-detail-field">
                <label>第2希望日:</label>
                <div class="pcb-field-value"><?php echo formatDate($appointment->preferred_date_2); ?> <?php echo formatTime($appointment->preferred_time_2); ?></div>
            </div>
            <?php endif; ?>
            <div class="pcb-detail-field">
                <label>確定予約日:</label>
                <div class="pcb-field-value"><?php echo formatDate($appointment->appointment_date); ?> <?php echo formatTime($appointment->appointment_time); ?></div>
            </div>
        </div>

        <!-- 紹介病院情報 Section -->
        <div class="pcb-detail-section">
            <h3>紹介病院情報</h3>
            <div class="pcb-detail-field">
                <label>紹介病院名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->referral_hospital ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>院長名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->hospital_director ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>担当医:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->assigned_doctor ?: '-'); ?></div>
            </div>
        </div>

        <!-- 飼主情報 Section -->
        <div class="pcb-detail-section">
            <h3>飼主情報</h3>
            <div class="pcb-detail-field">
                <label>飼主名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->customer_name); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>住所:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->customer_address); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>電話番号:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->customer_phone); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>メールアドレス:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->customer_email); ?></div>
            </div>
            <?php if ($appointment->emergency_contact): ?>
            <div class="pcb-detail-field">
                <label>緊急連絡先:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->emergency_contact); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 動物情報 Section -->
        <div class="pcb-detail-section">
            <h3>動物情報</h3>
            <div class="pcb-detail-field">
                <label>動物名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_name); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>動物種:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_type); ?></div>
            </div>
            <?php if ($appointment->pet_breed): ?>
            <div class="pcb-detail-field">
                <label>品種:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_breed); ?></div>
            </div>
            <?php endif; ?>
            <div class="pcb-detail-field">
                <label>年齢:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_age); ?>ヶ月</div>
            </div>
            <?php if ($appointment->pet_birth_date): ?>
            <div class="pcb-detail-field">
                <label>生年月日:</label>
                <div class="pcb-field-value"><?php echo formatDate($appointment->pet_birth_date); ?></div>
            </div>
            <?php endif; ?>
            <div class="pcb-detail-field">
                <label>性別:</label>
                <div class="pcb-field-value"><?php echo $appointment->pet_gender === 'male' ? 'オス' : 'メス'; ?></div>
            </div>
            <?php if ($appointment->pet_weight): ?>
            <div class="pcb-detail-field">
                <label>体重:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_weight); ?>kg</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 生活環境・病歴 Section -->
        <div class="pcb-detail-section">
            <h3>生活環境・病歴</h3>
            <?php if ($appointment->living_environment): ?>
            <div class="pcb-detail-field">
                <label>生活場所:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->living_environment)); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($appointment->vaccination_history): ?>
            <div class="pcb-detail-field">
                <label>ワクチン接種歴:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->vaccination_history)); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($appointment->disease_details): ?>
            <div class="pcb-detail-field">
                <label>既往症:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->disease_details)); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 症状・治療情報 Section -->
        <div class="pcb-detail-section">
            <h3>症状・治療情報</h3>
            <?php if ($appointment->symptoms): ?>
            <div class="pcb-detail-field">
                <label>症状:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->symptoms)); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($appointment->treatment_reference_data): ?>
            <div class="pcb-detail-field">
                <label>治療参考資料:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->treatment_reference_data)); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- その他情報 Section -->
        <div class="pcb-detail-section">
            <h3>その他情報</h3>
            <div class="pcb-detail-field">
                <label>個人情報同意:</label>
                <div class="pcb-field-value"><?php echo $appointment->personal_info_consent === 'agreed' ? '同意' : '不同意'; ?></div>
            </div>
            <?php if ($appointment->notes): ?>
            <div class="pcb-detail-field">
                <label>備考:</label>
                <div class="pcb-field-value"><?php echo nl2br(esc_html($appointment->notes)); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ステータス更新 Section -->
        <div class="pcb-detail-section">
            <h3>ステータス更新</h3>
            <div class="pcb-status-buttons">
                <button class="pcb-status-btn pcb-status-pending <?php echo $appointment->status === 'pending' ? 'active' : ''; ?>" data-status="pending">受付中</button>
                <button class="pcb-status-btn pcb-status-confirmed <?php echo $appointment->status === 'confirmed' ? 'active' : ''; ?>" data-status="confirmed">確認済み</button>
                <button class="pcb-status-btn pcb-status-completed <?php echo $appointment->status === 'completed' ? 'active' : ''; ?>" data-status="completed">完了</button>
                <button class="pcb-status-btn pcb-status-cancelled <?php echo $appointment->status === 'cancelled' ? 'active' : ''; ?>" data-status="cancelled">キャンセル</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle status update
    $('.pcb-status-btn').on('click', function() {
        var status = $(this).data('status');
        var appointmentId = <?php echo $appointment_id; ?>;
        
        if (confirm('ステータスを「' + $(this).text() + '」に変更しますか？')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pcb_admin_update_appointment_status',
                    appointment_id: appointmentId,
                    status: status,
                    nonce: '<?php echo wp_create_nonce('pcb_admin_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('ステータスが更新されました。');
                        location.reload();
                    } else {
                        alert('エラーが発生しました: ' + response.data);
                    }
                },
                error: function() {
                    alert('エラーが発生しました。');
                }
            });
        }
    });
});
</script>
