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
        'permission_callback' => 'user_authentication',
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
    $page = $request['page'];

    if (isset($page) && (!is_numeric($page) || $page < 1)) {
        wp_send_json_error(['error' => 'Invalid page parameter'], 400);
    } else {
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
            wp_send_json_error(['error' => 'No media items found for the given page'], 204);
        }
    }
}