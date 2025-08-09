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

// Include admin pages and permissions
if (is_admin()) {
    require_once PCB_PLUGIN_PATH . 'includes/permissions.php';
    require_once PCB_PLUGIN_PATH . 'includes/admin-pages.php';
    require_once PCB_PLUGIN_PATH . 'includes/csv-handler.php';
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

        add_action('wp_ajax_pcb_admin_update_appointment_status', array($this, 'ajax_admin_update_appointment_status'));
    }

    public function ajax_admin_update_appointment_status() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Not authorized.');
        }

        global $wpdb;
        $appointment_id = intval($_POST['appointment_id']);
        $status = sanitize_text_field($_POST['status']);

        $result = $wpdb->update(
            $wpdb->prefix . 'clinic_reservations',
            array('status' => $status),
            array('id' => $appointment_id),
            array('%s'),
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success('Update status successfully!');
        } else {
            wp_send_json_error('Error updating status.');
        }
    }


    public function activate() {
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function add_query_vars($vars) {
        $vars[] = 'pcb_page';
        $vars[] = 'booking_id';
        $vars[] = 'appointment_id';
        return $vars;
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

    /**
     * Get appointment detail by ID
     * @param int $appointment_id
     * @return object|null
     */
    public function get_appointment_detail($appointment_id) {
        global $wpdb;
        $table_bookings = $wpdb->prefix . 'clinic_reservations';
        
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
        $table_doctors = $wpdb->prefix . 'clinic_clinics';
        
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
