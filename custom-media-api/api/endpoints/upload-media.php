<?php
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/upload-media', [
        'methods' => 'POST',
        'callback' => 'upload_media',
        'permission_callback' => 'user_authentication',
    ]);
});

function upload_media($request) {
    $response = [];

    if (!empty($_FILES) && !empty($_FILES['file']['name'])) {
        $file = $_FILES['file']; // Getting media file

        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        // Allowed extensions
        $allow_ext = ['jpeg','jpg','png','gif','webp','mp4','svg'];

        // Checking the file extension is allowed or not
        if (!in_array($file_ext, $allow_ext)){
            wp_send_json_error(['error' => 'This extension is not allow.'], 400);
        }

        $maxFileSize = 2 * 1024 * 1024; // Maximum file size: 2MB
        $fileSize = $file['size'];

        if ($fileSize > $maxFileSize) {
            // Reject the file if it exceeds the maximum limit
            wp_send_json_error(['error' => 'File size exceeds the maximum limit'], 413);
        }

        $fileName = pathinfo($file['name'], PATHINFO_FILENAME);
        $fileType = wp_check_filetype($file['name'], null);

        // Use wp_upload_bits to move and store the uploaded file
        $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));

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
                // Schedule the background task (Cron job)
                wp_schedule_single_event(time(), 'update_attachment_metadata_cron', array($attachment_id));

                $response = [
                    'message' => 'Media uploaded and inserted into the Media Library',
                    'attachment_id' => $attachment_id,
                ];
                wp_send_json_success($response, 202);
            }
            else {
                wp_send_json_error(['error' => 'Failed to insert media into the Media Library.  An unexpected error occurred on the server.'], 500);
            }
        }
    } else {
        wp_send_json_error(['error' => 'Media file not provided or it exceeded the media size limit'], 413);
    }
}

function update_attachment_metadata_background($attachment_id) {
    // Generate attachment metadata and update the database
    $attachment_data = wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id));
    wp_update_attachment_metadata($attachment_id, $attachment_data);
}
add_action('update_attachment_metadata_cron', 'update_attachment_metadata_background');
