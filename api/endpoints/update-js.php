<?php

/**
 * Custom JS API Integration
 *
 * This file contains functions to integrate and update JS data from a custom API endpoint.
 *
 */

// Register the REST API endpoint for updating JS data
function register_js_api_endpoint()
{
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
function update_js_data()
{
    // Clear WordPress cache for the specific cache key.
    delete_option('ct_js_api_data');

    // Call the function to fetch and cache JS data.
    if (fetch_and_cache_js_data()) {
        $response = array(
            'message' => 'JS has been updated',
        );
        $status = 200;
    } else {
        $response = [
            'message' => 'There is some internal error. Please check middleware URL.',
        ];
        $status = 500;
    }

    return new WP_REST_Response($response, $status);
}


function fetch_and_cache_js_data()
{
    // Fetching the middleware URL from the WordPress options.
    $middleware = get_option('control_tower_middleware_url');
    $scope = get_option('control_tower_scope');

    // Creating URL for the API.
    $url = $middleware . '/others/cache';
    $args = array(
        'headers' => array('sourcescope' => $scope)
    );

    // Sending a GET request to get the data.
    $response = wp_remote_get($url, $args);

    if (!is_wp_error($response)) {
        // Get the response body.
        $body = wp_remote_retrieve_body($response);

        // Decoding the JSON string into a PHP array.
        $data = json_decode($body, true);

        // Storing the data in the options table.
        update_option('ct_js_api_data', $data);

        return true;
    }

    return false;
}


// Function to inject cached JS data into the HTML
function inject_cached_js_data()
{
    $cached_js_data = get_option('ct_js_api_data');

    if (!empty($cached_js_data)) {
        // Ensure the data is in JSON format
        $json_data = wp_json_encode($cached_js_data);
        $scope = wp_json_encode(get_option('control_tower_scope'));

        echo <<<HTML
        <script async data-dxpscript="caching">
            (async function () {
                window.unomiOption || (window.unomiOption = {})
                window.dxpTracker || (window.dxpTracker = {})
                window.unomiOption = {
                    scope: $scope,
                    ...window.unomiOption,
                    ...$json_data
                }

                const scriptElement = document.createElement('script')
                scriptElement.textContent = atob(unomiOption.script)
                document.head.appendChild(scriptElement)
            })();
        </script>
        HTML;
    } else {
        fetch_and_cache_js_data();
    }
}

if (get_option('enable') != '1') {
    delete_option('ct_js_api_data');
} else {
    add_action('wp_head', 'inject_cached_js_data', 2);
}
