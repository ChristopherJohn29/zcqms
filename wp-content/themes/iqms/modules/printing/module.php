<?php 

function redirect_on_post_update($post_id) {
    // Check if this is a post update (not a new post)
    if (wp_is_post_revision($post_id) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Get the post object
    $post = get_post($post_id);

    // Check if it's of a specific post type (replace 'your_post_type' with your actual post type)
    if ($post->post_type === 'printing') {
        // Define the URL you want to redirect to
        $redirect_url = home_url().'/wp-admin/edit.php?post_type=printing'; // Change to your desired destination URL

        // Perform the redirection
        // wp_redirect($redirect_url);
        exit;
    }
}

add_action('save_post', 'redirect_on_post_update');