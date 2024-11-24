<?php 

function add_custom_filter_to_admin_table() {
    // Replace 'dcm' with your custom post type name
    $post_type = 'dcm';
    if (isset($_GET['post_type']) && $_GET['post_type'] == $post_type) {
        $taxonomy = 'services'; // Replace with your taxonomy name
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        if (!empty($terms)) {
            echo '<select name="filter_by_service">';
            echo '<option value="">' . __('All Services', 'text-domain') . '</option>';
            foreach ($terms as $term) {
                $selected = isset($_GET['filter_by_service']) && $_GET['filter_by_service'] == $term->slug ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action('restrict_manage_posts', 'add_custom_filter_to_admin_table');

function filter_posts_by_custom_taxonomy($query) {
    global $pagenow;

    $post_type = 'dcm'; // Replace with your custom post type name
    $taxonomy = 'services'; // Replace with your taxonomy name

    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $post_type && !empty($_GET['filter_by_service'])) {
        $query->query_vars['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $_GET['filter_by_service'],
            ),
        );
    }
}
add_action('pre_get_posts', 'filter_posts_by_custom_taxonomy');
