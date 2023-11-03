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
        wp_send_json(['invalid_attachment_id' => 'Invalid or missing attachment ID'], 400);
    }

    // Check if the attachment exists.
    if (!wp_attachment_is_image($mediaId)) {
        wp_send_json(['invalid_attachment' => 'Attachment not found'], 404);
    }

    // Attempt to delete the attachment.
    $result = wp_delete_attachment($mediaId, true);

    if ($result === false) {
        wp_send_json(['delete_error' => 'Failed to delete the attachment'], 500);
    }

    // Return a success response.
    wp_send_json(['message' => 'Media deleted successfully'], 200);
}
