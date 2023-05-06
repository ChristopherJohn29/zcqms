<?php
function load_custom_wp_admin_style(){
    wp_enqueue_style( 'custom-css', get_stylesheet_directory_uri() . '/modules/admin-enqueue/css/styles.css' );
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/modules/admin-enqueue/js/scripts.js' );
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');