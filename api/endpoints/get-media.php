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
    $per_page = $request->get_param('size');
    $raw_page = $request->get_param('page');
    $media_id = $request->get_param('id');
    $search_name = $request->get_param('name');

    if ($media_id !== null) {
        $single_media = get_post($media_id);

        if (!$single_media || $single_media->post_type !== 'attachment') {
            return new WP_Error('media_not_found', 'Media not found', ['status' => 404]);
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

        return new WP_REST_Response($response, 200);
    }

    $page = 1;
    if ($raw_page !== null) {
        $page = max(1, intval($raw_page));
    }

    if ($per_page === null) {
        $per_page = 10;
    } else {
        $per_page = max(1, intval($per_page));
    }

    $offset = ($page - 1) * $per_page;

    $query_args = [
        'post_type' => 'attachment',
        'posts_per_page' => $per_page,
        'offset' => $offset,
        'post_status' => 'inherit',
    ];

    if (!empty($search_name)) {
        $query_args['s'] = $search_name;
    }
    
    $media_query = new WP_Query($query_args);

    // TOOD: deprecate with more complex search query
    if (!empty($search_name) && $media_query->found_posts === 0) {
        unset($query_args['s']);
        $query_args['name'] = $search_name;
        $media_query = new WP_Query($query_args);
    }

    $total_items = $media_query->found_posts;
    $total_pages = ceil($total_items / $per_page);

    if ($page > $total_pages && $total_items > 0) {
        return new WP_Error('invalid_page', 'Invalid page, please check!', ['status' => 400]);
    }

    $response = [
        'results' => array_map(function ($item) {
            return [
                'mid' => $item->ID,
                'link' => wp_get_attachment_url($item->ID),
                'name' => $item->post_name,
                'status' => $item->post_status,
                'mime_type' => get_post_mime_type($item->ID),
                'file_format' => pathinfo(get_attached_file($item->ID), PATHINFO_EXTENSION),
                'alt_text' => get_post_meta($item->ID, '_wp_attachment_image_alt', true),
                'caption' => $item->post_excerpt,
                'created' => $item->post_date_gmt,
            ];
        }, $media_query->posts),
        'pager' => [
            'count' => $total_items,
            'pages' => $total_pages,
            'items_per_page' => $per_page,
            'current_page' => $page,
            'next_page' => $page < $total_pages ? $page + 1 : null,
        ]
    ];

    return new WP_REST_Response($response, 200);
}
