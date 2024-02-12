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
    $per_page = max((int) $request->get_param('size'), 10);
    $page = max((int) $request->get_param('page'), 0);
    $media_id = $request->get_param('id');
    $search_name = $request->get_param('name');

    if ($media_id !== null) {
        $single_media = get_post($media_id);
        if (!$single_media || $single_media->post_type !== 'attachment') {
            return new WP_Error('media_not_found', 'Media not found', ['status' => 404]);
        }
        return new WP_REST_Response(get_media_response_data($single_media), 200);
    }

    // Define base query arguments
    $query_args = [
        'post_type'      => 'attachment',
        'post_mime_type' => ['image/jpeg', 'image/jpg', 'image/png'],
        'post_status'    => 'inherit',
        'posts_per_page' => $per_page,
        'paged'          => $page + 1,
        's'              => $search_name,
    ];

    // Fetch media items
    $media_items = new WP_Query($query_args);

    $total_items = $media_items->found_posts;
    $total_pages = $media_items->max_num_pages;

    if ($page >= $total_pages && $total_items > 0) {
        return new WP_Error('invalid_page', 'Invalid page, please check!', ['status' => 400]);
    }

    $response_data = array_map('get_media_response_data', $media_items->posts);
    $response = [
        'results' => $response_data,
        'pager'   => [
            'count'          => $total_items,
            'pages'          => $total_pages,
            'items_per_page' => $per_page,
            'current_page'   => $page,
            'next_page'      => $page < $total_pages - 1 ? $page + 1 : null,
        ],
    ];

    return new WP_REST_Response($response, 200);
}

function get_media_response_data($media)
{
    return [
        'mid' => $media->ID,
        'link' => wp_get_attachment_url($media->ID),
        'name' => $media->post_name,
        'alt_text' => get_post_meta($media->ID, '_wp_attachment_image_alt', true),
        'created' => $media->post_date_gmt,
    ];
}
