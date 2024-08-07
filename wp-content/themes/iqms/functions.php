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

function add_cors_http_header() {
    // Allow requests from the specific domain
    header("Access-Control-Allow-Origin: https://home.zcmc.ph"); 
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}

add_action('init', 'add_cors_http_header');

function custom_login_status_endpoint() {
    register_rest_route('custom/v1', '/login-status', array(
        'methods' => 'GET',
        'callback' => 'get_login_status',
        'permission_callback' => '__return_true', // Allows all requests
    ));
}
add_action('rest_api_init', 'custom_login_status_endpoint');

// Callback function to handle the request
function get_login_status() {
    error_log('Request received at /login-status endpoint');
    error_log('Session ID: ' . session_id());
    error_log('Cookies: ' . print_r($_COOKIE, true));
    error_log('Session Data: ' . print_r($_SESSION, true));
    
    if (is_user_logged_in()) {
        error_log('User is logged in'); // Log when user is logged in
        return new WP_REST_Response(array('status' => 'logged_in'), 200);
    } else {
        error_log('User is logged out'); // Log when user is logged out
        return new WP_REST_Response(array('status' => 'logged_out'), 200);
    }
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