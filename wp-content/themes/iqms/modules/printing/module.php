<?php 

function updatePrinting($meta_id, $post_id, $meta_key='', $meta_value='') {

    $post_type = get_post_type($post_id);
    
    if ($post_type === 'printing') {

       
        // wp_redirect(home_url().'/wp-admin/edit.php?post_type=printing');
    }
}


add_action('updated_post_meta', 'updatePrinting', 10, 4); 