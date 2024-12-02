<?php
function load_custom_wp_login_style(){
    wp_enqueue_style( 'custom-css', get_stylesheet_directory_uri() . '/modules/login-enqueue/css/styles.css' );
    // wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/modules/admin-enqueue/js/scripts.js' );
}
add_action('login_enqueue_scripts', 'load_custom_wp_login_style');