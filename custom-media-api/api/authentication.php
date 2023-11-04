<?php
/**
 * User Authentication for Custom Media API Endpoints.
 *
 * This function performs user authentication for Custom Media API endpoints using HTTP Basic Authentication.
 *
 * @param WP_REST_Request $request The incoming API request.
 *
 * @return bool|WP_Error Returns true if authentication is successful and the user has the required
 * permissions for the requested API method. Returns a WP_Error object with appropriate messages and
 * status codes for authentication and permission failures.
 */
function user_authentication($request)
{
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Authenticating user
        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            wp_send_json(['error' => 'rest_forbidden', 'message' => 'Invalid credentials.'], 401);
        } else {
            if ($user->has_cap('read')) {
                if ($request->get_method() === 'GET') {
                    return true;
                }

                if ($request->get_method() === 'POST' && $user->has_cap('upload_files')) {
                    return true;
                }

                if ($request->get_method() === 'DELETE' && $user->has_cap('delete_posts')) {
                    return true;
                }
            } else {
                wp_send_json(['error' => 'rest_forbidden', 'message' => 'Insufficient permissions.'], 403);
            }
        }
    }

    wp_send_json(['error' => 'rest_forbidden', 'message' => 'Authentication required.'], 401);
    return false;
}
