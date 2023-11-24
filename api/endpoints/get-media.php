<?php
/**
 * Registers a custom REST API route for retrieving media.
 *
 * This endpoint allows users to retrieve a list of media items from the WordPress Media Library via a GET request.
 */
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/get-media', [
        'methods' => 'GET',
        'callback' => 'get_media',
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Retrieves a list of media items from the Media Library.
 *
 * This function processes the GET request to retrieve a paginated list of media items. It returns essential details
 * for each media item, such as ID, URL, title, MIME type, file format, alt text, and caption.
 *
 * @param WP_REST_Request $request The incoming API request.
 */
function get_media($request)
{
    $per_page = intval(get_option('custom_media_api_per_page'));
    $raw_page = $request->get_param('page');
    $media_id = $request->get_param('id');

    if ($media_id !== null) {
        // Retrieve a single media item by ID
        $single_media = get_post($media_id);

        if (!$single_media || $single_media->post_type !== 'attachment') {
            wp_send_json(['error' => 'Media not found'], 404);
        }

        $response = [
            'id' => $single_media->ID,
            'rendered' => wp_get_attachment_url($single_media->ID),
            'title' => get_the_title($single_media->ID),
            'mime_type' => get_post_mime_type($single_media->ID),
            'file_format' => pathinfo(get_attached_file($single_media->ID), PATHINFO_EXTENSION),
            'alt_text' => get_post_meta($single_media->ID, '_wp_attachment_image_alt', true),
            'caption' => $single_media->post_excerpt,
        ];

        wp_send_json($response, 200);
    }

    if ($raw_page === null) {
        $page = 1;
    } elseif (!is_numeric($raw_page) || $raw_page <= 0) {
        wp_send_json(['error' => 'Invalid parameter(s): page'], 400);
    } else {
        $page = intval($raw_page);
    }

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

        wp_send_json($response, 200);
    } else {
        wp_send_json(['message' => 'The page number requested is larger than the number of pages available..'], 400);
    }
}
