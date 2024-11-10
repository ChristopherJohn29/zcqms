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

function custom_login_redirect($user_login, $user) {
    // Check if the user has logged in successfully
    if (isset($user->roles) && is_array($user->roles)) {
        // Redirect non-administrators to another website
        if (!in_array('administrator', $user->roles)) {
            wp_redirect( home_url( '/logged' ) );
            exit;
        }
    }
}


add_action('wp_login', 'custom_login_redirect', 10, 2);

function redirect_based_on_login_status() {
    // Check if the current URL contains 'logged' or 'out' and redirect to home.zcmc.ph
    $current_url = home_url( $_SERVER['REQUEST_URI'] );
    if ( strpos( $current_url, 'logged' ) !== false || strpos( $current_url, 'out' ) !== false ) {
        wp_redirect( 'https://home.zcmc.ph/' );
        exit();
    }

    // For the homepage, handle login status
    if ( is_home() || is_front_page() ) {
        if ( is_user_logged_in() ) {
            // Set cookie for logged-in users
            setcookie( 'my_custom_cookie', 'logged_in', time() + 3600, '/', '.infoadvance.com.ph', false, true );
            // Redirect logged-in users to '/logged' page
            wp_redirect( home_url( '/logged' ) );
            exit();
        } else {
            // Clear cookie for logged-out users (if needed)
            setcookie( 'my_custom_cookie', '', time() - 3600, '/', '.infoadvance.com.ph', false, true );
            // Redirect logged-out users to '/out' page
            wp_redirect( home_url( '/out' ) );
            exit();
        }
    }
}
add_action( 'template_redirect', 'redirect_based_on_login_status' );


add_action('rest_api_init', function () {
    header("Access-Control-Allow-Origin: https://home.zcmc.ph/"); // Replace with Site B's URL
    header("Access-Control-Allow-Credentials: true");
});

add_action('rest_api_init', function () {
    header("Access-Control-Allow-Origin: https://home.zcmc.ph/"); // Replace with Site B's URL
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
});


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

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/is-logged-in/', array(
        'methods' => 'POST',
        'callback' => 'check_user_logged_in',
        'permission_callback' => '__return_true', // Adjust for security
    ));
});

function check_user_logged_in(WP_REST_Request $request) {
    $token = sanitize_text_field($request->get_param('token'));
    $user_query = new WP_User_Query(array(
        'meta_key' => 'auth_token',
        'meta_value' => $token,
        'number' => 1,
    ));

    if (!empty($user_query->get_results())) {
        return new WP_REST_Response(['status' => 'logged_in'], 200);
    }
    return new WP_REST_Response(['status' => 'not_logged_in'], 200);
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
    <h3>Select Services</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_service_term">Select Services</label></th>
            <td>
                <select name="user_service_term" id="user_service_term">
                    <option value="">Select a Services</option>
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

// Hook into the 'user_register' and 'edit_user_profile_update' actions to save the custom field value
add_action('user_register', 'save_services_field_to_user');
add_action('edit_user_profile_update', 'save_services_field_to_user');

$user_service_term = get_user_meta($user_id, 'user_service_term', true);
$term = get_term($user_service_term, 'services');

if ($term && !is_wp_error($term)) {
    echo 'Services: ' . esc_html($term->name);
}

function remove_services_metabox() {
    remove_meta_box('servicesdiv', 'dcm', 'side'); // 'servicesdiv' is the ID of the taxonomy metabox
}
add_action('admin_menu', 'remove_services_metabox');

// Add a custom metabox with a dropdown for the 'services' taxonomy
function add_custom_services_dropdown_metabox() {
    add_meta_box('custom_services_dropdown', 'Select Service', 'custom_services_dropdown_callback', 'dcm', 'side', 'default');
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

    // Output a dropdown (select box)
    echo '<select name="post_services_term" id="post_services_term">';
    echo '<option value="">Select a Service</option>'; // Default option
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            // Preselect the user's saved service
            
            if(!empty($services_term)){
                $selected = (!empty($services_term) && $services_term[0] == $term->term_id) ? 'selected="selected"' : '';
            } else {
                $selected = ($services == $term->term_id) ? 'selected="selected"' : '';
            }
            
            echo '<option value="' . esc_attr($term->term_id) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
    }
    echo '</select>';
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
    echo '<select name="post_document_type_term" id="post_document_type_term">';
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
// Save the selected 'document_type' and 'documents_label' taxonomy terms
function save_selected_document_terms($post_id) {
    // Verify if this is not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
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
add_action('save_post_dcm', 'save_selected_document_terms');

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

        $description_of_the_noncomformity = get_post_meta($id, 'description_of_the_noncomformity', true);

        $reviewed_by = get_post_meta($id, 'reviewed_by', true);
        $reviewed_by_person = get_user_by('ID', $reviewed_by)->data->display_name;

        $followup_by = get_post_meta($id, 'followup_by', true);
        $followup_by_person = get_user_by('ID', $followup_by)->data->display_name;

        $approved_by = get_post_meta($id, 'approved_by', true);
        $approved_by_person = get_user_by('ID', $approved_by)->data->display_name;

        $corrective_action_data = get_post_meta($id, 'corrective_action_data', true);

        $root_causes_array = [];
        $corrective_action_array = [];
        
        // Loop through the corrective_action_data array and collect the root_causes and corrective_action
        if (!empty($corrective_action_data) && is_array($corrective_action_data)) {
            foreach ($corrective_action_data as $data) {
                if (!empty($data['root_causes'])) {
                    $root_causes_array[] = $data['root_causes'];
                }
                if (!empty($data['corrective_action'])) {
                    $corrective_action_array[] = $data['corrective_action'];
                }
            }
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
              ->setCellValue('G' . $row, '')
              ->setCellValue('H' . $row, '')
              ->setCellValue('I' . $row, '')
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

