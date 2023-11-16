<?php
/**
 * Plugin Name: Custom Media API
 * Description: This plugin creates an API for media
 * Version: 1.0
 * Author: Akshat
 *
 * This plugin sets up a custom media API for WordPress. It provides endpoints for uploading,
 * retrieving, and deleting media, as well as implementing authentication for these operations.
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
require_once(plugin_dir_path(__FILE__) . 'api/endpoints/update-css.php');
require_once(plugin_dir_path(__FILE__) . 'api/endpoints/update-js.php');
require_once(plugin_dir_path(__FILE__) . 'api/authentication.php');
require_once(plugin_dir_path(__FILE__) . 'includes/settings-page.php');
require_once(plugin_dir_path(__FILE__) . 'includes/dxp-settings.php');