<?php
/**
 * Plugin Name: Custom Media API
 * Description: This plugin creates an API for media
 * Version: 1.0
 * Author: Akshat
*/

if (!defined('ABSPATH')) {
    header("Location: /custom-media-api");
    die();
}

// Load necessary files
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(plugin_dir_path(__FILE__) . 'api/endpoints/upload-media.php');
require_once(plugin_dir_path(__FILE__) . 'api/endpoints/get-media.php');
require_once(plugin_dir_path(__FILE__) . 'api/endpoints/delete-media.php');
require_once(plugin_dir_path(__FILE__) . 'api/authentication.php');

// Include the settings page file
require plugin_dir_path(__FILE__) . 'includes/settings-page.php';
