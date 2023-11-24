<?php

/**
 * Custom CSS API Integration
 *
 * This file contains functions to integrate and update CSS data from a custom API endpoint.
 *
 */

// Register the REST API endpoint for updating CSS data
function register_css_api_endpoint()
{
    register_rest_route(
        'custom/v1',
        '/update-css',
        [
            'methods' => 'POST',
            'callback' => 'update_css_data',
            'permission_callback' => 'user_authentication',
        ]
    );
}

// Hook into REST API initialization to register the endpoints
add_action('rest_api_init', 'register_css_api_endpoint');


function update_css_data()
{
    // Clear the cache for the specific cache key.
    delete_option('ct_css_api_data');

    // Call the function to fetch and cache CSS data.
    $success = fetch_and_cache_css_data();

    if ($success) {
        $response = [
            'message' => 'CSS has been updated',
        ];
        $status = 200;
    } else {
        $response = [
            'message' => 'There is some internal error. Please check middleware URL.',
        ];
        $status = 500;
    }

    return new WP_REST_Response($response, $status);
}


function fetch_and_cache_css_data()
{
    // Fetching the middleware URL from the WordPress options.
    $middleware = get_option('control_tower_middleware_url');

    // Creating URL for the API.
    $url = $middleware . '/slot/search/filtered?format=css';

    // Sending a GET request to get the data.
    $response = wp_remote_get($url);

    if (!is_wp_error($response)) {
        // Get the response body.
        $body = wp_remote_retrieve_body($response);

        // Storing the data in the options table directly.
        update_option('ct_css_api_data', $body);

        return true;
    }

    return false;
}


// Function to inject cached CSS data into the HTML
function inject_cached_css_data()
{
    $cached_css_data = get_option('ct_css_api_data');

    // Inject the cached CSS data into the HTML of the page.
    if (!empty($cached_css_data)) {
        echo '<style data-dxpstyle="no-flicker">' . esc_html($cached_css_data) . '</style>';
    } else {
        // Fetch and cache CSS data if not already cached
        fetch_and_cache_css_data();
        // Retrieve and display the newly cached data
        echo '<style data-dxpstyle="no-flicker">' . esc_html(get_option('ct_css_api_data')) . '</style>';
    }
}

if (get_option('enable') != '1') {
    delete_option('ct_css_api_data');
} else {
    add_action('wp_head', 'inject_cached_css_data');
}