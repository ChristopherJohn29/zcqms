<?php 

function redirect_on_post_update($post_ID, $post, $update) {
    // Check if the post is being updated and not a new post
    if ($update) {
        // Check if it's of a specific post type (replace 'your_post_type' with your actual post type)
        if ($post->post_type === 'printing') {
            // Define the URL you want to redirect to
            $redirect_url = home_url().'/wp-admin/edit.php?post_type=printing'; // Change to your desired destination URL

            // Perform the redirection
            wp_redirect($redirect_url);
            exit;
        }
    }
}

add_action('wp_insert_post', 'redirect_on_post_update', 10, 3);