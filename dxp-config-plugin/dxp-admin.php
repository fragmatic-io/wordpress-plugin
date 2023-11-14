<?php
// Add an admin menu item for the configuration form
function dxp_admin_menu() {
    add_menu_page(
        'DXP Configuration',  // Page title
        'DXP Configuration',  // Menu title
        'manage_options',    // Capability required to access
        'dxp-config',        // Menu slug
        'dxp_config_form',    // Callback function to display the form
        'dashicons-admin-generic'  // Icon (you can change this to a different Dashicon)
    );
}

add_action('admin_menu', 'dxp_admin_menu');