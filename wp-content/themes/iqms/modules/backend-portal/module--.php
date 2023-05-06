<?php

if ( !class_exists('BackendPortal') ) {



	class BackendPortal {



		function __construct() {

			add_action( 'admin_menu', array( $this, 'backend_portal_page' ) );

		}



		function backend_portal_page() {



			add_menu_page(

				__( 'Backend Portal', 'my-textdomain' ),

				__( 'Backend Portal', 'my-textdomain' ),

				'manage_options',

				'backend-portal',

				array( $this, 'backend_portal_page_cb' ),

				'dashicons-info',

				999999

			);



		}



		function backend_portal_page_cb() {

			$taxonomy = 'services';
			$terms = get_terms([
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
			]);

			foreach ($terms as $term){
				echo $term->slug." : ";
				echo $term->name;
				echo "<br><br>";
			  }

		}

	}

	$BackendPortal = new BackendPortal();

}