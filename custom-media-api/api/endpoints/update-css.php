<?php

// Register the REST API endpoint for updating CSS data
function register_css_api_endpoint() {
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

function update_css_data() {
    // Clear the cache for the specific cache key.
    delete_option('dxp_css_api_data');

    // Call the function to fetch and cache CSS data.
    fetch_and_cache_css_data();
    if(fetch_and_cache_css_data()){

        $response = [
            'message' => 'CSS has been updated',
        ];
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


function fetch_and_cache_css_data() {
    // Fetching the middleware URL from the WordPress options.
    $middleware = get_option('dxp_middleware_url');

    // Creating URL for the API.
    $url = $middleware . '/slot/search/filtered?format=css';

    // Sending a GET request to get the data.
    $response = wp_remote_get($url);

    if (!is_wp_error($response)) {
        // Get the response body.
        $body = wp_remote_retrieve_body($response);

        // Storing the data in the options table directly.
        update_option('dxp_css_api_data', $body);

        return true;
    } else {
        return false;
    }
}


// Function to inject cached CSS data into the HTML
function inject_cached_css_data() {
    $cached_css_data = get_option('dxp_css_api_data');

    // Inject the cached CSS data into the HTML of the page.
    if (!empty($cached_css_data)) {
        echo '<style>' . esc_html($cached_css_data) . '</style>';
    }
    else{
        fetch_and_cache_css_data();
        echo '<style>' . esc_html(get_option('dxp_css_api_data')) . '</style>';
    }
}
add_action('wp_head', 'inject_cached_css_data');
