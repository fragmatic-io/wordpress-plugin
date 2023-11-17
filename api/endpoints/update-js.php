<?php

/**
 * Custom JS API Integration
 *
 * This file contains functions to integrate and update JS data from a custom API endpoint.
 *
 */

// Register the REST API endpoint for updating JS data
function register_js_api_endpoint() {
    register_rest_route(
        'custom/v1',
        '/update-js',
        [
            'methods' => 'POST',
            'callback' => 'update_js_data',
            'permission_callback' => 'user_authentication',
        ]
    );
}

// Hook into REST API initialization to register the endpoints
add_action('rest_api_init', 'register_js_api_endpoint');


// Function to update Js data and clear cache
function update_js_data() {
    // Clear WordPress cache for the specific cache key.
    delete_option('dxp_js_api_data');

    // Call the function to fetch and cache JS data.
    fetch_and_cache_js_data();

    if(fetch_and_cache_js_data()){
    $response = array(
        'message' => 'JS has been updated',
    );
    $status = 200;

    $response = new WP_REST_Response($response, $status);
    }
    else{
        $response = [
            'message' => 'There is some internal error. Please check middleware URL.',
        ];
        $status = 500;

        $response = new WP_REST_Response($response, $status);
    }

return $response;
}


function fetch_and_cache_js_data() {
    // Fetching the middleware URL from the WordPress options.
    $middleware = get_option('dxp_middleware_url');

    // Creating URL for the API.
    $url = $middleware . '/others/cache';


    // Sending a GET request to get the data.
    $response = wp_remote_get($url);

    if (!is_wp_error($response)) {
        // Get the response body.
        $body = wp_remote_retrieve_body($response);

        // Storing the data in the options table directly.
        update_option('dxp_js_api_data', $body);

        return true;
    } else {
        return false;
    }
}


// Function to inject cached JS data into the HTML
function inject_cached_js_data() {
    $cached_js_data = get_option('dxp_js_api_data');

    // Inject the cached JS data into the HTML of the page.
    if (!empty($cached_js_data)) {
        echo '<script>' . esc_html($cached_js_data) . '</script>';
    }
    else{
        fetch_and_cache_js_data();
        echo '<script>' . esc_html(get_option('dxp_js_api_data')) . '</script>';
    }
}
add_action('wp_head', 'inject_cached_js_data');