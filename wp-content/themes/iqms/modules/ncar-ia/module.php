<?php
if ( !class_exists('NCAR_IA_Module') ) {

	class NCAR_IA_Module {

		var $this_user;

		function __construct() {
			$this->this_user = get_current_user_id();
			add_action( 'admin_menu', array( $this, 'ncar_ia_main_module' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ncar_ia_resources' ) );
			/*post type*/

			add_action( 'init', array( $this, 'ncar_ia_post_type' ) );

			/*ncar ajax*/
			add_action('wp_ajax_ncar_ia_save', array($this, 'ncar_ia_save'));
        	add_action('wp_ajax_nopriv_ncar_ia_save', array($this, 'ncar_ia_save'));

			add_action('wp_ajax_ncar_ia_delete', array($this, 'ncar_ia_delete'));
        	add_action('wp_ajax_nopriv_ncar_ia_delete', array($this, 'ncar_ia_delete'));

			add_action('wp_ajax_ncar_ia_edit', array($this, 'ncar_ia_edit'));
        	add_action('wp_ajax_nopriv_ncar_ia_edit', array($this, 'ncar_ia_edit'));

			add_action('wp_ajax_ncar_ia_edit_save', array($this, 'ncar_ia_edit_save'));
        	add_action('wp_ajax_nopriv_ncar_ia_edit_save', array($this, 'ncar_ia_edit_save'));

			add_action('wp_ajax_ncar_ia_form2_save', array($this, 'ncar_ia_form2_save'));
        	add_action('wp_ajax_nopriv_ncar_ia_form2_save', array($this, 'ncar_ia_form2_save'));

			add_action('wp_ajax_ncar_ia_form3_save', array($this, 'ncar_ia_form3_save'));
        	add_action('wp_ajax_nopriv_ncar_ia_form3_save', array($this, 'ncar_ia_form3_save'));

			add_action('wp_ajax_ncar_ia_load_remarks', array($this, 'ncar_ia_load_remarks'));
        	add_action('wp_ajax_nopriv_ncar_ia_load_remarks', array($this, 'ncar_ia_load_remarks'));

			add_action('wp_ajax_ncar_ia_save_remarks', array($this, 'ncar_ia_save_remarks'));
        	add_action('wp_ajax_nopriv_ncar_ia_save_remarks', array($this, 'ncar_ia_save_remarks'));
		}

		function ncar_ia_save_remarks() {
			$data = $_POST['data'];

			$remarks = $data['remarks'];

			$post_id = $data['ncar_no'];
			$to_return = [];
			if ( $post_id ) {
				update_post_meta( $post_id, 'ncar_remarks', $remarks );
				$to_return = ['post_id' => $post_id];
			} else {
				$to_return = ['error' => true];
			}
			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_load_remarks() {
			if ( !$_POST['id'] ) 
				return false;

			$ncar = get_post( $_POST['id'] );
			$to_return = [];

			if ( $ncar ) {

				$id = $ncar->ID;

				$to_return['data'] = [
					'ncar_no' => $id,
					'remarks' => get_post_meta( $id, 'ncar_remarks', true ),
				];

			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_form3_save() {
			$data = $_POST['data'];

			$verification = $data['verification'];
			$correction = $data['correction'];

			$post_id = $data['ncar_no'];
			$satisfactory = $data['satisfactory'];
			
			$to_return = [];
			if ( $post_id ) {
				$current_correction = get_post_meta( $post_id, 'correction', true);

				foreach ($correction as $key => $value) {
					$correction[$key]['correction_text'] = $correction[$key]['correction_text'] ? $correction[$key]['correction_text'] : $current_correction[$key]['correction_text'];
					$correction[$key]['correction_date'] = $correction[$key]['correction_date'] ? $correction[$key]['correction_date'] : $current_correction[$key]['correction_date'];
					$correction[$key]['correction_implemented'] = $correction[$key]['correction_implemented'] ? $correction[$key]['correction_implemented'] : $current_correction[$key]['correction_implemented'];
					$correction[$key]['correction_remarks'] = $correction[$key]['correction_remarks'] ? $correction[$key]['correction_remarks'] : $current_correction[$key]['correction_remarks'];
				}

				if($satisfactory == 1){
					update_post_meta( $post_id, 'status', 'Satisfactory' );
				} else {
					update_post_meta( $post_id, 'status', 'For Improvement Action' );
				}

				$to_return = ['post_id' => $post_id];
				update_post_meta( $post_id, 'correction', $correction );
			} else {
				$to_return = ['error' => true];
			}

			

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_form2_save() {
			$data = $_POST['data'];

			$correction = $data['correction'];
			$files = ( $data['files'] ? $data['files'] : [] );
			$corrective_action_data = $data['corrective_action_data'];

			$post_id = $data['ncar_no'];
			$to_return = [];
			if ( $post_id ) {
				update_post_meta( $post_id, 'correction', $correction );
				update_post_meta( $post_id, 'files', $files );
				update_post_meta( $post_id, 'corrective_action_data', $corrective_action_data );
				update_post_meta( $post_id, 'status', 'For Follow up' );
				$to_return = ['post_id' => $post_id];
			} else {
				$to_return = ['error' => true];
			}
			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_edit_save() {

			$data = $_POST['data'];
			$evidences = ( $_POST['evidences'] ? $_POST['evidences'] : [] );

			$post_id = $_POST['ncar_no'];

			$to_return = [];
			if ( $post_id ) {
				foreach( $data as $field ) {
					update_post_meta( $post_id, $field['name'], $field['value'] );
				}

				$to_return = ['post_id' => $post_id];

				update_post_meta( $post_id, 'status', 'For Improvement Action' );
				update_post_meta( $post_id, 'evidences', $evidences );
			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_edit() {
			if ( !$_POST['id'] ) 
				return false;

			$ncar = get_post( $_POST['id'] );
			$to_return = [];

			if ( $ncar ) {

				$id = $ncar->ID;
				$to_return['cant_edit'] = ( $this->this_user == $ncar->post_author ? false : true );
				$to_return['cant_review'] = ( $this->this_user == get_post_meta( $ncar->ID, 'reviewed_by', true ) ? false : true );
				$to_return['cant_approve'] = ( $this->this_user == get_post_meta( $ncar->ID, 'approved_by', true ) ? false : true );

				$evidences_id = get_post_meta( $id, 'evidences', true );
				$evidences = [];
				foreach ( $evidences_id as $i ) {
					$attachment = get_post( $i );
					$evidences[] = [
						'id' => $i,
						'title' => $attachment->post_title,
						'url' => $attachment->guid
					];
				}

				$files_id = get_post_meta( $id, 'files', true );
				$files = [];
				foreach ( $files_id as $i ) {
					$attachment = get_post( $i );
					$files[] = [
						'id' => $i,
						'title' => $attachment->post_title,
						'url' => $attachment->guid
					];
				}

				$to_return['data'] = [
					'ncar_no' => $id,
					'add_date' => get_post_meta( $id, 'add_date', true ),
					'department' => get_post_meta( $id, 'department', true ),
					'source_of_nc' => get_post_meta( $id, 'source_of_nc', true ),
					'other_source' => get_post_meta( $id, 'other_source', true ),
					'clause_no' => get_post_meta( $id, 'clause_no', true ),
					'evidences' => $evidences,
					'reviewed_by' => get_post_meta( $id, 'reviewed_by', true ),
					'approved_by' => get_post_meta( $id, 'approved_by', true ),
					'description_of_the_noncomformity' => get_post_meta( $id, 'description_of_the_noncomformity', true ),
				];

				$to_return['form2'] = [
					'correction' => get_post_meta( $id, 'correction', true ),
					'files' => $files,
					'corrective_action_data' => get_post_meta( $id, 'corrective_action_data', true ),
				];

				$to_return['form3'] = [
					'verification' => get_post_meta( $id, 'verification', true ),
				];

			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_delete() {
			if ( !$_POST['id'] ) 
				return false;

			if ( wp_delete_post( $_POST['id'], true ) ) {
				$to_return = ['post_id' => $_POST['id']];
			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_save() {

			$data = $_POST['data'];
			$evidences = ( $_POST['evidences'] ? $_POST['evidences'] : [] );

	        $post_data = array(
	            'post_title' => 'Test',
	            'post_type' => 'ncar-ia',
	            'post_status' => 'publish'
	        );
			$post_id = wp_insert_post( $post_data );

			$to_return = [];
			if ( $post_id ) {
				foreach( $data as $field ) {
					update_post_meta( $post_id, $field['name'], $field['value'] );
				}

				update_post_meta( $post_id, 'status', 'For Improvement Action' );

				$to_return = ['post_id' => $post_id];
				update_post_meta( $post_id, 'evidences', $evidences );
			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_ia_post_type() {
			$labels = array(
				'name'                  => _x( 'Improvement Action', 'Post type general name', 'textdomain' ),
				'singular_name'         => _x( 'Improvement Action', 'Post type singular name', 'textdomain' ),
				'menu_name'             => _x( 'Improvement Action', 'Admin Menu text', 'textdomain' ),
				'name_admin_bar'        => _x( 'Improvement Action', 'Add New on Toolbar', 'textdomain' ),
				'add_new'               => __( 'Add New', 'textdomain' ),
				'add_new_item'          => __( 'Add New Improvement Action', 'textdomain' ),
				'new_item'              => __( 'New Improvement Action', 'textdomain' ),
				'edit_item'             => __( 'Edit Improvement Action', 'textdomain' ),
				'view_item'             => __( 'View Improvement Action', 'textdomain' ),
				'all_items'             => __( 'All Improvement Action', 'textdomain' ),
				'search_items'          => __( 'Search Improvement Action', 'textdomain' ),
				'parent_item_colon'     => __( 'Parent Improvement Action:', 'textdomain' ),
				'not_found'             => __( 'No Improvement Action found.', 'textdomain' ),
				'not_found_in_trash'    => __( 'No Improvement Action found in Trash.', 'textdomain' ),
				'featured_image'        => _x( 'Improvement Action Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'archives'              => _x( 'NCAR archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
				'insert_into_item'      => _x( 'Insert into Improvement Action', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this Improvement Action', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
				'filter_items_list'     => _x( 'Filter Improvement Action list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
				'items_list_navigation' => _x( 'Improvement Action list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
				'items_list'            => _x( 'Improvement Action list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'query_var'          => false,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
			);

			register_post_type( 'ncar-ia', $args );

			if ( isset( $_GET['dev'] ) ) {

		  //       $post_data = array(
		  //           'post_title' => 'Test',
		  //           'post_type' => 'ncar',
		  //           'post_status' => 'publish'
		  //       );
				// $post_id = wp_insert_post( $post_data );
				// var_dump( $post_id );

				$args = [
					'post_type' => 'ncar_ia',
					'posts_per_page' => -1
				];
				$query = new WP_Query( $args );
				foreach( $query->posts as $post ) {
					var_dump( get_post_meta( $post->ID ) );
					// wp_delete_post( $post->ID, true );
				}
				exit;
			}
		}

		function ncar_ia_resources(){
			if ( $_GET['page'] == 'ncar-ia' ) {
			    wp_enqueue_style( 'custom-ncar-ia-css', get_stylesheet_directory_uri() . '/modules/ncar-ia/css/styles.css' );
			    wp_enqueue_style( 'swal-ia-css', get_stylesheet_directory_uri() . '/modules/ncar-ia/css/sweetalert2.min.css' );
			    wp_enqueue_script( 'custom-ncar-ia-js', get_stylesheet_directory_uri() . '/modules/ncar-ia/js/scripts.js' );
			    wp_enqueue_script( 'swal-js', get_stylesheet_directory_uri() . '/modules/ncar-ia/js/sweetalert2.all.min.js' );

			    wp_enqueue_media();
			}
		}

		function ncar_ia_main_module() {

			add_menu_page(
				__( 'Improvement Action', 'my-textdomain' ),
				__( 'Improvement Action', 'my-textdomain' ),
				'create_posts',
				'ncar-ia',
				array( $this, 'ncar_ia_main_module_cb' ),
				'dashicons-info',
				999999
			);

		}

		function ncar_ia_main_module_cb() {
			require_once( 'main_module_html.php' );
			require_once( 'modals.php' );
		}
	}
	$NCAR_IA_Module = new NCAR_IA_Module();
}