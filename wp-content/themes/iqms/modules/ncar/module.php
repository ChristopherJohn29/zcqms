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

			add_action('wp_ajax_ncar_status_log', array($this, 'ncar_status_log'));
        	add_action('wp_ajax_nopriv_ncar_status_log', array($this, 'ncar_status_log'));

			// Ensure status log table exists
			add_action('admin_init', array($this, 'ensure_status_log_table'));

		// Add admin notice if table doesn't exist
		add_action('admin_notices', array($this, 'status_log_table_notice'));

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

		public function sendEmail($toemail = '', $subject = '', $message = ''){



			$sent = wp_mail($toemail, $subject, strip_tags($message), $headers);
				
			return $sent;
		}

		function get_date(){

			date_default_timezone_set('Asia/Shanghai');

			$currentDateTime = date("Y-m-d"). ' at ' .date('h:i A');

			return $currentDateTime;

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
					date_default_timezone_set('Asia/Shanghai');
					$old_status = get_post_meta($post_id, 'status', true);
					update_post_meta( $post_id, 'status', 'Closed' );
					$this->log_status_change($post_id, $old_status, 'Closed');
					update_post_meta( $post_id, 'close_date', date("Y-m-d") );
					

					if(get_option('notification_'.$owner)){
						$options = get_option('notification_'.$owner);
						$options[] = 'The '.$ncar_no_new.' you raised has already been verified and is now closed. <br><br>'.$this->get_date();
						update_option( 'notification_'.$owner,  $options);
					} else {
						add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been verified and is now closed. <br><br>'.$this->get_date()]);
					}

					$po = get_user_by('id', $owner);
					$this->sendEmail($po->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you raised has already been verified and is now closed.');
				

				} else {
					$old_status = get_post_meta($post_id, 'status', true);
					update_post_meta( $post_id, 'status', 'Reverted to For Action' );
					$this->log_status_change($post_id, $old_status, 'Reverted to For Action');

					$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

					$review_by = get_user_by('id', $review_by_id);
	
					if ( ! empty( $review_by ) ) {
						$review_by_name = $user->first_name .' '. $user->last_name;
					}

					if(get_option('notification_'.$review_by_id)){
						$options = get_option('notification_'.$review_by_id);
						$options[] = 'The '.$ncar_no_new.' you responded has already been verified, however it is found to be unsatisfactory. You are required to make another corrective action <br><br>'.$this->get_date();
						update_option( 'notification_'.$review_by_id,  $options);
					} else {
						add_option( 'notification_'.$review_by_id,  ['The '.$ncar_no_new.' you responded has already been verified, however it is found to be unsatisfactory. You are required to make another corrective action. <br><br>'.$this->get_date()]);
					}

					$this->sendEmail($review_by->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you responded has already been verified, however it is found to be unsatisfactory. You are required to make another corrective action.');
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

						$old_status = get_post_meta($post_id, 'status', true);
						update_post_meta( $post_id, 'status', 'For Verification' );
						$this->log_status_change($post_id, $old_status, 'For Verification');
						update_post_meta( $post_id, 'date_verified', date('Y-m-d'));

						if(get_option('notification_'.$owner)){
							$options = get_option('notification_'.$owner);
							$options[] = 'The '.$ncar_no_new.' you raised has already been followed up <br><br>'.$this->get_date();
							update_option( 'notification_'.$owner,  $options);
						} else {
							add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been followed up  <br><br>'.$this->get_date()]);
						}

						$owner_data = get_user_by('id', $owner);

						if ( ! empty( $owner_data ) ) {
							$owner_name = $owner_data->first_name .' '. $owner_data->last_name;
						}

						$this->sendEmail($owner_data->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you raised has already been followed up.');
				

						$approved_by_id = get_post_meta($post_id, 'approved_by', true);
						
			

						if(get_option('notification_'.$approved_by_id)){
							$options = get_option('notification_'.$approved_by_id);
							$options[] = 'The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its effectiveness.  <br><br>'.$this->get_date();
							update_option( 'notification_'.$approved_by_id,  $options);
						} else {
							add_option( 'notification_'.$approved_by_id,  ['The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its effectiveness.  <br><br>'.$this->get_date()]);
						}

						$approve_by = get_user_by('id', $approved_by_id);

						$this->sendEmail($approve_by->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its effectiveness. ');

					} else {
						$old_status = get_post_meta($post_id, 'status', true);
						update_post_meta( $post_id, 'status', 'Reverted to For Action' );
						$this->log_status_change($post_id, $old_status, 'Reverted to For Action');

						$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

						$review_by = get_user_by('id', $review_by_id);
		
						if ( ! empty( $review_by ) ) {
							$review_by_name = $user->first_name .' '. $user->last_name;
						}
	
						if(get_option('notification_'.$review_by_id)){
							$options = get_option('notification_'.$review_by_id);
							$options[] = 'The '.$ncar_no_new.' you responded has already been Followed-up, however it is found to be unsatisfactory. You are required to make another corrective action.  <br><br>'.$this->get_date();
							update_option( 'notification_'.$review_by_id,  $options);
						} else {
							add_option( 'notification_'.$review_by_id,  ['The '.$ncar_no_new.' you responded has already been Followed-up, however it is found to be unsatisfactory. You are required to make another corrective action. <br><br>'.$this->get_date()]);
						}

						$this->sendEmail($review_by->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you responded has already been Followed-up, however it is found to be unsatisfactory. You are required to make another corrective action.');

					
					}
				} else {

					$old_status = get_post_meta($post_id, 'status', true);
					update_post_meta( $post_id, 'status', 'For Follow up' );
					$this->log_status_change($post_id, $old_status, 'For Follow up');

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
						$options[] = 'The '.$ncar_no_new.' you raised has already been responded by '.$review_by_name . '<br><br>'.$this->get_date();
						update_option( 'notification_'.$owner,  $options);
					} else {
						add_option( 'notification_'.$owner,  ['The '.$ncar_no_new.' you raised has already been responded by '.$review_by_name . ' <br><br>'.$this->get_date()]);
					}

					$this->sendEmail($owner_data->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you raised has already been responded by '.$review_by_name . '');


					if(get_option('notification_'.$followup_by_id)){
						$options = get_option('notification_'.$followup_by_id);
						$options[] = 'The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its implementation. <br><br>'.$this->get_date();
						update_option( 'notification_'.$followup_by_id,  $options);
					} else {
						add_option( 'notification_'.$followup_by_id,  ['The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its implementation. <br><br>'.$this->get_date()]);
					}

					if ( ! empty( $followup_by ) ) {
						$this->sendEmail($followup_by->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' corrective action implemented by '.$owner_name.' requires you to verify its implementation.');

					}


				}


				///////////////////////////////////////

				$current_correction = get_post_meta( $post_id, 'correction', true);


				foreach ($correction as $key => $value) {
					$correction[$key]['correction_text'] = isset($correction[$key]['correction_text']) && !empty($correction[$key]['correction_text']) 
						? $correction[$key]['correction_text'] 
						: (isset($current_correction[$key]['correction_text']) ? $current_correction[$key]['correction_text'] : '');
				
					$correction[$key]['correction_date'] = isset($correction[$key]['correction_date']) && !empty($correction[$key]['correction_date']) 
						? $correction[$key]['correction_date'] 
						: (isset($current_correction[$key]['correction_date']) ? $current_correction[$key]['correction_date'] : '');
				
					$correction[$key]['correction_implemented'] = isset($correction[$key]['correction_implemented']) && !empty($correction[$key]['correction_implemented']) 
						? $correction[$key]['correction_implemented'] 
						: (isset($current_correction[$key]['correction_implemented']) ? $current_correction[$key]['correction_implemented'] : '');
				
					$correction[$key]['correction_remarks'] = isset($correction[$key]['correction_remarks']) && !empty($correction[$key]['correction_remarks']) 
						? $correction[$key]['correction_remarks'] 
						: (isset($current_correction[$key]['correction_remarks']) ? $current_correction[$key]['correction_remarks'] : '');
				}

				$current_correction_rca = get_post_meta( $post_id, 'correction_rca', true);

				foreach ($correction_rca as $key => $value) {
					$correction_rca[$key]['correction_text'] = $correction_rca[$key]['correction_text'] ? $correction_rca[$key]['correction_text'] : $current_correction_rca[$key]['correction_text'];
				}

				$current_corrective_action_data = get_post_meta( $post_id, 'corrective_action_data', true );

				foreach ($corrective_action_data as $key => $value) {
					$corrective_action_data[$key]['root_causes'] = isset($corrective_action_data[$key]['root_causes']) && !empty($corrective_action_data[$key]['root_causes'])
						? $corrective_action_data[$key]['root_causes']
						: (isset($current_corrective_action_data[$key]['root_causes']) ? $current_corrective_action_data[$key]['root_causes'] : '');

					$corrective_action_data[$key]['corrective_action'] = isset($corrective_action_data[$key]['corrective_action']) && !empty($corrective_action_data[$key]['corrective_action'])
						? $corrective_action_data[$key]['corrective_action']
						: (isset($current_corrective_action_data[$key]['corrective_action']) ? $current_corrective_action_data[$key]['corrective_action'] : '');

					$corrective_action_data[$key]['corrective_date'] = isset($corrective_action_data[$key]['corrective_date']) && !empty($corrective_action_data[$key]['corrective_date'])
						? $corrective_action_data[$key]['corrective_date']
						: (isset($current_corrective_action_data[$key]['corrective_date']) ? $current_corrective_action_data[$key]['corrective_date'] : '');

					$corrective_action_data[$key]['corrective_implemented'] = isset($corrective_action_data[$key]['corrective_implemented']) && !empty($corrective_action_data[$key]['corrective_implemented'])
						? $corrective_action_data[$key]['corrective_implemented']
						: (isset($current_corrective_action_data[$key]['corrective_implemented']) ? $current_corrective_action_data[$key]['corrective_implemented'] : '');

					$corrective_action_data[$key]['corrective_remarks'] = isset($corrective_action_data[$key]['corrective_remarks']) && !empty($corrective_action_data[$key]['corrective_remarks'])
						? $corrective_action_data[$key]['corrective_remarks']
						: (isset($current_corrective_action_data[$key]['corrective_remarks']) ? $current_corrective_action_data[$key]['corrective_remarks'] : '');

					// Handle evidence files for corrective actions
					if (isset($corrective_action_data[$key]['evidences']) && !empty($corrective_action_data[$key]['evidences'])) {
						$evidence_files = [];
						foreach ($corrective_action_data[$key]['evidences'] as $evidence_id) {
							if (!empty($evidence_id)) {
								$attachment = get_post($evidence_id);
								if ($attachment) {
									$evidence_files[] = [
										'id' => $evidence_id,
										'title' => $attachment->post_title,
										'url' => wp_get_attachment_url($evidence_id)
									];
								}
							}
						}
						$corrective_action_data[$key]['evidences'] = $evidence_files;
					} else {
						// Preserve existing evidence files if no new ones are provided
						$corrective_action_data[$key]['evidences'] = isset($current_corrective_action_data[$key]['evidences'])
							? $current_corrective_action_data[$key]['evidences']
							: [];
					}
				}
				

				update_post_meta( $post_id, 'correction', $correction );
				update_post_meta( $post_id, 'correction_rca', $correction_rca );
				if(!empty($files)){
					update_post_meta( $post_id, 'files', $files );
				}
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
				$year = date('Y'); // Get the current year
				$number_series = str_pad($post_id, 3, '0', STR_PAD_LEFT); // Pad the post_id to be 3 digits
				$ncar_no_new = "ZCMC-NCAR-{$year}-{$number_series}"; // Generate the NCAR code
			
				foreach( $data as $field ) {
					update_post_meta( $post_id, $field['name'], $field['value'] );
					if($field['name'] == 'ncar_no_new'){
						update_post_meta( $post_id, 'ncar_no_new', $ncar_no_new );
					}
				}

				update_post_meta( $post_id, 'status', 'For Action' );
				$this->log_status_change($post_id, null, 'For Action');

				if(get_option('notification_'.$this->this_user)){
					$options = get_option('notification_'.$this->this_user);
					$options[] = 'The '.$ncar_no_new.' you raised has been forwarded to the process owner for action.  <br><br>'.$this->get_date();
					update_option( 'notification_'.$this->this_user,  $options);
				} else {
					add_option( 'notification_'.$this->this_user,  ['The '.$ncar_no_new.' you raised has been forwarded to the process owner for action.  <br><br>'.$this->get_date()]);
				}

				$po = get_user_by('id', $this->this_user);
				$this->sendEmail($po->user_email, 'NCAR Notification', 'The '.$ncar_no_new.' you raised has been forwarded to the process owner for action.');
				

				$review_by_id = get_post_meta($post_id, 'reviewed_by', true);

				$review_by = get_user_by('id', $review_by_id);

				if ( ! empty( $review_by ) ) {
					$review_by_name = $user->first_name .' '. $user->last_name;
				}


				if(get_option('notification_'.$review_by_id)){
					$options = get_option('notification_'.$review_by_id);
					$options[] = 'You have an NCAR ('.$ncar_no_new.') due for response.  <br><br>'.$this->get_date();
					update_option( 'notification_'.$review_by_id,  $options);
				} else {
					add_option( 'notification_'.$review_by_id,  ['You have an NCAR ('.$ncar_no_new.') due for response.  <br><br>'.$this->get_date()]);
				}

				if ( ! empty( $review_by ) ) {
					$this->sendEmail($review_by->user_email, 'NCAR Notification', 'You have an NCAR ('.$ncar_no_new.') due for response.');
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
			if ( isset($_GET['page']) && $_GET['page'] == 'ncar' ) {
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
			// Handle table creation request
			if (isset($_GET['create_status_table']) && $_GET['create_status_table'] == '1') {
				$this->create_status_log_table();
				echo '<div class="notice notice-success is-dismissible">';
				echo '<p><strong>Success!</strong> NCAR Action Log table has been created.</p>';
				echo '</div>';
			}

			require_once( 'main_module_html.php' );
			require_once( 'modals.php' );
		}

		function ensure_status_log_table() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ncar_status_log';

			if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$this->create_status_log_table();
			}
		}

		function status_log_table_notice() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ncar_status_log';

			if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				echo '<div class="notice notice-warning is-dismissible">';
				echo '<p><strong>NCAR Action Log:</strong> The action log table needs to be created. ';
				echo '<a href="' . admin_url('admin.php?page=ncar&create_status_table=1') . '" class="button">Create Table Now</a></p>';
				echo '</div>';
			}
		}

		function create_status_log_table() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'ncar_status_log';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				post_id bigint(20) unsigned NOT NULL,
				old_status varchar(255) DEFAULT NULL,
				new_status varchar(255) NOT NULL,
				changed_by bigint(20) unsigned NOT NULL,
				timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY post_id (post_id),
				KEY timestamp (timestamp)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		function get_action_label($old_status, $new_status) {
			// Handle initial creation
			if ($old_status === null && $new_status === 'For Action') {
				return 'Created';
			}

			// Handle status transitions - "Actioned" covers both initial action and re-action after revert
			if (($old_status === 'For Action' || $old_status === 'Reverted to For Action') && $new_status === 'For Follow up') {
				return 'Actioned';
			}

			if ($old_status === 'For Follow up' && $new_status === 'For Verification') {
				return 'Followed up';
			}

			if ($old_status === 'For Verification' && $new_status === 'Closed') {
				return 'Verified and Closed';
			}

			if ($new_status === 'Reverted to For Action') {
				return 'Reverted';
			}

			// Default fallback for any other transitions
			return $new_status;
		}

		function log_status_change($post_id, $old_status, $new_status) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'ncar_status_log';
			$action_label = $this->get_action_label($old_status, $new_status);

			$wpdb->insert(
				$table_name,
				array(
					'post_id' => $post_id,
					'old_status' => $old_status,
					'new_status' => $action_label,
					'changed_by' => get_current_user_id(),
					'timestamp' => current_time('mysql')
				),
				array('%d', '%s', '%s', '%d', '%s')
			);
		}

		function ncar_status_log() {
			if (!$_POST['id']) {
				echo json_encode(['error' => 'No ID provided']);
				exit;
			}

			global $wpdb;
			$table_name = $wpdb->prefix . 'ncar_status_log';
			$post_id = intval($_POST['id']);

			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT old_status, new_status, changed_by, timestamp
				FROM $table_name
				WHERE post_id = %d
				ORDER BY timestamp ASC",
				$post_id
			));

			$log_entries = array();
			foreach ($results as $row) {
				$user = get_user_by('id', $row->changed_by);
				$user_name = $user ? $user->display_name : 'Unknown User';

				$log_entries[] = array(
					'status_label' => $row->new_status, // This now contains the action label
					'timestamp' => date('Y-m-d H:i:s', strtotime($row->timestamp)),
					'changed_by' => $user_name
				);
			}

			echo json_encode($log_entries);
			exit;
		}
	}
	$NCAR_Module = new NCAR_Module();
}