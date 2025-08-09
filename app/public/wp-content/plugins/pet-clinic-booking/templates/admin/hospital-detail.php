<?php
/**
 * Hospital Detail Template
 * Template for displaying detailed hospital information
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check permissions
if (!PCB_Permissions::can_view_hospital_detail()) {
    PCB_Permissions::show_access_denied('病院詳細');
    return;
}

// Get appointment ID from URL parameter
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if (!$appointment_id) {
    echo '<div class="wrap"><h1>エラー</h1><p>申し込みIDが指定されていません。</p></div>';
    return;
}

// Get plugin instance to access helper functions
$plugin = new PetClinicBooking();

// Get appointment and doctor data
$appointment_data = $plugin->get_appointment_detail($appointment_id);
$doctor_id = $appointment_data ? $appointment_data->doctor_id : 0;
$doctor_data = $doctor_id ? $plugin->get_doctor_detail($doctor_id) : null;

// Get hospital data from appointment
$hospital_data = $plugin->get_hospital_data_from_appointment($appointment_data);

// If no appointment found, show error
if (!$appointment_data) {
    echo '<div class="wrap"><h1>エラー</h1><p>指定された申し込みが見つかりません。</p></div>';
    return;
}
?>

<div class="pcb-admin-container">
    <div class="pcb-header">
        <h1>ドクター登録名簿 (詳細)</h1>
        <div class="pcb-header-actions">
            <a href="<?php echo admin_url('admin.php?page=pcb-appointment-detail&appointment_id=' . $appointment_id); ?>" class="pcb-btn-secondary">一覧に戻る</a>
        </div>
    </div>

    <div class="pcb-content">
        <div class="pcb-detail-card">
            <div class="pcb-detail-section">
                <h3>基本情報</h3>
                <h4>ドクター情報</h4>
                
                <div class="pcb-detail-grid">
                    <!-- Left Column -->
                    <div class="pcb-detail-column">
                        <div class="pcb-detail-field">
                            <label>病院名:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['clinic_name']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>院長名:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['director_name']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>担当医:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['doctor_in_charge']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>メールアドレス:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['email']); ?></div>
                        </div>
                        
<!--                        <div class="pcb-detail-field">-->
<!--                            <label>メールアドレス(確認):</label>-->
<!--                            <div class="pcb-field-value">--><?php //echo esc_html($hospital_data['email_confirm']); ?><!--</div>-->
<!--                        </div>-->
                        
                        <div class="pcb-detail-field">
                            <label>郵便番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['postal_code']); ?></div>
                        </div>
                        
<!--                        <div class="pcb-detail-field">-->
<!--                            <label>パスワード:</label>-->
<!--                            <div class="pcb-field-value">--><?php //echo esc_html($hospital_data['password']); ?><!--</div>-->
<!--                        </div>-->
                    </div>
                    
                    <!-- Right Column -->
                    <div class="pcb-detail-column">
                        <div class="pcb-detail-field">
                            <label>都道府県:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['prefecture']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>市区町村・町域:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['city_address']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>番地・それ以降の住所:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['building_name']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>電話番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['phone_number']); ?></div>
                            <div class="pcb-field-note">電話番号はハイフンあり・なしどちらでも登録できます</div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>緊急連絡先 電話番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['emergency_phone_number']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>FAX番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['fax_number']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pcb-admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: 100vh;
}

.pcb-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pcb-header h1 {
    margin: 0;
    color: #333;
    font-size: 24px;
    font-weight: 600;
}

.pcb-btn-secondary {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.3s;
}

.pcb-btn-secondary:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}

.pcb-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.pcb-detail-section h3 {
    color: #333;
    font-size: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.pcb-detail-section h4 {
    color: #495057;
    font-size: 16px;
    margin-bottom: 20px;
    font-weight: 600;
}

.pcb-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}


.pcb-detail-field {
    display: block;
}

.pcb-detail-field label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.pcb-field-value {
    padding: 12px 16px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #333;
    font-size: 14px;
    min-height: 20px;
}

.pcb-field-note {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
    font-style: italic;
}

@media (max-width: 768px) {
    .pcb-detail-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .pcb-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

}
</style> 