<?php
/**
 * Template for adding new doctor
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check permissions
if (!PCB_Permissions::can_add_doctors()) {
    PCB_Permissions::show_access_denied('ドクター追加');
    return;
}

// Handle form submission
if ($_POST && isset($_POST['pcb_add_doctor'])) {
    $name = sanitize_text_field($_POST['name']);
    $specialization = sanitize_text_field($_POST['specialization']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $status = sanitize_text_field($_POST['status']);
    
    global $wpdb;
    $table_doctors = $wpdb->prefix . 'pcb_doctors';
    
    $result = $wpdb->insert($table_doctors, array(
        'name' => $name,
        'specialization' => $specialization,
        'phone' => $phone,
        'email' => $email,
        'status' => $status,
        'created_at' => current_time('mysql')
    ));
    
    if ($result) {
        $success_message = 'ドクターが正常に追加されました。';
    } else {
        $error_message = 'エラーが発生しました。もう一度お試しください。';
    }
}
?>

<div class="pcb-admin-container">
    <div class="pcb-header">
        <h1>ドクター追加</h1>
        <div class="pcb-header-actions">
            <a href="<?php echo admin_url('admin.php?page=pcb-doctors'); ?>" class="pcb-btn-secondary">一覧に戻る</a>
        </div>
    </div>

    <div class="pcb-content">
        <?php if (isset($success_message)): ?>
            <div class="pcb-success">
                <p><?php echo esc_html($success_message); ?></p>
                <a href="<?php echo admin_url('admin.php?page=pcb-doctors'); ?>" class="pcb-btn-primary">ドクター一覧を見る</a>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="pcb-error">
                <p><?php echo esc_html($error_message); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" class="pcb-form">
            <div class="pcb-form-section">
                <h3>基本情報</h3>
                
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="name">名前 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? esc_attr($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="pcb-form-group">
                        <label for="specialization">専門分野 <span class="required">*</span></label>
                        <input type="text" id="specialization" name="specialization" required value="<?php echo isset($_POST['specialization']) ? esc_attr($_POST['specialization']) : ''; ?>">
                    </div>
                </div>
                
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="phone">電話番号 <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>">
                    </div>
                    
                    <div class="pcb-form-group">
                        <label for="email">メールアドレス <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="pcb-form-row">
                    <div class="pcb-form-group">
                        <label for="status">ステータス</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>アクティブ</option>
                            <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>非アクティブ</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="pcb-form-actions">
                <button type="submit" name="pcb_add_doctor" class="pcb-btn-primary">ドクター追加</button>
                <a href="<?php echo admin_url('admin.php?page=pcb-doctors'); ?>" class="pcb-btn-secondary">キャンセル</a>
            </div>
        </form>
    </div>
</div>

<style>
.pcb-admin-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
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

.pcb-btn-primary {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
}

.pcb-btn-primary:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
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
    padding: 30px;
}

.pcb-success {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.pcb-error {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.pcb-form-section {
    margin-bottom: 30px;
}

.pcb-form-section h3 {
    color: #333;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.pcb-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.pcb-form-group {
    display: flex;
    flex-direction: column;
}

.pcb-form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.pcb-form-group input,
.pcb-form-group select {
    padding: 12px 16px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.pcb-form-group input:focus,
.pcb-form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.required {
    color: #dc3545;
}

.pcb-form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-start;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .pcb-form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .pcb-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .pcb-form-actions {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
