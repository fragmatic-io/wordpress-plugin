<?php
/**
 * Registers a custom REST API route for deleting media.
 *
 * This endpoint allows users to delete media files from the WordPress Media Library via a DELETE request.
 */
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/delete-media', [
        'methods' => 'DELETE',
        'callback' => 'delete_media',
        'permission_callback' => 'user_authentication',
    ]);
});

/**
 * Handles the deletion of media from the Media Library.
 *
 * This function processes the DELETE request to delete a media attachment based on the provided attachment ID.
 *
 * @param WP_REST_Request $request The incoming API request.
 */
function delete_media($request)
{
    $mediaId = $request->get_param('id');

    if (empty($mediaId) || !is_numeric($mediaId)) {
        wp_send_json(['invalid_attachment_id' => 'Invalid or missing attachment ID'], 400);
    }

    if (!wp_attachment_is_image($mediaId)) {
        wp_send_json(['invalid_attachment' => 'Attachment not found'], 404);
    }

    $result = wp_delete_attachment($mediaId, true);

    if ($result === false) {
        wp_send_json(['delete_error' => 'Failed to delete the attachment'], 500);
    }

    wp_send_json(['message' => 'Media deleted successfully'], 200);
}
