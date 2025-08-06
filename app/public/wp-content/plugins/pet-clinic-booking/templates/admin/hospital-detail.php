<?php
/**
 * Hospital Detail Template
 * Template for displaying detailed hospital information
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
// Get appointment ID from URL parameter
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if (!$appointment_id) {
    echo '<div class="wrap"><h1>エラー</h1><p>申し込みIDが指定されていません。</p></div>';
    return;
}

// Sample hospital data (temporary)
$hospital_data = array(
    'hospital_name' => 'サンプル動物病院',
    'director_name' => '山田 太郎',
    'doctor_in_charge' => '佐藤 花子',
    'email' => 'yamada@example.com',
    'email_confirm' => 'yamada@example.com',
    'postal_code' => '〒100-0001',
    'password' => '**********',
    'prefecture' => '東京都',
    'city' => '千代田区',
    'street_address' => '1-1-1 サンプルビル 3F',
    'phone_number' => '03-1234-5678',
    'emergency_phone' => '03-8765-4321',
    'fax_number' => '03-1234-5679'
);
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
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['hospital_name']); ?></div>
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
                        
                        <div class="pcb-detail-field">
                            <label>メールアドレス(確認):</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['email_confirm']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>郵便番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['postal_code']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>パスワード:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['password']); ?></div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="pcb-detail-column">
                        <div class="pcb-detail-field">
                            <label>都道府県:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['prefecture']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>市区町村・町域:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['city']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>番地・それ以降の住所:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['street_address']); ?></div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>電話番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['phone_number']); ?></div>
                            <div class="pcb-field-note">電話番号はハイフンあり・なしどちらでも登録できます</div>
                        </div>
                        
                        <div class="pcb-detail-field">
                            <label>緊急連絡先 電話番号:</label>
                            <div class="pcb-field-value"><?php echo esc_html($hospital_data['emergency_phone']); ?></div>
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


.pcb-detail-section {
    margin-bottom: 30px;
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
    
    .pcb-detail-card {
        padding: 20px;
    }
}
</style> 