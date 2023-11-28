<?php

/**
 * Registers a custom REST API route for uploading media.
 *
 * This endpoint allows users to upload media files to the WordPress Media Library via a POST request.
 */
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/upload-media', [
        'methods' => 'POST',
        'callback' => 'upload_media',
        'permission_callback' => 'user_authentication',
    ]);
});

/**
 * Handles the media upload process and inserts it into the Media Library.
 *
 * This function processes the uploaded media file, validates it, and inserts it into the WordPress Media Library.
 *
 * @param WP_REST_Request $request The incoming API request.
 */
function upload_media($request)
{
    $response = [];
    $allow_ext = explode(',', get_option('media_file_ext'));

    $maxContentLength = $request->get_header('maxContentLength');
    $maxBodyLength = $request->get_header('maxBodyLength');

    if ($maxBodyLength === null && $maxContentLength === null) {
        $maxBodyLength = 30000000; // 30 MB
        $maxContentLength = 30000000; // 30 MB
    }

    $maxFileSize = min($maxContentLength, $maxBodyLength);

    // Read the raw POST data
    $data = file_get_contents("php://input");

    if (!$data || strlen($data) > $maxFileSize) {
        wp_send_json(['error' => 'Media file not provided or it exceeded the media size limit'], 413);
        return;
    }

    // Create a temporary file to store the binary data
    $tmpfname = tempnam(sys_get_temp_dir(), 'upload');
    file_put_contents($tmpfname, $data);

    // Mimic the $_FILES structure
    $filename = $request->get_header('filename'); // Assuming filename is passed in a header
    $file = [
        'name' => $filename,
        'type' => mime_content_type($tmpfname),
        'tmp_name' => $tmpfname,
        'error' => 0,
        'size' => filesize($tmpfname)
    ];

    // Check file extension
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array($file_ext, $allow_ext)) {
        unlink($tmpfname); // Delete temporary file
        wp_send_json(['error' => 'This extension is not allowed.'], 400);
        return;
    }

    // Handle file upload
    $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
    if ($upload['error']) {
        unlink($tmpfname); // Delete temporary file
        wp_send_json(array('error' => $upload['error']), 404);
        return;
    } else {
        $attachment_data = [
            'post_mime_type' => $file['type'],
            'post_title' => pathinfo($file['name'], PATHINFO_FILENAME),
            'post_content' => '',
            'post_status' => 'inherit',
            'post_excerpt' => $request->get_param('caption'),
        ];
        $attachment_id = wp_insert_attachment($attachment_data, $upload['file']);

        if ($alt_text = $request->get_param('alt_text')) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
        }

        if (!is_wp_error($attachment_id)) {
            wp_schedule_single_event(time(), 'update_attachment_metadata_cron', array($attachment_id));
            $response = [
                'message' => 'Media uploaded and inserted into the Media Library',
                'attachment_id' => $attachment_id,
            ];
            wp_send_json($response, 201);
        } else {
            wp_send_json(['error' => 'Failed to insert media into the Media Library. An unexpected error occurred on the server.'], 500);
        }
    }

    // Delete the temporary file
    unlink($tmpfname);
}

/**
 * Callback function for the 'update_attachment_metadata_cron' event.
 * Updates the attachment metadata in the background.
 *
 * @param int $attachment_id The ID of the attachment to update.
 */
function update_attachment_metadata_background($attachment_id)
{
    $attachment_data = wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id));
    wp_update_attachment_metadata($attachment_id, $attachment_data);
}

add_action('update_attachment_metadata_cron', 'update_attachment_metadata_background');
