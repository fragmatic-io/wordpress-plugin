<?php

add_action('admin_menu', 'custom_media_api_add_menu');

function custom_media_api_add_menu() {
    add_menu_page('Custom Media API Settings', 'Custom Media API', 'manage_options', 'custom-media-api-settings', 'custom_media_api_settings_page');
}

function custom_media_api_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Retrieve the current values of the configurable variables
    $file_ext = get_option('custom_media_api_file_ext', '');
    if (empty($file_ext)) {
        // If $file_ext is empty, use the default value
        $file_ext = 'jpeg,jpg,png,gif,webp,mp4';
    }

    $per_page = get_option('custom_media_api_per_page', 5);
    $max_size = get_option('custom_media_api_max_size', 2);

    if (isset($_POST['submit'])) {
        // Handle form submission and save the values
        $file_ext = sanitize_text_field($_POST['custom_media_api_file_ext']);

        // If user sets $file_ext empty, use the default value
        if($file_ext === ''){
            $file_ext = 'jpeg,jpg,png,gif,webp,mp4';
        }
        $per_page = intval($_POST['custom_media_api_per_page']);
        $max_size = intval($_POST['custom_media_api_max_size']);

        update_option('custom_media_api_file_ext', $file_ext);
        update_option('custom_media_api_per_page', $per_page);
        update_option('custom_media_api_max_size', $max_size);
    }

    // Display the settings form
    ?>
    <div class="wrap">
        <h2>Custom Media API Settings</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="custom_media_api_file_ext">Allowed File Extensions:</label>
                    </th>
                    <td>
                        <input type="text" id="custom_media_api_file_ext" name="custom_media_api_file_ext" value="<?php echo esc_attr($file_ext); ?>">
                        <p class="description">Enter allowed file extensions (comma-separated : jpg,png,etc)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="custom_media_api_per_page">Items Per Page:</label>
                    </th>
                    <td>
                        <input type="number" id="custom_media_api_per_page" name="custom_media_api_per_page" value="<?php echo esc_attr($per_page); ?>">
                        <p class="description">Number of items to show per page.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="custom_media_api_max_size">Maximum File Size (MB):</label>
                    </th>
                    <td>
                        <input type="number" id="custom_media_api_max_size" name="custom_media_api_max_size" value="<?php echo esc_attr($max_size); ?>">
                        <p class="description">Maximum file size in megabytes (MB).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
