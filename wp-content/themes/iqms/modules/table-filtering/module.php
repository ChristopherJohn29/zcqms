<?php 

function add_custom_filter_to_admin_table() {
    // Replace 'dcm' with your custom post type name
    $post_type = 'dcm';
    if (isset($_GET['post_type']) && $_GET['post_type'] == $post_type) {
        // Filter by 'services' taxonomy
        $services_taxonomy = 'services'; // Replace with your taxonomy name
        $services_terms = get_terms(array(
            'taxonomy' => $services_taxonomy,
            'hide_empty' => false,
        ));

        if (!empty($services_terms)) {
            echo '<select name="filter_by_service">';
            echo '<option value="">' . __('All Services', 'text-domain') . '</option>';
            foreach ($services_terms as $term) {
                $selected = isset($_GET['filter_by_service']) && $_GET['filter_by_service'] == $term->slug ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }

        // Filter by 'document_type' taxonomy
        $document_type_taxonomy = 'document_type'; // Replace with your taxonomy name
        $document_type_terms = get_terms(array(
            'taxonomy' => $document_type_taxonomy,
            'hide_empty' => false,
        ));

        if (!empty($document_type_terms)) {
            echo '<select name="filter_by_document_type">';
            echo '<option value="">' . __('All Document Types', 'text-domain') . '</option>';
            foreach ($document_type_terms as $term) {
                $selected = isset($_GET['filter_by_document_type']) && $_GET['filter_by_document_type'] == $term->slug ? 'selected="selected"' : '';
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

    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $post_type) {
        $tax_query = array();

        // Filter by 'services' taxonomy
        if (!empty($_GET['filter_by_service'])) {
            $tax_query[] = array(
                'taxonomy' => 'services', // Replace with your taxonomy name
                'field'    => 'slug',
                'terms'    => $_GET['filter_by_service'],
            );
        }

        // Filter by 'document_type' taxonomy
        if (!empty($_GET['filter_by_document_type'])) {
            $tax_query[] = array(
                'taxonomy' => 'document_type', // Replace with your taxonomy name
                'field'    => 'slug',
                'terms'    => $_GET['filter_by_document_type'],
            );
        }

        if (!empty($tax_query)) {
            $query->query_vars['tax_query'] = $tax_query;
        }
    }
}
add_action('pre_get_posts', 'filter_posts_by_custom_taxonomy');

