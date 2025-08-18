<?php

// Check permissions
if (!PCB_Permissions::can_view_appointments()) {
    PCB_Permissions::show_access_denied('申し込み一覧');
    return;
}

// Get search parameters from URL
$hospital_name = isset($_GET['hospital_name']) ? sanitize_text_field($_GET['hospital_name']) : '';
$owner_name = isset($_GET['owner_name']) ? sanitize_text_field($_GET['owner_name']) : '';
$doctor_name = isset($_GET['doctor_name']) ? sanitize_text_field($_GET['doctor_name']) : '';
$department = isset($_GET['department']) ? sanitize_text_field($_GET['department']) : '';
$page = isset($_GET['page_number']) ? intval($_GET['page_number']) : 1;
$per_page = 10;

// Build WHERE clause for filtering
$where_conditions = array();
$where_values = array();

if (!empty($hospital_name)) {
    $where_conditions[] = "referrer_clinic_name  LIKE %s";
    $where_values[] = '%' . $hospital_name . '%';
}

if (!empty($owner_name)) {
    $where_conditions[] = "owner_name LIKE %s";
    $where_values[] = '%' . $owner_name . '%';
}

if (!empty($doctor_name)) {
    $where_conditions[] = "(referrer_director_name LIKE %s OR referrer_doctor_name LIKE %s)";
    $where_values[] = '%' . $doctor_name . '%';
    $where_values[] = '%' . $doctor_name . '%';
}

if (!empty($department)) {
    $where_conditions[] = "department LIKE %s";
    $where_values[] = '%' . $department . '%';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count for pagination
global $wpdb;
$table_name = $wpdb->prefix . 'clinic_reservations';

$count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
if (!empty($where_values)) {
    $count_query = $wpdb->prepare($count_query, $where_values);
}
$total_appointments = $wpdb->get_var($count_query);
$total_pages = ceil($total_appointments / $per_page);

// Ensure page is within valid range
if ($page < 1)
    $page = 1;
if ($page > $total_pages && $total_pages > 0)
    $page = $total_pages;

// Calculate offset
$offset = ($page - 1) * $per_page;

// Get appointments with pagination
$query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
$query_values = array_merge($where_values, array($per_page, $offset));
$appointments = $wpdb->get_results($wpdb->prepare($query, $query_values));

// Helper functions
function getStatusClass($status)
{
    switch ($status) {
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

function getStatusText($status)
{
    switch ($status) {
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

function formatDateTime($dateTimeString)
{
    if (empty($dateTimeString))
        return '-';
    $date = new DateTime($dateTimeString);
    return $date->format('Y-m-d H:i:s');
}

function buildPaginationUrl($page, $params)
{
    $params['page_number'] = $page;
    return '?' . http_build_query($params);
}


?>

<div class="pcb-admin-appointments">
    <div class="pcb-admin-header">
        <h1>申し込み一覧</h1>
        <div class="pcb-admin-actions">
            <?php if (PCB_Permissions::can_export_csv()): ?>
                <a href="<?php echo add_query_arg(array_merge($_GET, array('download' => 'csv')), remove_query_arg('page')); ?>"
                    class="pcb-btn pcb-btn-csv">この一覧をCSVでダウンロード</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search/Filter Section -->
    <div class="pcb-search-section">
        <form method="GET" action="">
            <input type="hidden" name="page" value="pcb-appointments">

            <div class="pcb-search-grid">
                <div class="pcb-search-item">
                    <label for="hospital_name">病院名:</label>
                    <input type="text" id="hospital_name" name="hospital_name"
                        value="<?php echo esc_attr($hospital_name); ?>" placeholder="紹介病院名で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="owner_name">飼主名:</label>
                    <input type="text" id="owner_name" name="owner_name" value="<?php echo esc_attr($owner_name); ?>"
                        placeholder="飼主名で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="doctor_name">院長名/担当医:</label>
                    <input type="text" id="doctor_name" name="doctor_name" value="<?php echo esc_attr($doctor_name); ?>"
                        placeholder="院長名/担当医で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="department">診療科:</label>
                    <input type="text" id="department" name="department" value="<?php echo esc_attr($department); ?>"
                        placeholder="診療科で検索">
                </div>
                <div class="pcb-search-item">
                    <button type="submit" class="pcb-btn pcb-btn-primary">検索</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="pcb-table-container">
        <table class="pcb-appointments-table">
            <thead>
                <tr>
                    <th>詳細</th>
                    <th>受付日時</th>
                    <th>予約希望日時</th>
                    <th>紹介病院名</th>
                    <th>飼主名</th>
                    <th>動物名/種</th>
                    <th>診療科</th>
                    <th>電話番号</th>
                    <th>ステータス</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr>
                        <td colspan="9" class="pcb-empty">データが見つかりません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=pcb-appointment-detail&appointment_id=' . $appointment->id); ?>"
                                    class="pcb-detail-link">表示</a>
                            </td>
                            <td><?php echo formatDateTime($appointment->created_at); ?></td>
                            <td><?php echo formatDateTime($appointment->first_choice_date . ' ' . $appointment->first_choice_time); ?>
                            </td>
                            <td class="text-bold"><?php echo esc_html($appointment->referrer_clinic_name ?: '-'); ?></td>
                            <td class="text-bold"><?php echo esc_html($appointment->owner_name); ?></td>
                            <td><?php echo esc_html($appointment->pet_name . '/' . $appointment->animal_type); ?></td>
                            <td><?php echo esc_html($appointment->department); ?></td>
                            <td><?php echo esc_html($appointment->owner_phone); ?></td>
                            <td>
                                <span class="pcb-status <?php echo getStatusClass($appointment->status); ?>">
                                    <?php echo getStatusText($appointment->status); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pcb-pagination">
            <?php
            $current_params = $_GET;

            // Previous page
            if ($page > 1): ?>
                <a href="<?php echo buildPaginationUrl($page - 1, $current_params); ?>"
                    class="pcb-page-btn pcb-page-btn-prev pcb-page-inactive">&laquo; 前へ</a>
            <?php endif; ?>

            <?php
            // Page numbers
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="<?php echo buildPaginationUrl($i, $current_params); ?>"
                    class="pcb-page-btn <?php echo $i === $page ? 'pcb-page-active' : 'pcb-page-inactive'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php // Next page
                if ($page < $total_pages): ?>
                <a href="<?php echo buildPaginationUrl($page + 1, $current_params); ?>"
                    class="pcb-page-btn pcb-page-btn-next pcb-page-inactive">次へ &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
