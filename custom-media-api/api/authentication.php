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
            return true;
        }
    }

    return new WP_Error('rest_forbidden', 'Authentication required.', ['status' => 401]);
}
