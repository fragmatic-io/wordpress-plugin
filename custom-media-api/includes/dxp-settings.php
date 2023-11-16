<?php

// Add an admin menu item for the configuration form
function dxp_admin_menu() {
    add_menu_page(
        'DXP Configuration',  // Page title
        'DXP Configuration',  // Menu title
        'manage_options',    // Capability required to access
        'dxp-config',        // Menu slug
        'dxp_config_form',    // Callback function to display the form
        'dashicons-admin-generic'  // Icon
    );
}

add_action('admin_menu', 'dxp_admin_menu');


// Define and initialize the configuration variables
$dxp_scope = get_option('dxp_scope', '');
$dxp_url = get_option('dxp_url', '');
$dxp_middleware_url = get_option('dxp_middleware_url', '');
$dxp_dashboard_url = get_option('dxp_dashboard_url', '');
$dxp_tags = get_option('dxp_tags', '');
$dxp_categories = get_option('dxp_categories', '');
$dxp_session_expiry = get_option('dxp_session_expiry', '');
$dxp_consent_cookie_name = get_option('dxp_consent_cookie_name', '');
$dxp_consent_continent = get_option('dxp_consent_continent', '');
$dxp_timeout_in_milliseconds = get_option('dxp_timeout_in_milliseconds', '');
$dxp_prod = get_option('dxp_prod', '');


// Function to display the configuration form
function dxp_config_form() {
    ?>
    <div class="wrap">
        <h2>DXP Configuration</h2>
        <form method="post" action="options.php">
            <?php settings_fields('dxp-config-settings'); ?>
            <?php do_settings_sections('dxp-config-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">DXP Scope:</th>
                    <td><input type="text" name="dxp_scope" value="<?php echo esc_attr(get_option('dxp_scope')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP URL:</th>
                    <td><input type="text" name="dxp_url" value="<?php echo esc_attr(get_option('dxp_url')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Middleware URL:</th>
                    <td><input type="text" name="dxp_middleware_url" value="<?php echo esc_attr(get_option('dxp_middleware_url')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Dashboard URL:</th>
                    <td><input type="text" name="dxp_dashboard_url" value="<?php echo esc_attr(get_option('dxp_dashboard_url')); ?>" required/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Tags:</th>
                    <td><input type="text" name="dxp_tags" value="<?php echo esc_attr(get_option('dxp_tags')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Categories:</th>
                    <td><input type="text" name="dxp_categories" value="<?php echo esc_attr(get_option('dxp_categories')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Session Expiry:</th>
                    <td><input type="text" name="dxp_session_expiry" value="<?php echo esc_attr(get_option('dxp_session_expiry')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Consent Cookie Name:</th>
                    <td><input type="text" name="dxp_consent_cookie_name" value="<?php echo esc_attr(get_option('dxp_consent_cookie_name')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Consent Continent:</th>
                    <td><input type="text" name="dxp_consent_continent" value="<?php echo esc_attr(get_option('dxp_consent_continent')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Timeout (Milliseconds):</th>
                    <td><input type="text" name="dxp_timeout_in_milliseconds" value="<?php echo esc_attr(get_option('dxp_timeout_in_milliseconds')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">DXP Prod:</th>
                    <td><input type="text" name="dxp_prod" value="<?php echo esc_attr(get_option('dxp_prod')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Function to register and save settings
function dxp_register_settings() {
    register_setting('dxp-config-settings', 'dxp_scope');
    register_setting('dxp-config-settings', 'dxp_url');
    register_setting('dxp-config-settings', 'dxp_middleware_url');
    register_setting('dxp-config-settings', 'dxp_dashboard_url');
    register_setting('dxp-config-settings', 'dxp_tags');
    register_setting('dxp-config-settings', 'dxp_categories');
    register_setting('dxp-config-settings', 'dxp_session_expiry');
    register_setting('dxp-config-settings', 'dxp_consent_cookie_name');
    register_setting('dxp-config-settings', 'dxp_consent_continent');
    register_setting('dxp-config-settings', 'dxp_timeout_in_milliseconds');
    register_setting('dxp-config-settings', 'dxp_prod');
}

add_action('admin_init', 'dxp_register_settings');