<?php
function user_authentication($request) {
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Authenticating user
        $user  = wp_authenticate($username, $password);

        if(is_wp_error($user)){
            return new WP_Error('rest_forbidden', 'Invalid credentials.', ['status' => 401]);
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
                 if ($request->get_method() === 'DELETE' && $user->has_cap('delete_files')) {
                    return true;
                }
                return new WP_Error('rest_forbidden', 'Insufficient permissions.', ['status' => 403]);
            }
            else {
                return new WP_Error('rest_forbidden', 'Insufficient permissions.', ['status' => 403]);
            }
        }
    }

    return new WP_Error('rest_forbidden', 'Authentication required.', ['status' => 401]);
}
