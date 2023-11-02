<?php
function user_authentication($request) {
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        return true;
    }

    return new WP_Error('rest_forbidden', 'Authentication required.', ['status' => 401]);
}
