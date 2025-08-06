<?php
/**
 * Template for admin doctors list
 * Get data using PHP instead of AJAX
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check user permissions
if (!current_user_can('manage_options')) {
    wp_die('Không có quyền truy cập.');
}

// Get doctors data from database
global $wpdb;
$table_doctors = $wpdb->prefix . 'pcb_doctors';
$doctors = $wpdb->get_results("SELECT * FROM $table_doctors ORDER BY created_at DESC");

// Helper function to get status text
function getStatusText($status) {
    switch($status) {
        case 'active':
            return '有効';
        case 'inactive':
            return '無効';
        default:
            return $status;
    }
}

// Helper function to get status class
function getStatusClass($status) {
    return 'pcb-status-' . $status;
}

// Helper function to format date
function formatDate($dateString) {
    return date('d/m/Y', strtotime($dateString));
}
?>

<div class="pcb-admin-container">
    <div class="pcb-header">
        <h1>ドクター一覧</h1>
        <div class="pcb-header-actions">
            <a href="<?php echo admin_url('admin.php?page=pcb-add-doctor'); ?>" class="pcb-btn-primary">ドクター追加</a>
        </div>
    </div>

    <div class="pcb-content">
        <?php if (empty($doctors)): ?>
            <div class="pcb-empty">
                <p>まだドクターが登録されていません。</p>
                <a href="<?php echo admin_url('admin.php?page=pcb-add-doctor'); ?>" class="pcb-btn-primary">最初のドクターを追加</a>
            </div>
        <?php else: ?>
            <div class="pcb-doctors-grid">
                <?php foreach ($doctors as $doctor): ?>
                    <div class="pcb-doctor-card">
                        <div class="pcb-doctor-header">
                            <h3><?php echo esc_html($doctor->name); ?></h3>
                            <span class="pcb-status <?php echo getStatusClass($doctor->status); ?>">
                                <?php echo getStatusText($doctor->status); ?>
                            </span>
                        </div>
                        
                        <div class="pcb-doctor-details">
                            <div class="pcb-detail-field">
                                <label>専門分野:</label>
                                <div class="pcb-field-value"><?php echo esc_html($doctor->specialization); ?></div>
                            </div>
                            
                            <div class="pcb-detail-field">
                                <label>電話番号:</label>
                                <div class="pcb-field-value"><?php echo esc_html($doctor->phone); ?></div>
                            </div>
                            
                            <div class="pcb-detail-field">
                                <label>メールアドレス:</label>
                                <div class="pcb-field-value"><?php echo esc_html($doctor->email); ?></div>
                            </div>
                            
                            <div class="pcb-detail-field">
                                <label>登録日:</label>
                                <div class="pcb-field-value"><?php echo formatDate($doctor->created_at); ?></div>
                            </div>
                        </div>
                        
                        <div class="pcb-doctor-actions">
                            <a href="<?php echo admin_url('admin.php?page=pcb-edit-doctor&doctor_id=' . $doctor->id); ?>" class="pcb-btn-secondary">編集</a>
                            <button class="pcb-btn-danger" onclick="deleteDoctor(<?php echo $doctor->id; ?>)">削除</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteDoctor(doctorId) {
    if (confirm('このドクターを削除してもよろしいですか？')) {
        // TODO: Implement delete functionality with AJAX
        alert('削除機能は後で実装されます。');
    }
}
</script>

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
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
    margin-right: 8px;
}

.pcb-btn-secondary:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}

.pcb-btn-danger {
    background: #dc3545;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
}

.pcb-btn-danger:hover {
    background: #c82333;
    color: white;
}

.pcb-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    padding: 30px;
}

.pcb-empty {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.pcb-empty p {
    font-size: 18px;
    margin-bottom: 20px;
}

.pcb-doctors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.pcb-doctor-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    transition: box-shadow 0.3s ease;
}

.pcb-doctor-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.pcb-doctor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.pcb-doctor-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.pcb-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.pcb-status-active {
    background: #d4edda;
    color: #155724;
}

.pcb-status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.pcb-doctor-details {
    margin-bottom: 15px;
}

.pcb-detail-field {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.pcb-detail-field:last-child {
    border-bottom: none;
}

.pcb-detail-field label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    min-width: 100px;
}

.pcb-field-value {
    color: #333;
    font-size: 14px;
    text-align: right;
    flex: 1;
}

.pcb-doctor-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .pcb-doctors-grid {
        grid-template-columns: 1fr;
    }
    
    .pcb-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .pcb-doctor-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .pcb-detail-field {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .pcb-field-value {
        text-align: left;
    }
}
</style>
