<?php

/**
 * Admin Pages Handler
 */
class PCB_Admin_Pages
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function add_admin_menu()
    {
        // Only show menu if user has access
        if (! PCB_Permissions::can_access_pet_clinic()) {
            return;
        }

        add_menu_page(
            '予約一覧',
            'Pet Clinic',
            'read', // Use 'read' capability for subscribers
            'pcb-appointments',
            [$this, 'appointments_list_page'],
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'pcb-appointments',
            '予約一覧',
            '予約一覧',
            'read', // Use 'read' capability for subscribers
            'pcb-appointments',
            [$this, 'appointments_list_page']
        );

        // Register the page without adding it to menu
        add_action('admin_menu', [$this, 'register_hidden_page'], 999);
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'toplevel_page_pcb-appointments' && $hook !== 'pet-clinic_page_pcb-appointment-detail' && $hook !== 'pet-clinic_page_pcb-hospital-detail' && $hook !== 'pet-clinic_page_pcb-doctors' && $hook !== 'pet-clinic_page_pcb-add-doctor') {
            return;
        }

        wp_enqueue_script('pcb-admin', PCB_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], PCB_VERSION, true);
        wp_enqueue_style('pcb-admin', PCB_PLUGIN_URL . 'assets/css/admin.css', [], PCB_VERSION);
        wp_localize_script('pcb-admin', 'pcb_admin_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pcb_admin_nonce'),
        ]);
    }

    public function appointments_list_page()
    {
        // Check permissions
        if (! PCB_Permissions::check_access('can_view_appointments', '申し込み一覧')) {
            return;
        }

        include PCB_PLUGIN_PATH . 'templates/admin/appointments-list.php';
    }

    public function appointment_detail_page()
    {
        // Check permissions
        if (! PCB_Permissions::check_access('can_view_appointment_detail', '申し込み詳細')) {
            return;
        }

        include PCB_PLUGIN_PATH . 'templates/admin/appointment-detail.php';
    }

    /**
     * Doctors list page handler
     */
    public function doctors_list_page()
    {
        // Check permissions
        if (! PCB_Permissions::check_access('can_manage_doctors', 'ドクター一覧')) {
            return;
        }

        include PCB_PLUGIN_PATH . 'templates/admin/doctors-list.php';
    }

    /**
     * Add doctor page handler
     */
    public function add_doctor_page()
    {
        // Check permissions
        if (! PCB_Permissions::check_access('can_add_doctors', 'ドクター追加')) {
            return;
        }

        include PCB_PLUGIN_PATH . 'templates/admin/add-doctor.php';
    }

    /**
     * Hospital detail page handler
     */
    public function hospital_detail_page()
    {
        // Check permissions
        if (! PCB_Permissions::check_access('can_view_hospital_detail', '病院詳細')) {
            return;
        }

        include PCB_PLUGIN_PATH . 'templates/admin/hospital-detail.php';
    }

    /**
     * Debug permissions page
     */
    public function debug_permissions_page()
    {
        include PCB_PLUGIN_PATH . 'debug-permissions.php';
    }

    /**
     * Register hidden page without adding to menu
     * This allows access to the detail page via direct URL
     */
    public function register_hidden_page()
    {
        add_submenu_page(
            'pcb-appointments', // No parent menu
            '予約詳細',
            '予約詳細',
            'read', // Use 'read' capability for subscribers
            'pcb-appointment-detail',
            [$this, 'appointment_detail_page']
        );

        // Register hospital detail page
        add_submenu_page(
            'pcb-appointments', // No parent menu
            '予約詳細',
            '予約詳細',
            'read', // Use 'read' capability for subscribers
            'pcb-hospital-detail',
            [$this, 'hospital_detail_page']
        );
    }
}

new PCB_Admin_Pages;
