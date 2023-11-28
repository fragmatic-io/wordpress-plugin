<?php

// Add an admin menu item for the configuration form
function control_tower_admin_menu() {
    add_menu_page(
        'ControlTower Configuration',  // Page title
        'Control Tower Configs',  // Menu title
        'manage_options',    // Capability required to access
        'control-tower-config',        // Menu slug
        'control_tower_form',    // Callback function to display the form
        'dashicons-admin-generic',  // Icon
        30  // Position in the menu
    );
}

add_action('admin_menu', 'control_tower_admin_menu');

// Function to display the configuration form
function control_tower_form() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Retrieve options
    $file_ext = get_option('media_file_ext', 'jpeg,jpg,png,gif,webp,mp4');
    $control_tower_scope = get_option('control_tower_scope', '');
    $control_tower_middleware_url = get_option('control_tower_middleware_url', '');
    $enable = get_option('enable', '');

    // Handle form submission and save the values
    if (isset($_POST['submit'])) {
        $file_ext = sanitize_text_field($_POST['media_file_ext']) ?: 'jpeg,jpg,png,gif,webp,mp4';
        $control_tower_scope = sanitize_text_field($_POST['control_tower_scope']);
        $control_tower_middleware_url = esc_url_raw($_POST['control_tower_middleware_url']);
        $enable = isset($_POST['enable']) ? '1' : '';

        // Update options
        update_option('media_file_ext', $file_ext);
        update_option('control_tower_scope', $control_tower_scope);
        update_option('control_tower_middleware_url', $control_tower_middleware_url);
        update_option('enable', $enable);
    }
    ?>
    <div class="wrap">
        <h2>ControlTower Configuration</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Scope:</th>
                    <td><input type="text" name="control_tower_scope" value="<?php echo esc_attr($control_tower_scope); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Middleware URL:</th>
                    <td><input type="url" name="control_tower_middleware_url" value="<?php echo esc_url($control_tower_middleware_url); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Tracking:</th>
                    <td>
                        <input type="checkbox" name="enable" <?php checked($enable, '1'); ?> value="1" />
                   </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="media_file_ext">Allowed File Extensions:</label>
                    </th>
                    <td>
                        <input type="text" id="media_file_ext" name="media_file_ext"
                            value="<?php echo esc_attr($file_ext); ?>">
                        <p class="description">Enter allowed file extensions (comma-separated: jpg,png, etc)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Changes', 'primary', 'submit'); ?>
        </form>
    </div>
    <?php
}

// Function to register and save settings
function control_tower_register_settings() {
    register_setting('control-tower-config-settings', 'media_file_ext');
    register_setting('control-tower-config-settings', 'control_tower_scope');
    register_setting('control-tower-config-settings', 'control_tower_middleware_url');
    register_setting('control-tower-config-settings', 'enable');
}

add_action('admin_init', 'control_tower_register_settings');
