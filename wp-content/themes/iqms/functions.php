<?php



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
    $user_service_term = get_user_meta($current_user->ID, 'user_service_term', true);
    
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
            $selected = ($user_service_term == $term->term_id) ? 'selected="selected"' : '';
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
add_action('save_post', 'save_selected_service_term');
