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
function user_authentication($request) {
    if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
        wp_send_json(['error' => 'rest_forbidden', 'message' => 'Authentication required.'], 401);
    }

    $user = wp_authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

    if (is_wp_error($user)) {
        wp_send_json(['error' => 'rest_forbidden', 'message' => 'Invalid credentials.'], 401);
    }

    $method = $request->get_method();
    if ($method === 'POST' && !$user->has_cap('upload_files') ||
        $method === 'DELETE' && !$user->has_cap('delete_posts')) {
        wp_send_json(['error' => 'rest_forbidden', 'message' => 'Insufficient permissions.'], 403);
    }

    return true;
}
