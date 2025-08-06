<?php
/**
 * Plugin Name: Pet Clinic Booking System
 * Description: Hệ thống đặt phòng khám thú y
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PCB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PCB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PCB_VERSION', '1.0.0');

// Include admin pages
if (is_admin()) {
    require_once PCB_PLUGIN_PATH . 'includes/admin-pages.php';
}


class PetClinicBooking {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init() {
        // Add rewrite rules for custom pages
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'template_redirect'));

        // AJAX handlers
        add_action('wp_ajax_pcb_create_booking', array($this, 'ajax_create_booking'));
        add_action('wp_ajax_nopriv_pcb_create_booking', array($this, 'ajax_create_booking'));
        add_action('wp_ajax_pcb_get_bookings', array($this, 'ajax_get_bookings'));
        add_action('wp_ajax_nopriv_pcb_get_bookings', array($this, 'ajax_get_bookings'));
        add_action('wp_ajax_pcb_get_booking_detail', array($this, 'ajax_get_booking_detail'));
        add_action('wp_ajax_nopriv_pcb_get_booking_detail', array($this, 'ajax_get_booking_detail'));

        // Admin AJAX handlers
        add_action('wp_ajax_pcb_admin_add_doctor', array($this, 'ajax_admin_add_doctor'));
        add_action('wp_ajax_pcb_admin_get_doctors', array($this, 'ajax_admin_get_doctors'));
        add_action('wp_ajax_pcb_admin_get_all_bookings', array($this, 'ajax_admin_get_all_bookings'));
        add_action('wp_ajax_pcb_admin_get_appointments', array($this, 'ajax_admin_get_appointments'));
        add_action('wp_ajax_pcb_admin_get_appointment_detail', array($this, 'ajax_admin_get_appointment_detail'));
        add_action('wp_ajax_pcb_admin_update_appointment_status', array($this, 'ajax_admin_update_appointment_status'));
    }

    public function activate() {
        $this->create_tables();
        $this->add_rewrite_rules();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Bảng bác sĩ
        $table_doctors = $wpdb->prefix . 'pcb_doctors';
        $sql_doctors = "CREATE TABLE $table_doctors (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            specialization varchar(200) NOT NULL,
            phone varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Bảng đặt phòng - Cập nhật với các trường mới theo giao diện Nhật
        $table_bookings = $wpdb->prefix . 'pcb_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            booking_id varchar(20) NOT NULL,
            department varchar(100) NOT NULL,
            preferred_date_1 date,
            preferred_time_1 time,
            preferred_date_2 date,
            preferred_time_2 time,
            referral_hospital varchar(200),
            hospital_director varchar(100),
            assigned_doctor varchar(100),
            customer_name varchar(100) NOT NULL,
            pet_name varchar(100) NOT NULL,
            customer_address text NOT NULL,
            customer_phone varchar(20) NOT NULL,
            customer_email varchar(100) NOT NULL,
            emergency_contact varchar(20),
            pet_type varchar(50) NOT NULL,
            pet_breed varchar(100),
            pet_age int(3),
            pet_birth_date date,
            pet_gender enum('male','female') NOT NULL,
            pet_weight decimal(5,2),
            living_environment text,
            vaccination_history text,
            disease_details text,
            symptoms text,
            treatment_reference_data text,
            personal_info_consent enum('agreed','not_agreed') DEFAULT 'agreed',
            doctor_id mediumint(9),
            appointment_date date NOT NULL,
            appointment_time time NOT NULL,
            status varchar(20) DEFAULT 'pending',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_id (booking_id),
            KEY doctor_id (doctor_id),
            KEY appointment_date (appointment_date),
            KEY status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_doctors);
        dbDelta($sql_bookings);
    }

    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^dat-phong-kham/?$',
            'index.php?pcb_page=booking_form',
            'top'
        );
        add_rewrite_rule(
            '^danh-sach-dat-phong/?$',
            'index.php?pcb_page=booking_list',
            'top'
        );
        add_rewrite_rule(
            '^chi-tiet-dat-phong/([^/]+)/?$',
            'index.php?pcb_page=booking_detail&booking_id=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^admin/danh-sach-bac-si/?$',
            'index.php?pcb_page=admin_doctors',
            'top'
        );
        add_rewrite_rule(
            '^admin/them-bac-si/?$',
            'index.php?pcb_page=admin_add_doctor',
            'top'
        );
       add_rewrite_rule(
           '^admin/danh-sach-dat-lich/?$',
           'index.php?pcb_page=admin_appointments',
           'top'
       );
        add_rewrite_rule(
            '^admin/appointment-detail/([^/]+)/?$',
            'index.php?pcb_page=admin_appointment_detail&appointment_id=$matches[1]',
            'top'
        );
    }

    public function add_query_vars($vars) {
        $vars[] = 'pcb_page';
        $vars[] = 'booking_id';
        $vars[] = 'appointment_id';
        return $vars;
    }

    public function template_redirect() {
        $pcb_page = get_query_var('pcb_page');

        if ($pcb_page) {
            switch ($pcb_page) {
                case 'booking_form':
                    include PCB_PLUGIN_PATH . 'templates/booking-form.php';
                    exit;
                case 'booking_list':
                    include PCB_PLUGIN_PATH . 'templates/booking-list.php';
                    exit;
                case 'booking_detail':
                    include PCB_PLUGIN_PATH . 'templates/booking-detail.php';
                    exit;
                case 'admin_doctors':
                    include PCB_PLUGIN_PATH . 'templates/admin/doctors-list.php';
                    exit;
                case 'admin_add_doctor':
                    include PCB_PLUGIN_PATH . 'templates/admin/add-doctor.php';
                    exit;
                case 'admin_appointments':
                    include PCB_PLUGIN_PATH . 'templates/admin/appointments-list-bk.php';
                    exit;
                case 'admin_appointment_detail':
                    include PCB_PLUGIN_PATH . 'templates/admin/appointment-detail-bk.php';
                    exit;
            }
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('pcb-frontend', PCB_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), PCB_VERSION, true);
        wp_enqueue_style('pcb-frontend', PCB_PLUGIN_URL . 'assets/css/frontend.css', array(), PCB_VERSION);

        wp_localize_script('pcb-frontend', 'pcb_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pcb_nonce')
        ));
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('pcb-admin', PCB_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PCB_VERSION, true);
        wp_enqueue_style('pcb-admin', PCB_PLUGIN_URL . 'assets/css/admin.css', array(), PCB_VERSION);

        wp_localize_script('pcb-admin', 'pcb_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pcb_admin_nonce')
        ));
    }

    // AJAX handlers for frontend
    public function ajax_create_booking() {
        check_ajax_referer('pcb_nonce', 'nonce');

        global $wpdb;

        // Generate booking ID
        $booking_id = 'R' . date('Ymd') . sprintf('%03d', rand(1, 999));

        $data = array(
            'booking_id' => $booking_id,
            'department' => sanitize_text_field($_POST['department']),
            'preferred_date_1' => sanitize_text_field($_POST['preferred_date_1']),
            'preferred_time_1' => sanitize_text_field($_POST['preferred_time_1']),
            'preferred_date_2' => sanitize_text_field($_POST['preferred_date_2']),
            'preferred_time_2' => sanitize_text_field($_POST['preferred_time_2']),
            'referral_hospital' => sanitize_text_field($_POST['referral_hospital']),
            'hospital_director' => sanitize_text_field($_POST['hospital_director']),
            'assigned_doctor' => sanitize_text_field($_POST['assigned_doctor']),
            'customer_name' => sanitize_text_field($_POST['customer_name']),
            'pet_name' => sanitize_text_field($_POST['pet_name']),
            'customer_address' => sanitize_textarea_field($_POST['customer_address']),
            'customer_phone' => sanitize_text_field($_POST['customer_phone']),
            'customer_email' => sanitize_email($_POST['customer_email']),
            'emergency_contact' => sanitize_text_field($_POST['emergency_contact']),
            'pet_type' => sanitize_text_field($_POST['pet_type']),
            'pet_breed' => sanitize_text_field($_POST['pet_breed']),
            'pet_age' => intval($_POST['pet_age']),
            'pet_birth_date' => sanitize_text_field($_POST['pet_birth_date']),
            'pet_gender' => sanitize_text_field($_POST['pet_gender']),
            'pet_weight' => floatval($_POST['pet_weight']),
            'living_environment' => sanitize_textarea_field($_POST['living_environment']),
            'vaccination_history' => sanitize_textarea_field($_POST['vaccination_history']),
            'disease_details' => sanitize_textarea_field($_POST['disease_details']),
            'symptoms' => sanitize_textarea_field($_POST['symptoms']),
            'treatment_reference_data' => sanitize_textarea_field($_POST['treatment_reference_data']),
            'personal_info_consent' => sanitize_text_field($_POST['personal_info_consent']),
            'doctor_id' => intval($_POST['doctor_id']),
            'appointment_date' => sanitize_text_field($_POST['appointment_date']),
            'appointment_time' => sanitize_text_field($_POST['appointment_time']),
            'notes' => sanitize_textarea_field($_POST['notes'])
        );

        $result = $wpdb->insert($wpdb->prefix . 'pcb_bookings', $data);

        if ($result) {
            wp_send_json_success(array(
                'message' => 'Đặt phòng thành công!',
                'booking_id' => $wpdb->insert_id,
                'booking_number' => $booking_id
            ));
        } else {
            wp_send_json_error('Có lỗi xảy ra khi đặt phòng.');
        }
    }

    public function ajax_get_bookings() {
        check_ajax_referer('pcb_nonce', 'nonce');

        global $wpdb;

        $customer_email = sanitize_email($_POST['customer_email']);
        $customer_phone = sanitize_text_field($_POST['customer_phone']);

        $bookings = $wpdb->get_results($wpdb->prepare(
            "SELECT b.*, d.name as doctor_name
             FROM {$wpdb->prefix}pcb_bookings b
             LEFT JOIN {$wpdb->prefix}pcb_doctors d ON b.doctor_id = d.id
             WHERE b.customer_email = %s AND b.customer_phone = %s
             ORDER BY b.created_at DESC",
            $customer_email, $customer_phone
        ));

        wp_send_json_success($bookings);
    }

    public function ajax_get_booking_detail() {
        check_ajax_referer('pcb_nonce', 'nonce');

        global $wpdb;

        $booking_id = intval($_POST['booking_id']);

        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT b.*, d.name as doctor_name, d.specialization
             FROM {$wpdb->prefix}pcb_bookings b
             LEFT JOIN {$wpdb->prefix}pcb_doctors d ON b.doctor_id = d.id
             WHERE b.id = %d",
            $booking_id
        ));

        if ($booking) {
            wp_send_json_success($booking);
        } else {
            wp_send_json_error('Không tìm thấy thông tin đặt phòng.');
        }
    }

    // AJAX handlers for admin
    public function ajax_admin_add_doctor() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'specialization' => sanitize_text_field($_POST['specialization']),
            'phone' => sanitize_text_field($_POST['phone']),
            'email' => sanitize_email($_POST['email'])
        );

        $result = $wpdb->insert($wpdb->prefix . 'pcb_doctors', $data);

        if ($result) {
            wp_send_json_success('Thêm bác sĩ thành công!');
        } else {
            wp_send_json_error('Có lỗi xảy ra khi thêm bác sĩ.');
        }
    }

    public function ajax_admin_get_doctors() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;

        $doctors = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}pcb_doctors ORDER BY created_at DESC"
        );

        wp_send_json_success($doctors);
    }

    public function ajax_admin_get_all_bookings() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;

        $bookings = $wpdb->get_results(
            "SELECT b.*, d.name as doctor_name
             FROM {$wpdb->prefix}pcb_bookings b
             LEFT JOIN {$wpdb->prefix}pcb_doctors d ON b.doctor_id = d.id
             ORDER BY b.created_at DESC"
        );

        wp_send_json_success($bookings);
    }

    public function ajax_admin_get_appointments() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'pcb_bookings';

        // Get search parameters
        $search = isset($_POST['search']) ? $_POST['search'] : array();
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        // Build WHERE clause for search
        $where_conditions = array();
        $where_values = array();

        if (!empty($search['hospital_name'])) {
            $where_conditions[] = "referral_hospital LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($search['hospital_name']) . '%';
        }

        if (!empty($search['owner_name'])) {
            $where_conditions[] = "customer_name LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($search['owner_name']) . '%';
        }

        if (!empty($search['doctor_name'])) {
            $where_conditions[] = "(hospital_director LIKE %s OR assigned_doctor LIKE %s)";
            $where_values[] = '%' . $wpdb->esc_like($search['doctor_name']) . '%';
            $where_values[] = '%' . $wpdb->esc_like($search['doctor_name']) . '%';
        }

        if (!empty($search['department'])) {
            $where_conditions[] = "department LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($search['department']) . '%';
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

        // Get total count
        $count_query = "SELECT COUNT(*) FROM $table $where_clause";
        if (!empty($where_values)) {
            $count_query = $wpdb->prepare($count_query, $where_values);
        }
        $total_count = $wpdb->get_var($count_query);

        // Get appointments with pagination
        $query = "SELECT * FROM $table $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $query_values = array_merge($where_values, array($per_page, $offset));
        $appointments = $wpdb->get_results($wpdb->prepare($query, $query_values));

        // Calculate pagination
        $total_pages = ceil($total_count / $per_page);

        $response = array(
            'appointments' => $appointments,
            'pagination' => array(
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_count' => $total_count,
                'per_page' => $per_page
            )
        );

        wp_send_json_success($response);
    }

    public function ajax_admin_get_appointment_detail() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;
        $appointment_id = intval($_POST['appointment_id']);

        $appointment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pcb_bookings WHERE id = %d",
            $appointment_id
        ));

        if ($appointment) {
            wp_send_json_success($appointment);
        } else {
            wp_send_json_error('Không tìm thấy thông tin lịch hẹn.');
        }
    }

    public function ajax_admin_update_appointment_status() {
        check_ajax_referer('pcb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Không có quyền truy cập.');
        }

        global $wpdb;
        $appointment_id = intval($_POST['appointment_id']);
        $status = sanitize_text_field($_POST['status']);

        $result = $wpdb->update(
            $wpdb->prefix . 'pcb_bookings',
            array('status' => $status),
            array('id' => $appointment_id),
            array('%s'),
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success('Cập nhật trạng thái thành công!');
        } else {
            wp_send_json_error('Có lỗi xảy ra khi cập nhật trạng thái.');
        }
    }

    /**
     * Get appointment detail by ID
     * @param int $appointment_id
     * @return object|null
     */
    public function get_appointment_detail($appointment_id) {
        global $wpdb;
        $table_bookings = $wpdb->prefix . 'pcb_bookings';
        
        $appointment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_bookings WHERE id = %d",
            $appointment_id
        ));
        
        return $appointment;
    }

    /**
     * Get doctor detail by ID
     * @param int $doctor_id
     * @return object|null
     */
    public function get_doctor_detail($doctor_id) {
        global $wpdb;
        $table_doctors = $wpdb->prefix . 'pcb_doctors';
        
        $doctor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_doctors WHERE id = %d",
            $doctor_id
        ));
        
        return $doctor;
    }

    /**
     * Get hospital data from appointment
     * @param object $appointment
     * @return array
     */
    public function get_hospital_data_from_appointment($appointment) {
        if (!$appointment) {
            return array();
        }

        // Extract hospital information from appointment data
        $hospital_data = array(
            'hospital_name' => $appointment->referral_hospital ?: 'サンプル動物病院',
            'director_name' => $appointment->hospital_director ?: '山田 太郎',
            'doctor_in_charge' => $appointment->assigned_doctor ?: '佐藤 花子',
            'email' => $appointment->customer_email ?: 'yamada@example.com',
            'email_confirm' => $appointment->customer_email ?: 'yamada@example.com',
            'postal_code' => $this->extractPostalCode($appointment->customer_address) ?: '〒100-0001',
            'password' => '**********',
            'prefecture' => $this->extractPrefecture($appointment->customer_address) ?: '東京都',
            'city' => $this->extractCity($appointment->customer_address) ?: '千代田区',
            'street_address' => $this->extractStreet($appointment->customer_address) ?: '1-1-1 サンプルビル 3F',
            'phone_number' => $appointment->customer_phone ?: '03-1234-5678',
            'emergency_phone' => $appointment->emergency_contact ?: '03-8765-4321',
            'fax_number' => '03-1234-5679'
        );

        return $hospital_data;
    }

    /**
     * Extract postal code from address
     * @param string $address
     * @return string
     */
    private function extractPostalCode($address) {
        if (empty($address)) return '';
        
        // Look for postal code pattern (〒123-4567 or 123-4567)
        if (preg_match('/(?:〒)?(\d{3}-\d{4})/', $address, $matches)) {
            return '〒' . $matches[1];
        }
        
        return '';
    }

    /**
     * Extract prefecture from address
     * @param string $address
     * @return string
     */
    private function extractPrefecture($address) {
        if (empty($address)) return '';
        
        // Common Japanese prefectures
        $prefectures = array(
            '東京都', '大阪府', '京都府', '神奈川県', '愛知県', '埼玉県', '千葉県', '兵庫県',
            '福岡県', '静岡県', '茨城県', '広島県', '宮城県', '新潟県', '長野県', '群馬県',
            '岐阜県', '栃木県', '岡山県', '福島県', '三重県', '徳島県', '山口県', '愛媛県',
            '山梨県', '長崎県', '滋賀県', '奈良県', '青森県', '岩手県', '秋田県', '山形県',
            '宮崎県', '鹿児島県', '沖縄県', '北海道'
        );
        
        foreach ($prefectures as $prefecture) {
            if (strpos($address, $prefecture) !== false) {
                return $prefecture;
            }
        }
        
        return '';
    }

    /**
     * Extract city from address
     * @param string $address
     * @return string
     */
    private function extractCity($address) {
        if (empty($address)) return '';
        
        // Common Japanese cities/wards
        $cities = array(
            '千代田区', '中央区', '港区', '新宿区', '文京区', '台東区', '墨田区', '江東区',
            '品川区', '目黒区', '大田区', '世田谷区', '渋谷区', '中野区', '杉並区', '豊島区',
            '北区', '荒川区', '板橋区', '練馬区', '足立区', '葛飾区', '江戸川区'
        );
        
        foreach ($cities as $city) {
            if (strpos($address, $city) !== false) {
                return $city;
            }
        }
        
        return '';
    }

    /**
     * Extract street address from full address
     * @param string $address
     * @return string
     */
    private function extractStreet($address) {
        if (empty($address)) return '';
        
        // Remove postal code, prefecture, and city
        $street = $address;
        
        // Remove postal code
        $street = preg_replace('/(?:〒)?\d{3}-\d{4}\s*/', '', $street);
        
        // Remove prefecture and city
        $prefectures = array('東京都', '大阪府', '京都府', '神奈川県', '愛知県', '埼玉県', '千葉県', '兵庫県');
        $cities = array('千代田区', '中央区', '港区', '新宿区', '文京区', '台東区', '墨田区', '江東区');
        
        foreach ($prefectures as $prefecture) {
            $street = str_replace($prefecture, '', $street);
        }
        
        foreach ($cities as $city) {
            $street = str_replace($city, '', $street);
        }
        
        // Clean up extra spaces
        $street = trim($street);
        
        return $street ?: '1-1-1 サンプルビル 3F';
    }
}

// Initialize the plugin
new PetClinicBooking();
