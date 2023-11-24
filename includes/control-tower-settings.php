<?php

// Add an admin menu item for the configuration form
function control_tower_admin_menu() {
    add_menu_page(
        'ControlTower Configuration',  // Page title
        'ControlTower Configuration',  // Menu title
        'manage_options',    // Capability required to access
        'control-tower-config',        // Menu slug
        'control_tower_form',    // Callback function to display the form
        'dashicons-admin-generic'  // Icon
    );
}

add_action('admin_menu', 'control_tower_admin_menu');


// Define and initialize the configuration variables
$control_tower_scope = get_option('control_tower_scope', '');
$control_tower_middleware_url = get_option('control_tower_middleware_url', '');
$enable = get_option('enable', '');


// Function to display the configuration form
function control_tower_form() {
    ?>
    <div class="wrap">
        <h2>ControlTower Configuration</h2>
        <form method="post" action="options.php">
            <?php settings_fields('control-tower-config-settings'); ?>
            <?php do_settings_sections('control-tower-config-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">ControlTower Scope:</th>
                    <td><input type="text" name="control_tower_scope" value="<?php echo esc_attr(get_option('control_tower_scope')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">ControlTower Middleware URL:</th>
                    <td><input type="text" name="control_tower_middleware_url" value="<?php echo esc_attr(get_option('control_tower_middleware_url')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable:</th>
                    <td>
                        <input type="checkbox" name="enable" <?php checked(get_option('enable'), '1'); ?> value="1" />
                   </td>
                </tr>
            </table>
            <?php submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Function to register and save settings
function control_tower_register_settings() {
    register_setting('control-tower-config-settings', 'control_tower_scope');
    register_setting('control-tower-config-settings', 'control_tower_middleware_url');
    register_setting('control-tower-config-settings', 'enable');
}

add_action('admin_init', 'control_tower_register_settings');