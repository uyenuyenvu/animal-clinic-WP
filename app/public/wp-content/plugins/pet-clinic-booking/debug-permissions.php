<?php
/**
 * Debug file to check permissions and user roles
 * Access this file directly to debug permission issues
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress context, include WordPress
    require_once('../../../wp-load.php');
}

// Include permissions class
require_once plugin_dir_path(__FILE__) . 'includes/permissions.php';

echo '<h1>Pet Clinic Booking - Debug Permissions</h1>';

// Check if user is logged in
echo '<h2>User Login Status</h2>';
if (is_user_logged_in()) {
    echo '<p style="color: green;">✓ User is logged in</p>';
} else {
    echo '<p style="color: red;">✗ User is NOT logged in</p>';
    echo '<p><a href="' . wp_login_url() . '">Login here</a></p>';
    exit;
}

// Get current user info
$user = wp_get_current_user();
echo '<h2>Current User Information</h2>';
echo '<p><strong>User ID:</strong> ' . $user->ID . '</p>';
echo '<p><strong>Username:</strong> ' . $user->user_login . '</p>';
echo '<p><strong>Display Name:</strong> ' . $user->display_name . '</p>';
echo '<p><strong>Email:</strong> ' . $user->user_email . '</p>';

// Check user roles
echo '<h2>User Roles</h2>';
if (!empty($user->roles)) {
    echo '<ul>';
    foreach ($user->roles as $role) {
        echo '<li>' . esc_html($role) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">No roles assigned to user</p>';
}

// Check permissions
echo '<h2>Permission Checks</h2>';
echo '<p><strong>can_access_pet_clinic():</strong> ' . (PCB_Permissions::can_access_pet_clinic() ? '✓ TRUE' : '✗ FALSE') . '</p>';
echo '<p><strong>can_view_appointments():</strong> ' . (PCB_Permissions::can_view_appointments() ? '✓ TRUE' : '✗ FALSE') . '</p>';
echo '<p><strong>can_export_csv():</strong> ' . (PCB_Permissions::can_export_csv() ? '✓ TRUE' : '✗ FALSE') . '</p>';
echo '<p><strong>can_manage_doctors():</strong> ' . (PCB_Permissions::can_manage_doctors() ? '✓ TRUE' : '✗ FALSE') . '</p>';

// Check WordPress capabilities
echo '<h2>WordPress Capabilities</h2>';
echo '<p><strong>manage_options:</strong> ' . (current_user_can('manage_options') ? '✓ TRUE' : '✗ FALSE') . '</p>';
echo '<p><strong>read:</strong> ' . (current_user_can('read') ? '✓ TRUE' : '✗ FALSE') . '</p>';
echo '<p><strong>edit_posts:</strong> ' . (current_user_can('edit_posts') ? '✓ TRUE' : '✗ FALSE') . '</p>';

// Show allowed roles
echo '<h2>Allowed Roles for Pet Clinic Access</h2>';
echo '<p>Currently allowed roles: <strong>administrator, subscriber</strong></p>';

// Check if current user has allowed role
$allowed_roles = array('administrator', 'subscriber');
$has_allowed_role = false;

foreach ($user->roles as $role) {
    if (in_array($role, $allowed_roles)) {
        $has_allowed_role = true;
        break;
    }
}

echo '<p><strong>Has allowed role:</strong> ' . ($has_allowed_role ? '✓ YES' : '✗ NO') . '</p>';

// Provide solutions
echo '<h2>Solutions</h2>';
if (!is_user_logged_in()) {
    echo '<p style="color: red;">1. You need to log in to WordPress admin first.</p>';
} elseif (!$has_allowed_role) {
    echo '<p style="color: red;">2. Your user role is not allowed. You need to be either:</p>';
    echo '<ul>';
    echo '<li>Administrator</li>';
    echo '<li>Subscriber</li>';
    echo '</ul>';
    echo '<p>To change your role, go to Users > Your Profile and change your role.</p>';
} else {
    echo '<p style="color: green;">✓ All permissions are correct. You should be able to access the appointments page.</p>';
    echo '<p><a href="' . admin_url('admin.php?page=pcb-appointments') . '">Go to Appointments Page</a></p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url() . '">← Back to WordPress Admin</a></p>';
?>
