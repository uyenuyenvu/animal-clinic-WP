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
        add_menu_page(
            '申し込み管理',
            'Pet Clinic',
            'manage_options',
            'pcb-appointments',
            array($this, 'appointments_list_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'pcb-appointments',
            '申し込み一覧',
            '申し込み一覧',
            'manage_options',
            'pcb-appointments',
            array($this, 'appointments_list_page')
        );
        
                // Hidden submenu - removed from display but functionality remains
        // add_submenu_page(
        //     'pcb-appointments',
        //     '申し込み詳細',
        //     '申し込み詳細',
        //     'manage_options',
        //     'pcb-appointment-detail',
        //     array($this, 'appointment_detail_page')
        // );
        
        // Register the page without adding it to menu
        add_action('admin_menu', array($this, 'register_hidden_page'), 999);

    }
    
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_pcb-appointments' !== $hook && 'pet-clinic_page_pcb-appointment-detail' !== $hook) {
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
        include PCB_PLUGIN_PATH . 'templates/admin/appointments-list.php';
    }
    
    public function appointment_detail_page() {
        include PCB_PLUGIN_PATH . 'templates/admin/appointment-detail.php';
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
            'manage_options',
            'pcb-appointment-detail',
            array($this, 'appointment_detail_page')
        );
    }
}

new PCB_Admin_Pages();