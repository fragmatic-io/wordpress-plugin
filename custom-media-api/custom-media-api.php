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

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');

// Register REST API endpoints
add_action('rest_api_init', function () {
    // Endpoint to upload media
    register_rest_route('custom/v1', '/upload-media', array(
        'methods' => 'POST',
        'callback' => 'upload_media',
        'permission_callback' => '__return_true',
    ));

    // Endpoint to retrieve uploaded media
    register_rest_route('custom/v1', '/get-media', array(
        'methods' => 'GET',
        'callback' => 'get_media',
        'permission_callback' => '__return_true',
    ));
});

// Callback function for uploading media
function upload_media($request)
{
    $response = [];

    if (!empty($_FILES)) {
        $mediaFile = $_FILES[array_keys($_FILES)[0]]; // Getting media file
        $file_ext = pathinfo($mediaFile['name'], PATHINFO_EXTENSION);

        // Allowed extensions
        $allow_ext = ['jpeg','jpg','png','gif','webp','mp4','wmv','wav','svg'];

        // Checking the file extension is allowed or not
        if (!in_array($file_ext, $allow_ext)){
            wp_send_json_error(['error' => 'This extension is not allow.'], 400);
        }

        $maxFileSize = 2 * 1024 * 1024; // Maximum file size: 2MB
        $fileSize = $mediaFile['size'];

        if ($fileSize > $maxFileSize) {
            // Reject the file if it exceeds the maximum limit
            wp_send_json_error(['error' => 'File size exceeds the maximum limit'], 413);
        }

        $fileName = pathinfo($mediaFile['name'], PATHINFO_FILENAME);
        $fileType = wp_check_filetype($mediaFile['name'], null);

        // Use wp_upload_bits to move and store the uploaded file
        $upload = wp_upload_bits($mediaFile['name'], null, file_get_contents($mediaFile['tmp_name']));

        if ($upload['error']) {
            // Handle upload error
            wp_send_json_error(array('error' => $upload['error']), 404);
        } else {
            // Insert the uploaded media into the Media Library
            $attachment_data = [
                'post_mime_type' => $fileType['type'],
                'post_title' => $fileName,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_excerpt' => $request->get_param('caption'),
            ];
            $attachment_id = wp_insert_attachment($attachment_data, $upload['file']);

            if ($alt_text = $request->get_param('alt_text')) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
            }

            if (!is_wp_error($attachment_id)) {
                // Generate attachment metadata and update the database
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                $response = [
                    'message' => 'Media uploaded and inserted into the Media Library',
                    'attachment_id' => $attachment_id,
                ];

                wp_send_json_success($response, 201);
            } else {
                wp_send_json_error(['error' => 'Failed to insert media into the Media Library.  An unexpected error occurred on the server.'], 500);
            }
        }
    } else {
        wp_send_json_error(['error' => 'Media file not provided or it exceeded the media size limit'], 413);
    }
}

// Callback function for retrieving media
function get_media($request)
{
    $per_page = 5; // Set the number of attachments to show per page.
    $page = $request['page']; // Get the requested page number from the API request.

    if (isset($page) && (!is_numeric($page) || $page < 1)) {
        wp_send_json_error(['error' => 'Invalid page parameter'], 400);
    }
    else{
    // Calculate the offset for the database query.
    $offset = ($page - 1) * $per_page;

    $media = get_posts([
        'post_type' => 'attachment',
        'posts_per_page' => $per_page,
        'offset' => $offset,
    ]);

    $response = [];

    if ($media) {
        foreach ($media as $item) {
            $response[] = [
                'id' => $item->ID,
                'rendered' => wp_get_attachment_url($item->ID),
                'title' => get_the_title($item->ID),
                'mime_type' => get_post_mime_type($item->ID),
                'file_format' => pathinfo(get_attached_file($item->ID), PATHINFO_EXTENSION),
                'alt_text' => get_post_meta($item->ID, '_wp_attachment_image_alt', true),
                'caption' => $item->post_excerpt,
            ];
        }
        wp_send_json_success($response, 200);
    }
    else{
        // 404 (Not Found) when no media items are found
        wp_send_json_error(['error' => 'No media items found for the given page'], 204);
    }
}
}
