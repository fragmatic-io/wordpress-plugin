<?php

/**
 * Authenticate a user for Custom Media API using HTTP Basic Authentication.
 *
 * @param WP_REST_Request $request The incoming API request.
 *
 * @return bool|WP_Error Returns true if authenticated with the required
 * permissions for the requested API method, or a WP_Error with
 * appropriate messages and status codes for failures.
 */
function user_authentication($request) {
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Authenticating user
        $user  = wp_authenticate($username, $password);

        if(is_wp_error($user)){
            wp_send_json(['error' => 'rest_forbidden', 'message' => 'Invalid credentials.'], 401);
        }
        else{
            // Check for specific permissions here
            if($user->has_cap('read')){
                // Allow access to the GET API
                if($request->get_method() === 'GET'){
                    return true;
                }

                // Check if the user has upload_files capability for POST requests
                if ($request->get_method() === 'POST' && $user->has_cap('upload_files')) {
                    return true;
                }

                 // Check if the user has delete_files capability for DELETE requests
                 if ($request->get_method() === 'DELETE' && $user->has_cap('delete_posts')) {
                    return true;
                }
            }
            else {
                wp_send_json(['error' => 'rest_forbidden', 'message' => 'Insufficient permissions.'], 403);
            }
        }
    }

    wp_send_json(['error' => 'rest_forbidden', 'message' => 'Authentication required.'], 401);
    return false;
}
