<?php



require get_template_directory() . '/vendor/autoload.php';

add_action( 'wp_enqueue_scripts', 'iqms_assets', 11 );
function iqms_assets(){
    wp_enqueue_script('mdb-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js');
    wp_enqueue_script('jquery');
    wp_enqueue_script('iqms-global-scripts', get_stylesheet_directory_uri() . '/js/scripts.js');

    wp_enqueue_script('iqms-flickity.metafizzy-scripts', 'https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js');
    wp_enqueue_style('iqms-flickity.metafizzy-style', 'https://unpkg.com/flickity@2/dist/flickity.min.css');

    wp_enqueue_script('iqms-slick-scripts', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js');
    wp_enqueue_style('iqms-slick-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');

    wp_enqueue_script('iqms-magnific-popup-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js');
    wp_enqueue_style('iqms-magnific-popup-style', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css');

}


function custom_wp_mail_from($from_email) {
    return 'zcmc-iqms@zcmc-iqms.infoadvance.com.ph'; // Change to the email address you want to use as the sender
}

function custom_wp_mail_from_name($from_name) {
    return 'zcmc-iqms'; // Change to the name you want to use as the sender
}

add_filter('wp_mail_from', 'custom_wp_mail_from');
add_filter('wp_mail_from_name', 'custom_wp_mail_from_name');


function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
    </div>
    <?php
}

add_action( 'admin_notices', 'sample_admin_notice__success' );


/**
 * Register all the modules included on the module directory
 */
add_action( 'after_setup_theme', 'modules_require' );
function modules_require() {
    $modules = glob( plugin_dir_path( __FILE__ ) . 'modules/' . '*' , GLOB_ONLYDIR );
    if( $modules ) {
        foreach( $modules as $module ) {
            if( file_exists( $module . '/module.php' ) ) {
                require_once( $module . '/module.php' );
            }
        }
    }
}

add_theme_support( 'menus' );

/*
  Admin Bar Tweak. This changes the default CSS added by WordPress to place the admin bar margin in the body element instead of the html element.

Add this code to your active theme's functions.php or a custom plugin.
*/
add_theme_support( 'admin-bar', array( 'callback' => 'my_admin_bar_css') );
function my_admin_bar_css()
{
?>
<style type="text/css" media="screen">
	html body { margin-top: 0 !important; }
</style>
<?php
}


function gp_get_cmb_options_array_tax( $taxonomy, $args = array() ) {

    if ( empty( $taxonomy ) ) { return; }

    $defaults = array(
        'hide_empty' => 0,
    );

    $args = wp_parse_args( $args, $defaults );
    $terms = get_terms( $taxonomy, $args );


    $hierarchy = _get_term_hierarchy( $taxonomy );

    $term_list = array();
    foreach ( $terms as $term ) {

        if( $term->parent ) {
            continue;
        }

        $term_list[ $term->term_id ] = $term->name;

        if( isset( $hierarchy[ $term->term_id ] ) ) {

            foreach ( $hierarchy[ $term->term_id ] as $child ) {

                $child = get_term( $child, $taxonomy );
                $term_list[ $child->term_id ] = $term->name . ' > ' . $child->name;

                if( !isset( $hierarchy[ $child->term_id ] ) )
                    continue;

                foreach ($hierarchy[ $child->term_id ] as $subchild) {

                    $subchild = get_term( $subchild, $taxonomy );
                    $term_list[ $subchild->term_id ] = $term->name . ' > ' . $child->name. ' > ' .$subchild->name;

                }

            }

        }

    }

    return $term_list;

}

function sort_hierarchical(array &$cats, array &$into, $parent_id = 0){

    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parent_id) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $top_cat) {
        $top_cat->children = array();
        sort_hierarchical($cats, $top_cat->children, $top_cat->term_id);
    }
}

add_theme_support('post-thumbnails', array(
    'news-events',
));

function custom_after_login_action($user_login, $user) {

    if(get_option('visitor_count')){
        $options = get_option('visitor_count');
        $options =  intval(intval($options) + 1);
        update_option( 'visitor_count',  $options);
    } else {
        $options =  1;
        add_option( 'visitor_count',  $options);
    }
}

// Hook the custom action to the wp_login hook
add_action('wp_login', 'custom_after_login_action', 10, 2);



add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/generate-token/', array(
        'methods' => 'GET',
        'callback' => 'generate_user_token',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
});

function generate_user_token() {
    $user = wp_get_current_user();
    if ($user->ID) {
        $token = wp_generate_password(20, false);
        update_user_meta($user->ID, 'auth_token', $token);
        return new WP_REST_Response(['token' => $token], 200);
    }
    return new WP_REST_Response(['error' => 'User not logged in'], 401);
}




function hide_field_based_on_role() {
    // Check if the current user has the "dco" role
    if (!current_user_can('dco')) {
        ?>
        <style>
            .auto-approve {
                display: none;
            }
        </style>
        <?php
    }
}
add_action('wp_footer', 'hide_field_based_on_role');


// Add custom field on the Add New User page and Edit User page
function add_services_field_to_user($user) {
    // Get all terms from the 'services' taxonomy in the 'dcm' post type
    $terms = get_terms(array(
        'taxonomy' => 'services',
        'hide_empty' => false,
    ));

    // Get saved value for this user (if you're editing)
    $user_service_term = get_user_meta($user->ID, 'user_service_term', true);
    ?>
    <h3>Select Department/section/unit</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_service_term">Select Department/section/unit</label></th>
            <td>
                <select name="user_service_term" id="user_service_term">
                    <option value="">Select Department/section/unit</option>
                    <?php
                    if (!empty($terms) && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->term_id) . '" ' . selected($user_service_term, $term->term_id, false) . '>' . esc_html($term->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Hook into the 'user_new_form' and 'edit_user_profile' actions to display the custom field
add_action('user_new_form', 'add_services_field_to_user');
add_action('show_user_profile', 'add_services_field_to_user');
add_action('edit_user_profile', 'add_services_field_to_user');

// Save custom field value when user is added/updated
function save_services_field_to_user($user_id) {
    // Check if the value is set and save it
    if (isset($_POST['user_service_term'])) {
        update_user_meta($user_id, 'user_service_term', sanitize_text_field($_POST['user_service_term']));
    }
}

// Hook into the 'user_register', 'personal_options_update', and 'edit_user_profile_update' actions to save the custom field value
add_action('user_register', 'save_services_field_to_user');
add_action('personal_options_update', 'save_services_field_to_user'); // Allow users to update their own profile
add_action('edit_user_profile_update', 'save_services_field_to_user');

if(isset($user_id)){
    $user_service_term = get_user_meta($user_id, 'user_service_term', true);
    $term = get_term($user_service_term, 'services');

    if ($term && !is_wp_error($term)) {
        echo 'Services: ' . esc_html($term->name);
    }

}

function remove_services_metabox() {
    remove_meta_box('servicesdiv', 'dcm', 'side'); // 'servicesdiv' is the ID of the taxonomy metabox
}
add_action('admin_menu', 'remove_services_metabox');

// Add a custom metabox with a dropdown for the 'services' taxonomy
function add_custom_services_dropdown_metabox() {
    add_meta_box('custom_services_dropdown', 'Select Department/section/unit', 'custom_services_dropdown_callback', 'dcm', 'side', 'default');
}
add_action('add_meta_boxes', 'add_custom_services_dropdown_metabox');

function custom_services_dropdown_callback($post) {
    global $current_user;
    wp_get_current_user();

    // Get the user's saved service term from user meta
    $services_term = wp_get_post_terms($post->ID, 'services', array('fields' => 'ids'));
    $services = get_user_meta($current_user->ID, 'user_service_term', true);

    // Get all terms in the 'services' taxonomy
    $terms = get_terms(array(
        'taxonomy' => 'services',
        'hide_empty' => false,
    ));
    $disabled = 'disabled';
    // Output a dropdown (select box)
    if (!empty($services_term)) {
        $disabled = '';
    }
    echo '<select name="post_services_term" id="post_services_term" '.$disabled.'>';
    echo '<option value="">Select a Service</option>'; // Default option
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            // Preselect the user's saved service

            if(!empty($services_term)){
                $selected = (!empty($services_term) && $services_term[0] == $term->term_id) ? 'selected="selected"' : '';
                if((!empty($services_term) && $services_term[0] == $term->term_id)){
                    $selected_for_hidden =  esc_attr($term->term_id);
                }
            } else {
                $selected = ($services == $term->term_id) ? 'selected="selected"' : '';
                if($services == $term->term_id){
                    $selected_for_hidden = esc_attr($term->term_id);
                }

            }

            echo '<option value="' . esc_attr($term->term_id) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
    }
    echo '</select>';

    if (!empty($disabled)) {
        echo '<input type="hidden" name="post_services_term" value="' . $selected_for_hidden . '">';
    }
}

// Save the selected 'services' taxonomy term from the dropdown
function save_selected_service_term($post_id) {


    // Verify if this is not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the service term is set in the POST request
    if (isset($_POST['post_services_term']) && $_POST['post_services_term'] !== '') {

        $service_term_id = intval($_POST['post_services_term']);

        // Set the taxonomy term for the 'dcm' post type
        wp_set_post_terms($post_id, array($service_term_id), 'services');
    } else {
        // If no term is selected, remove the term
        wp_set_post_terms($post_id, array(), 'services');
    }

    // Save selected 'document_type' term
    if (isset($_POST['post_document_type_term']) && $_POST['post_document_type_term'] !== '') {
        $document_type_term_id = intval($_POST['post_document_type_term']);
        wp_set_post_terms($post_id, array($document_type_term_id), 'document_type');
    } else {
        wp_set_post_terms($post_id, array(), 'document_type'); // Remove if no term selected
    }

    // Save selected 'documents_label' term
    if (isset($_POST['post_documents_label_term']) && $_POST['post_documents_label_term'] !== '') {
        $documents_label_term_id = intval($_POST['post_documents_label_term']);
        wp_set_post_terms($post_id, array($documents_label_term_id), 'documents_label');
    } else {
        wp_set_post_terms($post_id, array(), 'documents_label'); // Remove if no term selected
    }
}
add_action('save_post_dcm', 'save_selected_service_term');

// Remove default taxonomy metaboxes for 'document_type' and 'documents_label'
function remove_document_type_and_label_metaboxes() {
    remove_meta_box('document_typediv', 'dcm', 'side'); // 'document_typediv' is the default metabox ID for 'document_type'
    remove_meta_box('documents_labeldiv', 'dcm', 'side'); // 'documents_labeldiv' is the default metabox ID for 'documents_label'
}
add_action('admin_menu', 'remove_document_type_and_label_metaboxes');

// Add custom metaboxes with dropdowns for 'document_type' and 'documents_label' taxonomies
function add_custom_document_dropdowns_metaboxes() {
    add_meta_box('custom_document_type_dropdown', 'Select Document Type', 'custom_document_type_dropdown_callback', 'dcm', 'side', 'default');
    add_meta_box('custom_documents_label_dropdown', 'Select Document Label', 'custom_documents_label_dropdown_callback', 'dcm', 'side', 'default');
}
add_action('add_meta_boxes', 'add_custom_document_dropdowns_metaboxes');

// Callback for 'document_type' dropdown
function custom_document_type_dropdown_callback($post) {
    // Get all terms in the 'document_type' taxonomy
    $terms = get_terms(array(
        'taxonomy' => 'document_type',
        'hide_empty' => false,
    ));

    // Get the selected term for the current post
    $selected_term = wp_get_post_terms($post->ID, 'document_type', array('fields' => 'ids'));

    // Output a dropdown (select box)
    echo '<select name="post_document_type_term" id="post_document_type_term" required>';
    echo '<option value="">Select Document Type</option>'; // Default option
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $selected = (!empty($selected_term) && $selected_term[0] == $term->term_id) ? 'selected="selected"' : '';
            echo '<option value="' . esc_attr($term->term_id) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
    }
    echo '</select>';
}

// Callback for 'documents_label' dropdown
function custom_documents_label_dropdown_callback($post) {
    // Get all terms in the 'documents_label' taxonomy
    $terms = get_terms(array(
        'taxonomy' => 'documents_label',
        'hide_empty' => false,
    ));

    // Get the selected term for the current post
    $selected_term = wp_get_post_terms($post->ID, 'documents_label', array('fields' => 'ids'));

    // Output a dropdown (select box)
    echo '<select name="post_documents_label_term" id="post_documents_label_term">';
    echo '<option value="">Select Document Label</option>'; // Default option
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $selected = (!empty($selected_term) && $selected_term[0] == $term->term_id) ? 'selected="selected"' : '';
            echo '<option value="' . esc_attr($term->term_id) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
    }
    echo '</select>';
}


function disable_drag_and_drop_script() {
    global $pagenow;

    // Only enqueue the script on the post add/edit pages
    if (in_array($pagenow, array('post.php', 'post-new.php'))) {
        wp_enqueue_script('disable-drag', get_template_directory_uri() . '/js/disable-drag.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'disable_drag_and_drop_script');

// Ensure PhpSpreadsheet classes are loaded (if using Composer)
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Hook for admin-post.php action for non-logged-in users (use 'admin_post_' for logged-in users)
add_action('admin_post_nopriv_download_ncar_report', 'download_ncar_report');
add_action('admin_post_download_ncar_report', 'download_ncar_report');

function download_ncar_report() {
    // Check if PhpSpreadsheet is available
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        // Include PhpSpreadsheet autoload (if you're using it manually)
        require get_template_directory() . '/vendor/autoload.php';
    }

    // Query NCAR posts
    $args = [
        'post_type' => 'ncar',
        'posts_per_page' => -1
    ];
    $query = new WP_Query($args);

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the headers for the spreadsheet
    $sheet->setCellValue('A1', 'NCAR No.')
          ->setCellValue('B1', 'Department/Services')
          ->setCellValue('C1', 'Date Issued')
          ->setCellValue('D1', 'Description of Non Conformity')
          ->setCellValue('E1', 'Root Cause Analysis')
          ->setCellValue('F1', 'Corrective Action')
          ->setCellValue('G1', 'Implemented Date')
          ->setCellValue('H1', 'Date Verified Implemented')
          ->setCellValue('I1', 'Date Verified Effective')
          ->setCellValue('J1', 'Status')
          ->setCellValue('K1', 'Remarks');

    // Populate the data in the spreadsheet from NCAR posts
    $row = 2;
    foreach ($query->posts as $ncar) {
        $id = $ncar->ID;
        $author = get_user_by('ID', $ncar->post_author)->data->display_name;
        $ncar_no_new = get_post_meta($id, 'ncar_no_new', true);
        $status = get_post_meta($id, 'status', true);
        $source = get_post_meta($id, 'source_of_nc', true);
        $department = get_post_meta($id, 'department', true);
        $date = get_post_meta($id, 'add_date', true);
        $clause_no = get_post_meta($id, 'clause_no', true);
        $close_date = get_post_meta($id, 'close_date', true);

        $description_of_the_noncomformity = get_post_meta($id, 'description_of_the_noncomformity', true);

        $reviewed_by = get_post_meta($id, 'reviewed_by', true);
        $reviewed_by_person = get_user_by('ID', $reviewed_by)->data->display_name;

        $followup_by = get_post_meta($id, 'followup_by', true);
        $followup_by_person = get_user_by('ID', $followup_by)->data->display_name;

        $approved_by = get_post_meta($id, 'approved_by', true);
        $approved_by_person = get_user_by('ID', $approved_by)->data->display_name;

        $date_verified = get_post_meta($id, 'date_verified', true);

        $corrective_action_data = get_post_meta($id, 'corrective_action_data', true);

        $root_causes_array = [];
        $corrective_action_array = [];
        $corrective_date_array = [];

        // Loop through the corrective_action_data array and collect the root_causes and corrective_action
        if (!empty($corrective_action_data) && is_array($corrective_action_data)) {
            foreach ($corrective_action_data as $data) {
                if (!empty($data['root_causes'])) {
                    $root_causes_array[] = $data['root_causes'];
                }
                if (!empty($data['corrective_action'])) {
                    $corrective_action_array[] = $data['corrective_action'];
                }

                if (!empty($data['corrective_date']) && is_string($data['corrective_date'])) {
                    // Attempt to create a DateTime object to validate the date format
                    $date = DateTime::createFromFormat('Y-m-d', $data['corrective_date']);
                    if ($date && $date->format('Y-m-d') === $data['corrective_date']) {
                        $corrective_date_array[] = $data['corrective_date'];
                    }
                }

            }
        }
               // Get the latest date if there are dates in the array
        if (!empty($corrective_date_array)) {
            $latest_date_corrective = max($corrective_date_array);
            $date = new DateTime($latest_date_corrective);
            $date->modify('+7 days');
            $latest_date_plus_7 = $date->format('Y-m-d');
        }

        // Convert the arrays into comma-separated strings
        $root_causes = implode(', ', $root_causes_array);
        $corrective_action = implode(', ', $corrective_action_array);

        // Populate the sheet with NCAR data
        $sheet->setCellValue('A' . $row, $ncar_no_new ? $ncar_no_new : $ncar->ID)
              ->setCellValue('B' . $row, $department)
              ->setCellValue('C' . $row, $date)
              ->setCellValue('D' . $row, $description_of_the_noncomformity)
              ->setCellValue('E' . $row, $root_causes)
              ->setCellValue('F' . $row, $corrective_action)
              ->setCellValue('G' . $row, $latest_date_corrective)
              ->setCellValue('H' . $row, $date_verified)
              ->setCellValue('I' . $row, $close_date)
              ->setCellValue('J' . $row, $status)
              ->setCellValue('K' . $row, '');

        $row++;
    }

    // Set headers to force download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ncar_report.xlsx"');
    header('Cache-Control: max-age=0');

    // Write the spreadsheet to output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    // Exit to prevent any additional output
    exit();
}

// Add QMS report download handler and admin button
add_action('admin_post_nopriv_download_qms_report', 'download_qms_report_qms');
add_action('admin_post_download_qms_report', 'download_qms_report_qms');

/**
 * Output a "Download QMS Report" button on the qms-documents listing filters area.
 */
function iqms_qms_download_button() {
    global $typenow;
    if ( isset($typenow) && $typenow === 'qms-documents' ) {
        echo '<a href="' . esc_url( admin_url('admin-post.php?action=download_qms_report') ) . '" class="button">Download QMS Report</a>';
    }
}
add_action('restrict_manage_posts', 'iqms_qms_download_button');

function download_qms_report_qms() {
    // Ensure PhpSpreadsheet is available
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        require get_template_directory() . '/vendor/autoload.php';
    }

    $args = [
        'post_type' => 'qms-documents',
        'posts_per_page' => -1,
    ];
    $query = new WP_Query($args);

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headers as requested
    $sheet->setCellValue('A1', 'DEPARTMENT/ SECTION/ UNIT')
          ->setCellValue('B1', 'Document Type (s.a. Procedure, Operations Manual, Work Instructions)')
          ->setCellValue('C1', 'Document No.')
          ->setCellValue('D1', 'Rev no.')
          ->setCellValue('E1', 'DOCUMENT TITLE')
          ->setCellValue('F1', 'Effectivity Date');

    $row = 2;
    foreach ($query->posts as $post) {
        $id = $post->ID;

        // Department / services
        $services = wp_get_post_terms($id, 'services', array('fields' => 'names'));
        $department = !empty($services) ? implode(', ', $services) : '';

        // Document type (taxonomy)
        $doc_types = wp_get_post_terms($id, 'document_type', array('fields' => 'names'));
        $document_type = !empty($doc_types) ? implode(', ', $doc_types) : '';

        // Document no and revision - try ACF then postmeta
        $document_no = function_exists('get_field') ? get_field('document_id', $id) : false;
        if (empty($document_no)) $document_no = get_post_meta($id, 'document_id', true);

        $revision = function_exists('get_field') ? get_field('revision', $id) : false;
        if (empty($revision)) $revision = get_post_meta($id, 'revision', true);

        $title = get_the_title($id);

        $effectivity = function_exists('get_field') ? get_field('date_of_effectivity', $id) : false;
        if (empty($effectivity)) $effectivity = get_post_meta($id, 'date_of_effectivity', true);

        $sheet->setCellValue('A' . $row, $department)
              ->setCellValue('B' . $row, $document_type)
              ->setCellValue('C' . $row, $document_no)
              ->setCellValue('D' . $row, $revision)
              ->setCellValue('E' . $row, $title)
              ->setCellValue('F' . $row, $effectivity);

        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="qms_report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

function enable_update_button_for_correction() {
    // Check if we are on the post edit screen and the post is of type 'dcm'
    if (get_current_screen()->base == 'post' && isset($_GET['post'])) {
        global $post;

        // Only apply this if the post type is 'dcm'
        if ($post->post_type !== 'dcm') {
            return; // Do not apply to non-'dcm' post types
        }

        // Retrieve the current user ID
        $current_user_id = get_current_user_id();

        // Retrieve the prepared_by field (this is the user ID who prepared the post)
        $prepared_by = get_field('users', get_the_ID());

        // Retrieve the values for your conditions
        $dco_reviewed_status = get_field('dco_review_status', $post->ID);
        $reviewed_status = get_field('review_status', $post->ID);
        $approval_status = get_field('approval_status', $post->ID);
        $for_revision = get_field('for_revision', $post->ID);
        $assigned_dco_raw = get_field('assigned_dco', $post->ID);

        // Set display variable based on the retrieved field values
        $display = '';
        if (get_post_status($post->ID) == 'draft') {
            $display = 'For Correction';
        } else {
            if ($dco_reviewed_status == 'review') {
                $display = 'For Review (DCO Complied)';
            } else if ($dco_reviewed_status == 'yes') {
                if ($reviewed_status == 'yes') {
                    if ($approval_status == 'no') {
                        $display = 'For Correction';
                    } else if ($approval_status == 'review') {
                        $display = 'For Review (Complied)';
                    } else {
                        $display = 'For Approval';
                    }
                } else if ($reviewed_status == 'no') {
                    $display = 'For Correction';
                } else if ($reviewed_status == 'review') {
                    $display = 'For Review (Complied)';
                } else {
                    $display = 'For Recommendation';
                }
            } else if ($dco_reviewed_status == 'no') {
                $display = 'For Correction';
            } else {
                if ($for_revision[0] == 'yes') {
                    if (!empty($assigned_dco_raw)) {
                        $display = 'Initial Review';
                    } else {
                        $display = 'For Revision';
                    }
                } else {
                    $display = 'Initial Review';
                }
            }
        }

        // Enable the "Update" button only if the current user is the author OR prepared_by
        if ($current_user_id == $post->post_author || $current_user_id == $prepared_by) {
            ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    const updateButton = document.getElementById('publish');
                    const displayStatus = "<?php echo esc_js($display); ?>";

                    if (updateButton) {
                        // Initially disable the button
                        updateButton.disabled = true;
                        updateButton.style.pointerEvents = 'none';
                        updateButton.style.opacity = '0.5';

                        // Enable only if status is "For Correction"
                        if (displayStatus.includes('For Correction')) {
                            updateButton.disabled = false;
                            updateButton.style.pointerEvents = 'auto';
                            updateButton.style.opacity = '1';
                        }
                    }
                });
            </script>
            <?php
        }
    }
}
add_action('admin_footer', 'enable_update_button_for_correction');

function custom_redirect_based_on_login() {
    // Check if the current page is the homepage
    if (is_front_page()) {
        if (is_user_logged_in()) {
            // Redirect logged-in users to home.zcmc.ph
            wp_redirect('https://home.zcmc.ph');
        } else {
            // Redirect non-logged-in users to wp-admin
            wp_redirect(admin_url());
        }
        exit; // Ensure the redirection is executed
    }
}
add_action('template_redirect', 'custom_redirect_based_on_login');

function reposition_publish_metabox() {
    // Remove the default Publish metabox
    remove_meta_box('submitdiv', 'post', 'side');

    // Add the Publish metabox to a different location
    add_meta_box('submitdiv', __('Publish'), 'post_submit_meta_box', 'post', 'normal', 'low');
}

function force_all_postboxes_open() {
    add_action('admin_head', 'hide_postbox_toggles_css');
    add_action('admin_footer', 'disable_postbox_toggle_js');
    add_action('add_meta_boxes', 'add_postbox_class_filters', 999);
    add_filter('postbox_classes', 'force_postbox_open_classes', 10, 2);
    add_action('admin_init', 'clear_closed_postboxes_user_meta');
    add_filter('pre_update_user_meta', 'prevent_closed_postboxes_save', 10, 4);
    add_filter('pre_option_closedpostboxes', 'force_empty_closed_postboxes');
    add_filter('get_user_option_closedpostboxes', 'force_empty_closed_postboxes');
    add_action('init', 'add_closed_postboxes_filters');
}

function add_postbox_class_filters() {
    global $wp_meta_boxes;
    $screen = get_current_screen();
    if (!$screen) return;

    $screen_id = $screen->id;
    if (isset($wp_meta_boxes[$screen_id])) {
        foreach ($wp_meta_boxes[$screen_id] as $context => $priorities) {
            foreach ($priorities as $priority => $boxes) {
                foreach ($boxes as $box_id => $box) {
                    add_filter("postbox_classes_{$screen_id}_{$box_id}", 'remove_closed_class_from_postboxes', 10, 1);
                }
            }
        }
    }
    add_filter('postbox_classes_' . $screen_id, 'remove_closed_class_from_postboxes', 10, 1);
}

function add_closed_postboxes_filters() {
    $screens = array('post', 'page', 'dashboard', 'edit-post', 'edit-page', 'acf-field-group', 'attachment', 'nav-menus');
    $post_types = get_post_types(array('public' => true), 'names');
    $screens = array_merge($screens, $post_types);

    foreach ($post_types as $post_type) {
        $screens[] = 'edit-' . $post_type;
    }

    foreach ($screens as $screen) {
        add_filter("get_user_option_closedpostboxes_{$screen}", 'force_empty_closed_postboxes');
        add_filter("pre_get_user_option_closedpostboxes_{$screen}", 'force_empty_closed_postboxes');
    }
}

function force_empty_closed_postboxes($value) {
    return array();
}

function clear_closed_postboxes_user_meta() {
    $user_id = get_current_user_id();
    if ($user_id) {
        $meta_keys = get_user_meta($user_id);
        foreach ($meta_keys as $key => $value) {
            if (strpos($key, 'closedpostboxes_') === 0) {
                delete_user_meta($user_id, $key);
            }
        }
    }
}

function prevent_closed_postboxes_save($check, $object_id, $meta_key, $meta_value) {
    if (strpos($meta_key, 'closedpostboxes_') === 0) {
        return array();
    }
    return $check;
}

function force_postbox_open_classes($classes, $box_id) {
    return '';
}

function remove_closed_class_from_postboxes($classes) {
    if (is_array($classes)) {
        $classes = array_diff($classes, array('closed'));
    } else {
        $classes = str_replace('closed', '', $classes);
        $classes = trim(preg_replace('/\s+/', ' ', $classes));
    }
    return $classes;
}

function hide_postbox_toggles_css() {
    ?>
    <style type="text/css">
        .postbox .handlediv,
        .postbox .handle-actions .handlediv,
        .postbox .postbox-header .handle-actions .handlediv,
        .acf-postbox .handlediv {
            display: none !important;
        }
        .postbox.closed .inside,
        .acf-postbox.closed .inside,
        .acf-postbox.closed .acf-fields {
            display: block !important;
        }
    </style>
    <?php
}

function disable_postbox_toggle_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.postbox .hndle, .postbox .handlediv').off('click.postboxes');
            $('.postbox').removeClass('closed');
            if (typeof window.postboxes !== 'undefined') {
                window.postboxes.handle_click = function() { return false; };
            }
            $('.acf-postbox').removeClass('closed');
            $('.acf-postbox .inside').show();
            $('.postbox.closed').removeClass('closed').find('.inside').show();
        });
    </script>
    <?php
}
add_action('do_meta_boxes', 'reposition_publish_metabox');
add_action('admin_init', 'force_all_postboxes_open');