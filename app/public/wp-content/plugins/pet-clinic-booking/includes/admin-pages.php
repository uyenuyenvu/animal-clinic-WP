<?php
/**
 * Admin Pages Handler
 */

class PCB_Admin_Pages {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        // Only show menu if user has access
        if (!PCB_Permissions::can_access_pet_clinic()) {
            return;
        }
        
        add_menu_page(
            '申し込み管理',
            'Pet Clinic',
            'read', // Use 'read' capability for subscribers
            'pcb-appointments',
            array($this, 'appointments_list_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'pcb-appointments',
            '申し込み一覧',
            '申し込み一覧',
            'read', // Use 'read' capability for subscribers
            'pcb-appointments',
            array($this, 'appointments_list_page')
        );
        
        // Only show doctor management for administrators
        if (PCB_Permissions::can_manage_doctors()) {
            add_submenu_page(
                'pcb-appointments',
                'ドクター一覧',
                'ドクター一覧',
                'read',
                'pcb-doctors',
                array($this, 'doctors_list_page')
            );
            
            add_submenu_page(
                'pcb-appointments',
                'ドクター追加',
                'ドクター追加',
                'read',
                'pcb-add-doctor',
                array($this, 'add_doctor_page')
            );
        }
        
        // Register the page without adding it to menu
        add_action('admin_menu', array($this, 'register_hidden_page'), 999);
        
        // Add debug page for development
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_submenu_page(
                'pcb-appointments',
                'Debug Permissions',
                'Debug Permissions',
                'read',
                'pcb-debug-permissions',
                array($this, 'debug_permissions_page')
            );
        }
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_pcb-appointments' !== $hook && 'pet-clinic_page_pcb-appointment-detail' !== $hook && 'pet-clinic_page_pcb-hospital-detail' !== $hook && 'pet-clinic_page_pcb-doctors' !== $hook && 'pet-clinic_page_pcb-add-doctor' !== $hook) {
            return;
        }
        
        wp_enqueue_script('pcb-admin', PCB_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PCB_VERSION, true);
        wp_enqueue_style('pcb-admin', PCB_PLUGIN_URL . 'assets/css/admin.css', array(), PCB_VERSION);
        wp_localize_script('pcb-admin', 'pcb_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pcb_admin_nonce')
        ));
    }
    
    public function appointments_list_page() {
        // Check permissions
        if (!PCB_Permissions::check_access('can_view_appointments', '申し込み一覧')) {
            return;
        }
        
        include PCB_PLUGIN_PATH . 'templates/admin/appointments-list.php';
    }
    
    public function appointment_detail_page() {
        // Check permissions
        if (!PCB_Permissions::check_access('can_view_appointment_detail', '申し込み詳細')) {
            return;
        }
        
        include PCB_PLUGIN_PATH . 'templates/admin/appointment-detail.php';
    }
    
    /**
     * Doctors list page handler
     */
    public function doctors_list_page() {
        // Check permissions
        if (!PCB_Permissions::check_access('can_manage_doctors', 'ドクター一覧')) {
            return;
        }
        
        include PCB_PLUGIN_PATH . 'templates/admin/doctors-list.php';
    }
    
    /**
     * Add doctor page handler
     */
    public function add_doctor_page() {
        // Check permissions
        if (!PCB_Permissions::check_access('can_add_doctors', 'ドクター追加')) {
            return;
        }
        
        include PCB_PLUGIN_PATH . 'templates/admin/add-doctor.php';
    }
    
    /**
     * Hospital detail page handler
     */
    public function hospital_detail_page() {
        // Check permissions
        if (!PCB_Permissions::check_access('can_view_hospital_detail', '病院詳細')) {
            return;
        }
        
        include PCB_PLUGIN_PATH . 'templates/admin/hospital-detail.php';
    }
    
    /**
     * Debug permissions page
     */
    public function debug_permissions_page() {
        include PCB_PLUGIN_PATH . 'debug-permissions.php';
    }
    
    /**
     * Register hidden page without adding to menu
     * This allows access to the detail page via direct URL
     */
    public function register_hidden_page() {
        add_submenu_page(
            null, // No parent menu
            '申し込み詳細',
            '申し込み詳細',
            'read', // Use 'read' capability for subscribers
            'pcb-appointment-detail',
            array($this, 'appointment_detail_page')
        );
        
        // Register hospital detail page
        add_submenu_page(
            null, // No parent menu
            '病院詳細',
            '病院詳細',
            'read', // Use 'read' capability for subscribers
            'pcb-hospital-detail',
            array($this, 'hospital_detail_page')
        );
    }
}

new PCB_Admin_Pages();