<?php
if ( !class_exists('NCAR_Module') ) {

	class NCAR_Module {

		var $this_user;

		function __construct() {
			$this->this_user = get_current_user_id();
			add_action( 'admin_menu', array( $this, 'ncar_main_module' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ncar_resources' ) );
			/*post type*/

			add_action( 'init', array( $this, 'ncar_post_type' ) );

			/*ncar ajax*/
			add_action('wp_ajax_ncar_save', array($this, 'ncar_save'));
        	add_action('wp_ajax_nopriv_ncar_save', array($this, 'ncar_save'));

			add_action('wp_ajax_ncar_delete', array($this, 'ncar_delete'));
        	add_action('wp_ajax_nopriv_ncar_delete', array($this, 'ncar_delete'));

			add_action('wp_ajax_ncar_edit', array($this, 'ncar_edit'));
        	add_action('wp_ajax_nopriv_ncar_edit', array($this, 'ncar_edit'));

			add_action('wp_ajax_ncar_edit_save', array($this, 'ncar_edit_save'));
        	add_action('wp_ajax_nopriv_ncar_edit_save', array($this, 'ncar_edit_save'));

			add_action('wp_ajax_ncar_form2_save', array($this, 'ncar_form2_save'));
        	add_action('wp_ajax_nopriv_ncar_form2_save', array($this, 'ncar_form2_save'));

			add_action('wp_ajax_ncar_form3_save', array($this, 'ncar_form3_save'));
        	add_action('wp_ajax_nopriv_ncar_form3_save', array($this, 'ncar_form3_save'));

			add_action('wp_ajax_ncar_load_remarks', array($this, 'ncar_load_remarks'));
        	add_action('wp_ajax_nopriv_ncar_load_remarks', array($this, 'ncar_load_remarks'));

			add_action('wp_ajax_ncar_save_remarks', array($this, 'ncar_save_remarks'));
        	add_action('wp_ajax_nopriv_ncar_save_remarks', array($this, 'ncar_save_remarks'));
		}

		function ncar_save_remarks() {
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

		function ncar_load_remarks() {
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

		function ncar_form3_save() {
			$data = $_POST['data'];

			$verification = $data['verification'];
			$final_decision = $data['final_decision'];

			$post_id = $data['ncar_no'];
			$to_return = [];
			if ( $post_id ) {

				$owner = get_post_field('post_author',$post_id);
				$ncar_no_new = get_post_meta($post_id, 'ncar_no_new', true);

				if($final_decision == 'satisfactory'){
					update_post_meta( $post_id, 'status', 'Closed' );

					if(get_option('notification_'.$owner)){
						$options = get_option('notification_'.$owner);
						$options[] = 'The '.$ncar_no_new.' you raised has already been verified and is now closed.';
						update_option( 'notification_'.$owner,  $options);
					} else {
						add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been verified and is now closed.']);
					}

				} else {
					update_post_meta( $post_id, 'status', 'Reverted to For Action' );

					$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

					$review_by = get_user_by('id', $review_by_id);
	
					if ( ! empty( $review_by ) ) {
						$review_by_name = $user->first_name .' '. $user->last_name;
					}

					if(get_option('notification_'.$review_by_id)){
						$options = get_option('notification_'.$review_by_id);
						$options[] = 'The '.$ncar_no_new.' you responded has already been verified, however it is found to be unsatisfactory. You are required to make another corrective action.';
						update_option( 'notification_'.$review_by_id,  $options);
					} else {
						add_option( 'notification_'.$review_by_id,  ['The '.$ncar_no_new.' you responded has already been verified, however it is found to be unsatisfactory. You are required to make another corrective action.
						']);
					}
				}

				update_post_meta( $post_id, 'verification', $verification );
				update_post_meta( $post_id, 'final_decision', $final_decision );
				$to_return = ['post_id' => $post_id];
			} else {
				$to_return = ['error' => true];
			}
			echo json_encode( $to_return );
			exit;
		}

		function ncar_form2_save() {
			$data = $_POST['data'];

			$correction = $data['correction'];
			$correction_rca = $data['correction_rca'];
			$files = ( $data['files'] ? $data['files'] : [] );
			$corrective_action_data = $data['corrective_action_data'];
			$satisfactory = ( isset($data['satisfactory']) ? $data['satisfactory']  : '' );

			$post_id = $data['ncar_no'];
			

			$to_return = [];
			if ( $post_id ) {

				if($satisfactory !== ''){
					
					$owner = get_post_field('post_author',$post_id);
					$ncar_no_new = get_post_meta($post_id, 'ncar_no_new', true);
					$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

					$review_by = get_user_by('id', $review_by_id);

					if($satisfactory == 1){
						update_post_meta( $post_id, 'status', 'For Verification' );

						if(get_option('notification_'.$owner)){
							$options = get_option('notification_'.$owner);
							$options[] = 'The '.$ncar_no_new.' you raised has already been followed up';
							update_option( 'notification_'.$owner,  $options);
						} else {
							add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been followed up']);
						}

						$owner_data = get_user_by('id', $owner);

						if ( ! empty( $owner_data ) ) {
							$owner_name = $owner_data->first_name .' '. $owner_data->last_name;
						}

						$approved_by_id = get_post_meta($post_id, 'approved_by', true);
						
						var_dump($approved_by_id);
						exit;

						if(get_option('notification_'.$approved_by_id)){
							$options = get_option('notification_'.$approved_by_id);
							$options[] = 'A corrective action implemented by '.$owner_name.' requires you to verify its effectiveness.';
							update_option( 'notification_'.$approved_by_id,  $options);
						} else {
							add_option( 'notification_'.$approved_by_id,  ['A corrective action implemented by '.$owner_name.' requires you to verify its effectiveness.']);
						}

					} else {
						update_post_meta( $post_id, 'status', 'Reverted to For Action' );

					
					}
				} else {
					update_post_meta( $post_id, 'status', 'For Follow up' );

					$owner = get_post_field('post_author',$post_id);
					$ncar_no_new = get_post_meta($post_id, 'ncar_no_new', true);
					$review_by_id = get_post_meta($post_id, 'reviewed_by', true);
					$followup_by_id = get_post_meta($post_id, 'followup_by', true);

					$review_by = get_user_by('id', $review_by_id);
					$followup_by = get_user_by('id', $followup_by_id);

					if ( ! empty( $review_by ) ) {
						$review_by_name = $user->first_name .' '. $user->last_name;
					}

					if ( ! empty( $followup_by ) ) {
						$followup_by_name = $followup_by->first_name .' '. $followup_by->last_name;
					}

					$owner_data = get_user_by('id', $owner);

					if ( ! empty( $owner_data ) ) {
						$owner_name = $owner_data->first_name .' '. $owner_data->last_name;
					}


					if(get_option('notification_'.$owner)){
						$options = get_option('notification_'.$owner);
						$options[] = 'The '.$ncar_no_new.' you raised has already been responded by '.$review_by_name;
						update_option( 'notification_'.$owner,  $options);
					} else {
						add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been responded by '.$review_by_name]);
					}

					if(get_option('notification_'.$followup_by_id)){
						$options = get_option('notification_'.$followup_by_id);
						$options[] = 'A corrective action implemented by '.$owner_name.' requires you to verify its implementation.';
						update_option( 'notification_'.$followup_by_id,  $options);
					} else {
						add_option( 'notification_'.$followup_by_id,  ['A corrective action implemented by '.$owner_name.' requires you to verify its implementation.']);
					}


				}

				$current_correction = get_post_meta( $post_id, 'correction', true);


				foreach ($correction as $key => $value) {
					$correction[$key]['correction_text'] = $correction[$key]['correction_text'] ? $correction[$key]['correction_text'] : $current_correction[$key]['correction_text'];
					$correction[$key]['correction_date'] = $correction[$key]['correction_date'] ? $correction[$key]['correction_date'] : $current_correction[$key]['correction_date'];
					$correction[$key]['correction_implemented'] = $correction[$key]['correction_implemented'] ? $correction[$key]['correction_implemented'] : $current_correction[$key]['correction_implemented'];
					$correction[$key]['correction_remarks'] = $correction[$key]['correction_remarks'] ? $correction[$key]['correction_remarks'] : $current_correction[$key]['correction_remarks'];
				}

				$current_correction_rca = get_post_meta( $post_id, 'correction_rca', true);


				foreach ($correction_rca as $key => $value) {
					$correction_rca[$key]['correction_text'] = $correction_rca[$key]['correction_text'] ? $correction_rca[$key]['correction_text'] : $current_correction_rca[$key]['correction_text'];
				}

				$current_corrective_action_data = get_post_meta( $post_id, 'corrective_action_data', true );

				foreach ($corrective_action_data as $key => $value) {
					$corrective_action_data[$key]['root_causes'] = $corrective_action_data[$key]['root_causes'] ? $corrective_action_data[$key]['root_causes'] : $current_corrective_action_data[$key]['root_causes'];
					$corrective_action_data[$key]['corrective_action'] = $corrective_action_data[$key]['corrective_action'] ? $corrective_action_data[$key]['corrective_action'] : $current_corrective_action_data[$key]['corrective_action'];
					$corrective_action_data[$key]['corrective_date'] = $corrective_action_data[$key]['corrective_date'] ? $corrective_action_data[$key]['corrective_date'] : $current_corrective_action_data[$key]['corrective_date'];
					$corrective_action_data[$key]['corrective_implemented'] = $corrective_action_data[$key]['corrective_implemented'] ? $corrective_action_data[$key]['corrective_implemented'] : $current_corrective_action_data[$key]['corrective_implemented'];
					$corrective_action_data[$key]['corrective_remarks'] = $corrective_action_data[$key]['corrective_remarks'] ? $corrective_action_data[$key]['corrective_remarks'] : $current_corrective_action_data[$key]['corrective_remarks'];
				}

				update_post_meta( $post_id, 'correction', $correction );
				update_post_meta( $post_id, 'correction_rca', $correction_rca );
				update_post_meta( $post_id, 'files', $files );
				update_post_meta( $post_id, 'corrective_action_data', $corrective_action_data );
				

				$to_return = ['post_id' => $post_id];
			} else {
				$to_return = ['error' => true];
			}
			echo json_encode( $to_return );
			exit;
		}

		function ncar_edit_save() {

			$data = $_POST['data'];
			$evidences = ( $_POST['evidences'] ? $_POST['evidences'] : [] );

			$post_id = $_POST['ncar_no'];

			$to_return = [];
			if ( $post_id ) {
				foreach( $data as $field ) {
					update_post_meta( $post_id, $field['name'], $field['value'] );
				}

				$to_return = ['post_id' => $post_id];
				update_post_meta( $post_id, 'evidences', $evidences );
			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_edit() {
			if ( !$_POST['id'] ) 
				return false;

			$ncar = get_post( $_POST['id'] );
			$to_return = [];

			if ( $ncar ) {

				$id = $ncar->ID;
				$to_return['cant_edit'] = ( $this->this_user == $ncar->post_author ? false : true );
				$to_return['cant_review'] = ( $this->this_user == get_post_meta( $ncar->ID, 'reviewed_by', true ) ? false : true );
				$to_return['cant_followup'] = ( $this->this_user == get_post_meta( $ncar->ID, 'followup_by', true ) ? false : true );
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
					'ncar_no_new' => get_post_meta( $id, 'ncar_no_new', true ),
					'add_date' => get_post_meta( $id, 'add_date', true ),
					'department' => get_post_meta( $id, 'department', true ),
					'source_of_nc' => get_post_meta( $id, 'source_of_nc', true ),
					'clause_no' => get_post_meta( $id, 'clause_no', true ),
					'evidences' => $evidences,
					'reviewed_by' => get_post_meta( $id, 'reviewed_by', true ),
					'followup_by' => get_post_meta( $id, 'followup_by', true ),
					'approved_by' => get_post_meta( $id, 'approved_by', true ),
					'description_of_the_noncomformity' => get_post_meta( $id, 'description_of_the_noncomformity', true ),
				];

				$to_return['form2'] = [
					'correction' => get_post_meta( $id, 'correction', true ),
					'correction_rca' => get_post_meta( $id, 'correction_rca', true ),
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

		function ncar_delete() {
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

		function ncar_save() {

			$data = $_POST['data'];
			$evidences = ( $_POST['evidences'] ? $_POST['evidences'] : [] );

	        $post_data = array(
	            'post_title' => 'Test',
	            'post_type' => 'ncar',
	            'post_status' => 'publish'
	        );
			$post_id = wp_insert_post( $post_data );

			$to_return = [];
			if ( $post_id ) {
				foreach( $data as $field ) {
					update_post_meta( $post_id, $field['name'], $field['value'] );
					if($field['name'] == 'ncar_no_new'){
						$ncar_no_new = $field['value'];
					}
				}

				update_post_meta( $post_id, 'status', 'For Action' );

				if(get_option('notification_'.$this->this_user)){
					$options = get_option('notification_'.$this->this_user);
					$options[] = 'The '.$ncar_no_new.' you raised has been forwarded to the process owner for action.';
					update_option( 'notification_'.$this->this_user,  $options);
				} else {
					add_option( 'notification_'.$this->this_user,  ['The '.$ncar_no_new.' you raised has been forwarded to the process owner for action.']);
				}
				

				$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

				$review_by = get_user_by('id', $review_by_id);

				if ( ! empty( $review_by ) ) {
					$review_by_name = $user->first_name .' '. $user->last_name;
				}


				if(get_option('notification_'.$review_by_id)){
					$options = get_option('notification_'.$review_by_id);
					$options[] = 'You have an NCAR due for response.';
					update_option( 'notification_'.$review_by_id,  $options);
				} else {
					add_option( 'notification_'.$review_by_id,  ['You have an NCAR due for response.']);
				}
				

				$to_return = ['post_id' => $post_id];
				update_post_meta( $post_id, 'evidences', $evidences );
			} else {
				$to_return = ['error' => true];
			}

			echo json_encode( $to_return );
			exit;
		}

		function ncar_post_type() {
			$labels = array(
				'name'                  => _x( 'NCAR', 'Post type general name', 'textdomain' ),
				'singular_name'         => _x( 'NCAR', 'Post type singular name', 'textdomain' ),
				'menu_name'             => _x( 'NCAR', 'Admin Menu text', 'textdomain' ),
				'name_admin_bar'        => _x( 'NCAR', 'Add New on Toolbar', 'textdomain' ),
				'add_new'               => __( 'Add New', 'textdomain' ),
				'add_new_item'          => __( 'Add New NCAR', 'textdomain' ),
				'new_item'              => __( 'New NCAR', 'textdomain' ),
				'edit_item'             => __( 'Edit NCAR', 'textdomain' ),
				'view_item'             => __( 'View NCAR', 'textdomain' ),
				'all_items'             => __( 'All NCAR', 'textdomain' ),
				'search_items'          => __( 'Search NCAR', 'textdomain' ),
				'parent_item_colon'     => __( 'Parent NCAR:', 'textdomain' ),
				'not_found'             => __( 'No NCAR found.', 'textdomain' ),
				'not_found_in_trash'    => __( 'No NCAR found in Trash.', 'textdomain' ),
				'featured_image'        => _x( 'NCAR Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'archives'              => _x( 'NCAR archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
				'insert_into_item'      => _x( 'Insert into NCAR', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this NCAR', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
				'filter_items_list'     => _x( 'Filter NCAR list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
				'items_list_navigation' => _x( 'NCAR list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
				'items_list'            => _x( 'NCAR list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
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

			register_post_type( 'ncar', $args );

			if ( isset( $_GET['dev'] ) ) {

		  //       $post_data = array(
		  //           'post_title' => 'Test',
		  //           'post_type' => 'ncar',
		  //           'post_status' => 'publish'
		  //       );
				// $post_id = wp_insert_post( $post_data );
				// var_dump( $post_id );

				$args = [
					'post_type' => 'ncar',
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

		function ncar_resources(){
			if ( $_GET['page'] == 'ncar' ) {
			    wp_enqueue_style( 'custom-ncar-css', get_stylesheet_directory_uri() . '/modules/ncar/css/styles.css' );
			    wp_enqueue_style( 'swal-css', get_stylesheet_directory_uri() . '/modules/ncar/css/sweetalert2.min.css' );
			    wp_enqueue_script( 'custom-ncar-js', get_stylesheet_directory_uri() . '/modules/ncar/js/scripts.js' );
			    wp_enqueue_script( 'swal-js', get_stylesheet_directory_uri() . '/modules/ncar/js/sweetalert2.all.min.js' );

			    wp_enqueue_media();
			}
		}

		function ncar_main_module() {

			add_menu_page(
				__( 'NCAR', 'my-textdomain' ),
				__( 'NCAR', 'my-textdomain' ),
				'create_posts',
				'ncar',
				array( $this, 'ncar_main_module_cb' ),
				'dashicons-info',
				999999
			);

		}

		function ncar_main_module_cb() {
			require_once( 'main_module_html.php' );
			require_once( 'modals.php' );
		}
	}
	$NCAR_Module = new NCAR_Module();
}