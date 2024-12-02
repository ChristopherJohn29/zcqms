<?php
if ( !class_exists( 'Custom_Dashboard' ) ) {
	class Custom_Dashboard {

		function __construct() {
			add_action( 'wp_dashboard_setup', array( $this, 'custom_dashboard_cb' ), 1000000 );
		}

		function custom_dashboard_cb() {

		    global $wp_meta_boxes;
		    /*remove dashboards first*/
		    unset( $wp_meta_boxes['dashboard'] );
		    /*add custom dashboard*/
		    add_meta_box( 'custom_dashboard_widget',
				__( '-', 'example-text-domain' ),
				array( $this, 'render_example_widget' ),
				'dashboard',
				'normal',
				'core',
			);
		}

		function render_example_widget() {
			require_once( 'dashboard.html.php' );
		}

	}
	$Custom_Dashboard = new Custom_Dashboard();
}