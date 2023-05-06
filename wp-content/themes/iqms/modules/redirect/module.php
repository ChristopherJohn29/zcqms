<?php 

function frontend_redirect() {
   

    if ( is_home() ) {
        wp_redirect( get_site_url().'/services/medical-services/', 301 );
        exit();
    }
} 

// add_action( 'template_redirect', 'frontend_redirect', 10 );