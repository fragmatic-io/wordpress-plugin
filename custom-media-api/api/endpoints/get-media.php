<?php
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/get-media', [
        'methods' => 'GET',
        'callback' => 'get_media',
        'permission_callback' => 'user_authentication',
    ]);
});

function get_media($request) {
    $per_page = intval(get_option('custom_media_api_per_page', 5));
    // $per_page = 5; // Set the number of attachments to show per page.
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

            wp_send_json($response, 200);
        }
        else{
            // 404 (Not Found) when no media items are found
            wp_send_json_error(['error' => 'No media items found for the given page'], 204);
        }
    }
}