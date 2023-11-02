<?php
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/delete-media', [
        'methods' => 'DELETE',
        'callback' => 'delete_media',
        'permission_callback' => 'user_authentication',
    ]);
});

function delete_media($request) {
    $mediaId = $request->get_param('id');

    // Check if the attachment ID is provided in the request.
    if(empty($mediaId) || !is_numeric($mediaId)) {
        return new WP_Error('invalid_attachment_id', 'Invalid or missing attachment ID', ['status' => 400]);
    }

    // Check if the attachment exists.
    if (!wp_attachment_is_image($mediaId)) {
        return new WP_Error('invalid_attachment', 'Attachment not found or not an image', array('status' => 404));
    }

    // Attempt to delete the attachment.
    $result = wp_delete_attachment($mediaId, true);

    if ($result === false) {
        return new WP_Error('delete_error', 'Failed to delete the attachment', array('status' => 500));
    }

    // Return a success response.
    wp_send_json_success(['message' => 'Media deleted successfully'], 200);
}
