<?php
/**
 * CSV Export Handler for Pet Clinic Booking Plugin
 */

class PCB_CSV_Handler {
    
    /**
     * Initialize CSV handler
     */
    public function __construct() {
        add_action('init', array($this, 'handle_csv_download'), 1);
    }
    
    /**
     * Handle CSV download request
     */
    public function handle_csv_download() {
        // Check if this is a CSV download request
        if (!isset($_GET['page']) || $_GET['page'] !== 'pcb-appointments') {
            return;
        }
        
        if (!isset($_GET['download']) || $_GET['download'] !== 'csv') {
            return;
        }

        if (!is_user_logged_in()) {
            auth_redirect(); // Redirect to login if not logged in
        }
        
        // Check permissions
        if (!PCB_Permissions::can_export_csv()) {
            wp_die('Không có quyền xuất CSV.');
        }
        
        // Prevent any output before headers
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Get search parameters
        $hospital_name = isset($_GET['hospital_name']) ? sanitize_text_field($_GET['hospital_name']) : '';
        $owner_name = isset($_GET['owner_name']) ? sanitize_text_field($_GET['owner_name']) : '';
        $doctor_name = isset($_GET['doctor_name']) ? sanitize_text_field($_GET['doctor_name']) : '';
        $department = isset($_GET['department']) ? sanitize_text_field($_GET['department']) : '';
        
        // Build WHERE clause for filtering
        global $wpdb;
        $table_name = $wpdb->prefix . 'pcb_bookings';
        
        $where_conditions = array();
        $where_values = array();
        
        if (!empty($hospital_name)) {
            $where_conditions[] = "referrer_clinic_name LIKE %s";
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
        
        // Get all appointments for CSV (without pagination)
        $csv_query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC";
        if (!empty($where_values)) {
            $csv_query = $wpdb->prepare($csv_query, $where_values);
        }
        $csv_appointments = $wpdb->get_results($csv_query);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers
        fputcsv($output, array('詳細', '受付日時', '予約希望日時', '紹介病院名', '飼主名', '動物名/種', '診療科', '電話番号', 'ステータス'));
        
        // Add data rows
        foreach ($csv_appointments as $appointment) {
            $row = array(
                '表示',
                $this->formatDateTime($appointment->created_at),
                $this->formatDateTime($appointment->first_choice_date . ' ' . $appointment->first_choice_time),
                $appointment->referrer_clinic_name ?: '-',
                $appointment->owner_name,
                $appointment->pet_name . '/' . $appointment->animal_type,
                $appointment->department,
                $appointment->owner_phone,
                $this->getStatusText($appointment->status)
            );
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Format date time for CSV
     * 
     * @param string $dateTimeString
     * @return string
     */
    private function formatDateTime($dateTimeString) {
        if (empty($dateTimeString)) return '-';
        $date = new DateTime($dateTimeString);
        return $date->format('Y-m-d H:i:s');
    }
    
    /**
     * Get status text for CSV
     * 
     * @param string $status
     * @return string
     */
    private function getStatusText($status) {
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
}

// Initialize CSV handler
new PCB_CSV_Handler(); 
