<?php
/**
 * Permissions Handler for Pet Clinic Booking Plugin
 */

class PCB_Permissions {
    
    /**
     * Check if user can access Pet Clinic features
     * Only Administrator and Subscriber can access
     * 
     * @return bool
     */
    public static function can_access_pet_clinic() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }
        
        $user = wp_get_current_user();
        $allowed_roles = array('administrator', 'subscriber');
        
        // Check if user has any of the allowed roles
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed_roles)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user can view appointments list
     * 
     * @return bool
     */
    public static function can_view_appointments() {
        return self::can_access_pet_clinic();
    }
    
    /**
     * Check if user can view appointment details
     * 
     * @return bool
     */
    public static function can_view_appointment_detail() {
        return self::can_access_pet_clinic();
    }
    
    /**
     * Check if user can view hospital details
     * 
     * @return bool
     */
    public static function can_view_hospital_detail() {
        return self::can_access_pet_clinic();
    }
    
    /**
     * Check if user can export CSV
     * Only Administrator and Subscriber can export
     * 
     * @return bool
     */
    public static function can_export_csv() {
        return self::can_access_pet_clinic();
    }
    
    /**
     * Check if user can manage doctors
     * Only Administrator can manage doctors
     * 
     * @return bool
     */
    public static function can_manage_doctors() {
        if (!is_user_logged_in()) {
            return false;
        }
        
        $user = wp_get_current_user();
        return in_array('administrator', $user->roles);
    }
    
    /**
     * Check if user can add doctors
     * Only Administrator can add doctors
     * 
     * @return bool
     */
    public static function can_add_doctors() {
        return self::can_manage_doctors();
    }
    
    /**
     * Check if user can edit doctors
     * Only Administrator can edit doctors
     * 
     * @return bool
     */
    public static function can_edit_doctors() {
        return self::can_manage_doctors();
    }
    
    /**
     * Check if user can delete doctors
     * Only Administrator can delete doctors
     * 
     * @return bool
     */
    public static function can_delete_doctors() {
        return self::can_manage_doctors();
    }
    
    /**
     * Get user's role display name
     * 
     * @return string
     */
    public static function get_user_role_display() {
        if (!is_user_logged_in()) {
            return '未ログイン';
        }
        
        $user = wp_get_current_user();
        
        switch ($user->roles[0]) {
            case 'administrator':
                return '管理者';
            case 'editor':
                return '編集者';
            case 'author':
                return '投稿者';
            case 'contributor':
                return '寄稿者';
            case 'subscriber':
                return '購読者';
            default:
                return '不明';
        }
    }
    
    /**
     * Show access denied message
     * 
     * @param string $feature_name
     * @param string $debug_info
     */
    public static function show_access_denied($feature_name = 'この機能', $debug_info = '') {
        $user_role = self::get_user_role_display();
        ?>
        <div class="wrap">
            <h1>アクセス拒否</h1>
            <div class="notice notice-error">
                <p><strong><?php echo esc_html($feature_name); ?></strong>にアクセスする権限がありません。</p>
                <p>現在のユーザーロール: <strong><?php echo esc_html($user_role); ?></strong></p>
                <p>この機能にアクセスするには、管理者または購読者である必要があります。</p>
                
                <?php if (!empty($debug_info)): ?>
                    <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #0073aa;">
                        <h4>Debug Information:</h4>
                        <p><?php echo wp_kses_post($debug_info); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <p><a href="<?php echo admin_url(); ?>" class="button">ダッシュボードに戻る</a></p>
            <p><a href="<?php echo admin_url('admin.php?page=pcb-debug-permissions'); ?>" class="button">Debug Permissions</a></p>
        </div>
        <?php
    }
    
    /**
     * Check if user can access and show error if not
     * 
     * @param string $capability
     * @param string $feature_name
     * @return bool
     */
    public static function check_access($capability, $feature_name = 'この機能') {
        if (!self::$capability()) {
            self::show_access_denied($feature_name);
            return false;
        }
        return true;
    }
} 