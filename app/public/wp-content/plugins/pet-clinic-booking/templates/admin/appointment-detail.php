<?php
// Check permissions
if (!PCB_Permissions::can_view_appointment_detail()) {
    PCB_Permissions::show_access_denied('申し込み詳細');
    return;
}

// Get appointment ID from URL parameter
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if (!$appointment_id) {
    echo '<div class="wrap"><h1>エラー</h1><p>申し込みIDが指定されていません。</p></div>';
    return;
}

// Get appointment data
global $wpdb;
$table_name = $wpdb->prefix . 'clinic_reservations';

$appointment = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $appointment_id
));

if (!$appointment) {
    echo '<div class="wrap"><h1>エラー</h1><p>指定された申し込みが見つかりません。</p></div>';
    return;
}

function buildUrlBack()
{
    $params = array_merge($_GET, ['page' => 'pcb-appointments']);
    unset($params['appointment_id']);
    $stringParams = http_build_query($params);
    return admin_url('admin.php?' . $stringParams);
}

function buildUrlHospitalDetail($appointment_id)
{
    $params = array_merge($_GET, ['appointment_id' => $appointment_id]);
    $params['page'] = 'pcb-hospital-detail';
    $stringParams = http_build_query($params);
    return admin_url('admin.php?' . $stringParams);
}

// Helper functions
function getStatusClass($status): string
{
    return match ($status) {
        2, 'temporary' => 'pcb-status-temporary',
        3, 'confirmed' => 'pcb-status-confirmed',
        4, 'cancelled' => 'pcb-status-cancelled',
        5, 'changing' => 'pcb-status-changing',
        default => 'pcb-status-pending',
    };
}

function getStatusText($status): string
{
    return match ($status) {
        'pending', 1 => '受付中',
        'temporary', 2 => '仮予約',
        'confirmed', 3 => '予約確定',
        'cancelled', 4 => 'キャンセル済',
        'changing', 5 => '変更依頼中',
        default => '不明な状態',
    };
}


function formatDateTime($dateTimeString)
{
    if (empty($dateTimeString)) return '-';
    $date = new DateTime($dateTimeString);
    return $date->format('Y-m-d H:i:s');
}

function formatDate($dateString)
{
    if (empty($dateString)) return '-';
    $date = new DateTime($dateString);
    return $date->format('Y-m-d');
}

function formatTime($timeString)
{
    if (empty($timeString)) return '-';
    $time = new DateTime($timeString);
    return $time->format('H:i');
}

function formatBirthDate($dateString)
{
    if (empty($dateString)) return '-';
    $date = new DateTime($dateString);
    return $date->format('Y年m月d日');
}

function formatGender($gender)
{
    return getMasterCodeLabel('gender', $gender);
}

function extractPostalCode($address)
{
    if (empty($address)) return '-';
    if (preg_match('/〒(\d{3}-\d{4})/', $address, $matches)) {
        return $matches[1];
    }
    return '123-4567';
}

function extractPrefecture($address)
{
    if (empty($address)) return '-';
    if (strpos($address, '東京都') !== false) return '東京都';
    if (strpos($address, '大阪府') !== false) return '大阪府';
    if (strpos($address, '神奈川県') !== false) return '神奈川県';
    return '東京都';
}

function extractCity($address)
{
    if (empty($address)) return '-';
    if (strpos($address, '新宿区') !== false) return '新宿区';
    if (strpos($address, '渋谷区') !== false) return '渋谷区神宮前';
    if (strpos($address, '豊島区') !== false) return '豊島区西池袋';
    return '新宿区';
}

function extractStreet($address)
{
    if (empty($address)) return '-';
    return '1-1-1 サンプルマンション101';
}

function formatAddress($address)
{
    if (empty($address)) return '-';
    return preg_replace('/〒\d{3}-\d{4}\s*/', '', $address);
}

function getMasterCodeLabel($code, $value)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'clinic_masters';

    $masterCode = $wpdb->get_row(
            $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE code = %s AND value = %s",
                    $code,
                    $value
            )
    );

    if ($masterCode) {
        return $masterCode->label;
    }

    return '';
}


?>

<div class="wrap pcb-admin-appointment-detail">
    <div class="pcb-detail-header">
        <div class="pcb-detail-header-title">
            <h1>申し込み詳細 #<?php echo $appointment->code; ?></h1>
        </div>
        <div class="pcb-detail-nav">
            <a href="<?php echo buildUrlBack(); ?>" class="pcb-btn-back">一覧に戻る</a>
            <div class="pcb-detail-info">
                <span class="pcb-info-item"><strong>申し込みID:</strong> <?php echo esc_html($appointment->code); ?></span>
                <span class="pcb-info-item"><strong>受付日時:</strong> <?php echo formatDateTime($appointment->created_at); ?></span>
                <span class="pcb-info-item"><strong>ステータス:</strong> <span
                            class="pcb-status <?php echo getStatusClass($appointment->status); ?>"><?php echo getStatusText($appointment->status); ?></span></span>
            </div>
        </div>
    </div>

    <div class="pcb-detail-sections">
        <!-- 診療科 Section -->
        <div class="pcb-detail-section">
            <h3>診療科</h3>
            <div class="pcb-detail-field">
                <label>選択された診療科:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->department); ?></div>
            </div>
        </div>

        <!-- 診察日時 Section -->
        <div class="pcb-detail-section">
            <h3>診察日時</h3>
            <div class="pcb-detail-field">
                <label>指定タイプ:</label>
                <div class="pcb-field-value"><?= getMasterCodeLabel('schedule_type', $appointment->schedule_type)?></div>
            </div>
            <?php if ($appointment->schedule_type == "reservation_confirmed"): ?>
                <div class="pcb-detail-field">
                    <label>確定予約日時:</label>
                    <div class="pcb-field-value"><?php echo formatDate($appointment->confirmed_date); ?><?php echo formatTime($appointment->confirmed_time); ?></div>
                </div>
            <?php endif; ?>


            <?php if ($appointment->schedule_type = "preferred_datetime"): ?>
                <?php if ($appointment->first_choice_date): ?>
                    <div class="pcb-detail-field">
                        <label>第1希望日時:</label>
                        <div class="pcb-field-value"><?php echo formatDate($appointment->first_choice_date); ?><?php echo formatTime($appointment->first_choice_time); ?></div>
                    </div>
                <?php endif; ?>
                <?php if ($appointment->second_choice_date): ?>
                    <div class="pcb-detail-field">
                        <label>第2希望日時:</label>
                        <div class="pcb-field-value"><?php echo formatDate($appointment->second_choice_date); ?><?php echo formatTime($appointment->second_choice_time); ?></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>

        <!-- ご紹介者様 (貴院) について Section -->
        <div class="pcb-detail-section">
            <h3>ご紹介者様 (貴院) について</h3>
            <div class="pcb-detail-field">
                <label>病院名:</label>
                <div class="pcb-field-value">
                    <?php echo esc_html($appointment->referrer_clinic_name ?: '-'); ?>
                    <a href="<?php echo buildUrlHospitalDetail($appointment_id); ?>"
                       class="pcb-btn-detail">詳細を見る</a>
                </div>
            </div>
            <div class="pcb-detail-field">
                <label>院長名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->referrer_director_name ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>担当医:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->referrer_doctor_name ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>メールアドレス:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->referrer_email); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>郵便番号:</label>
                <div class="pcb-field-value">〒<?php echo extractPostalCode($appointment->referrer_postal_code); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>ご住所:</label>
                <div class="pcb-field-value"><?php echo formatAddress($appointment->referrer_address); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>電話番号:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->referrer_phone); ?></div>
            </div>
        </div>

        <!-- 患者様について Section -->
        <div class="pcb-detail-section">
            <h3>患者様について</h3>
            <div class="pcb-detail-field">
                <label>飼主名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->owner_name); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>動物名:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->pet_name); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>郵便番号:</label>
                <div class="pcb-field-value">〒<?php echo extractPostalCode($appointment->owner_postal_code); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>都道府県:</label>
                <div class="pcb-field-value"><?php echo extractPrefecture($appointment->owner_city); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>市区町村・町域:</label>
                <div class="pcb-field-value"><?php echo extractCity($appointment->owner_prefecture); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>番地・それ以降:</label>
                <div class="pcb-field-value"><?php echo extractStreet($appointment->owner_address_detail); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>電話番号:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->owner_phone); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>緊急連絡先:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->owner_emergency_phone ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>動物種:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->animal_type); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>品種:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->breed ?: '-'); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>年齢:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->age); ?>ヶ月</div>
            </div>
            <div class="pcb-detail-field">
                <label>生年月日:</label>
                <div class="pcb-field-value"><?php echo formatBirthDate($appointment->birth_date); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>性別:</label>
                <div class="pcb-field-value"><?php echo formatGender($appointment->gender); ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>生活場所:</label>
                <div class="pcb-field-value"><?php echo getMasterCodeLabel('living_environment', $appointment->living_environment) ?></div>
            </div>
            <div class="pcb-detail-field">
                <label>予防歴:</label>
                <div class="pcb-field-value"><?php echo getMasterCodeLabel('preventive_history', $appointment->preventive_history) ?></div>
            </div>
        </div>

        <!-- 疾患の詳細について Section -->
        <div class="pcb-detail-section">
            <h3>疾患の詳細について</h3>
            <div class="pcb-detail-field">
                <label>主訴・病歴など:</label>
                <div class="pcb-field-value"><?php echo esc_html($appointment->medical_details ? getMasterCodeLabel('treatment_reference_data', $appointment->treatment_reference_data) : '-'); ?></div>
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
                    <a href="<?php echo esc_url($appointment->test_data_file_path); ?>"
                       class="pcb-file-link"
                       download>
                        <?php echo isset($appointment->test_data_file_path) ? basename($appointment->test_data_file_path) : ""; ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- 個人情報について Section -->
        <div class="pcb-detail-section">
            <h3>個人情報について</h3>
            <div class="pcb-detail-field">
                <label>送信ファイル:</label>

                <div class="pcb-field-value"><?php echo getMasterCodeLabel('academic_publication_consent', $appointment->academic_publication_consent) ?></div>
            </div>
        </div>

        <!-- ステータス変更 Section -->
        <div class="pcb-detail-section">
            <h3>ステータス変更</h3>
            <div class="pcb-status-buttons">
                <button class="pcb-status-btn pcb-status-pending <?php echo $appointment->status === 'pending' ? 'active' : ''; ?>"
                        data-status="pending">受付中
                </button>

                <button class="pcb-status-btn pcb-status-temporary <?php echo $appointment->status === 'temporary' ? 'active' : ''; ?>"
                        data-status="temporary">仮予約
                </button>

                <button class="pcb-status-btn pcb-status-confirmed <?php echo $appointment->status === 'confirmed' ? 'active' : ''; ?>"
                        data-status="confirmed">予約確定
                </button>

                <button class="pcb-status-btn pcb-status-cancelled <?php echo $appointment->status === 'cancelled' ? 'active' : ''; ?>"
                        data-status="cancelled">キャンセル済
                </button>

                <button class="pcb-status-btn pcb-status-changing <?php echo $appointment->status === 'changing' ? 'active' : ''; ?>"
                        data-status="changing">変更依頼中
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Handle status update
        $('.pcb-status-btn').on('click', function () {
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
                    success: function (response) {
                        if (response.success) {
                            alert('ステータスが更新されました。');
                            location.reload();
                        } else {
                            alert('エラーが発生しました: ' + response.data);
                        }
                    },
                    error: function () {
                        alert('エラーが発生しました。');
                    }
                });
            }
        });
    });
</script>
