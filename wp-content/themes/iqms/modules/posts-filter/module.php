<?php


function notice__success() {

	$options = get_option('notification_'.get_current_user_id());
	
	$options = array_reverse($options);

	foreach ($options as $key => $value) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( $value, 'sample-text-domain' ); ?></p>
		</div>
		<?php
	}

	?>

	<div class="notice notice-success is-dismissible">
        <p><?php _e( 'testinggggggggg!', 'sample-text-domain' ); ?></p>
    </div>
	
	<?php

}

add_action( 'admin_notices', 'notice__success' );

add_action('init', function(){

	if($_GET['test']){
		$options = get_option('notification_'.get_current_user_id());
		$options = array_reverse($options);

		var_dump($options);
		exit;
	}


});



add_action( 'edit_form_top', 'filter_post_fields' );

function filter_post_fields() {

		


	if( get_post_type() == 'printing'){

		$this_user = wp_get_current_user();

		$user_id = $this_user->ID;

		$approve_by = false;

		$approve_by_data = get_field( 'initial_approver' );

		$approve_by_data = ( is_array( $approve_by_data ) ? $approve_by_data : [] );

		if ( $user_id == $approve_by_data['ID'] ) {
			$approve_by = true;
		}

		if ( $approve_by === false ) {

			echo '<style>.acf-field[data-name="initial_approval_status"] {display: none;}</style>';

		}

		$initial_approval_status = get_field( 'initial_approval_status',  $post_id );
		$final_approval_status = get_field( 'final_approval_status',  $post_id );

		$approve_by_final = false;

		$approve_by_final_data = get_field( 'final_approver' );

		$approve_by_final_data = ( is_array( $approve_by_final_data ) ? $approve_by_final_data : [] );

		if ( $user_id == $approve_by_final_data['ID'] && $initial_approval_status == 'yes') {
			$approve_by_final = true;
		}

		if ( $approve_by_final === false ) {

			echo '<style>.acf-field[data-name="final_approval_status"] {display: none;}</style>';

		}

		if($initial_approval_status == 'yes' && $final_approval_status == 'yes'){
			echo '<style>#publishing-action {display: none;}</style>';
		}

		$cur_user = wp_get_current_user();


		$roles = $cur_user->roles;

		$user_id = $cur_user->ID;
		$requestor = get_post_field('post_author',$post_id);
		$post_status = get_post_status($post_id);
		// $requestor = get_user_by('ID', $requestor);


		if($user_id == $requestor && $post_status != 'auto-draft'){
			echo '<style>#publishing-action {display: none;}</style>';
		}

		if(($roles[0] === 'administrator' || $roles[0] === 'dco') ){
			echo '<style>#publishing-action {display: block;}</style>';
		}

		if(($roles[0] !== 'administrator' && $roles[0] !== 'dco') ){
			echo '<style>[data-name="initial_approver"] {display: none;}</style>';
			echo '<style>[data-name="final_approver"] {display: none;}</style>';
		}


		if(($roles[0] === 'administrator' || $roles[0] === 'dco') ){
			echo '<style>[data-name="initial_approver"] {display: block;}</style>';
			echo '<style>[data-name="final_approver"] {display: block;}</style>';
		}

		
		echo '<style>#pageparentdiv {display: none;}</style>';
		
		
		$this_post_id = get_the_id();

		// var_dump();
		$document_title = get_field( 'document_title' );

		
		echo '<script>

		(function($){
			$(window).on(\'load\', function(){
				$(\'[data-name="document_title"]\').append(\'<a href="'.$document_title->guid.'" style="margin:5px;" target="_blank">View Document</a>\');
			});
		})(jQuery);

		</script>';
		
	}



	if ( get_post_type() == 'dcm' ) {



		$this_user = wp_get_current_user();

		$this_post_id = get_the_id();



		$roles = [

			'mcc',

			'iso-chairperson',

			'division-chief',

			'dep-sec-head',

			'process-owners',

		];

		$hide = false;

		$this_user_roles = $this_user->roles;

		$user_id = $this_user->ID;

		// var_dump( get_post( get_the_id() )->post_author );

		// var_dump( $user_id );exit;

		// if ( $user_id != get_post( get_the_id() )->post_author ) {

		// 	/*user cant edit the file*/

		// 	echo '<script>



		// 	(function($){

		// 		$(window).on(\'load\', function(){

		// 			$(\'[data-name="document_type"] select\').prop(\'disabled\', true);

		// 			$(\'[data-name="file_url"] input, [data-name="upload_document"] input\').prop(\'disabled\', true);

		// 			// console.log(\'test\');

		// 		});

		// 	})(jQuery);



		// 	</script>';



		// 	echo '

		// 	<style type="text/css">

		// 		[data-name="upload_document"] .acf-file-uploader.has-value {

		// 		    pointer-events: none;

		// 		}

		// 	</style>';

		// }



		foreach( $this_user_roles as $r ) {

			if ( in_array($r, $roles) ) {

				$hide = true;

			}

		}



		/*approve fields*/

		if ( $hide ) {

			echo '<style>.acf-field[data-name="approved_by"],.acf-field[data-name="review_by"] {display: none;}</style>';

		}



		/*chechk if post already reviewed and approved by DCO*/

		$dco_review_status = get_field( 'dco_review_status' );



		$this_user_can_approve = false;

		$this_user_can_review = false;

		$this_user_assigned_dco = false;



		$post_approve = get_field( 'approved_by' );

		$post_approve = ( is_array( $post_approve ) ? $post_approve : [] );

		foreach( $post_approve as $p ) {

			if ( $user_id == $p['ID'] ) {

				$this_user_can_approve = true;

			}

		}



		$post_review = get_field( 'review_by' );

		$post_review = ( is_array( $post_review ) ? $post_review : [] );

		foreach( $post_review as $p ) {

			if ( $user_id == $p['ID'] ) {

				$this_user_can_review = true;

			}

		}



		$assigned_dco = get_field( 'assigned_dco' );

		$assigned_dco = ( is_array( $assigned_dco ) ? $assigned_dco : [] );

		foreach( $assigned_dco as $p ) {

			if ( $user_id == $p['ID'] ) {

				$this_user_assigned_dco = true;

			}

		}

		/*hide the publish button*/

		$hide_publish_button = true;

		$hide_sidebar = false;

		if ( $user_id == get_post( get_the_id() )->post_author ) {

			$hide_publish_button = false;

		} else {

			if ( $this_user_assigned_dco || $this_user_can_review || $this_user_can_approve ) {

				$hide_publish_button = false;

			}

			if ( $this_user_can_review || $this_user_can_approve ) {

				$hide_sidebar = true;

			}

		}

		// if ( isset( $_GET['testdev'] ) ) {

		// 	var_dump( $dco_review_status );exit;

		// }

		if ( $user_id == get_post( get_the_id() )->post_author && ( $dco_review_status === '' || $dco_review_status === 'yes' ) ) {

			$hide_publish_button = true;

		}

		
		// to be removed
		if($user_id == get_post( get_the_id() )->post_author) {
			$hide_publish_button = false;
		}

		if ( $hide_publish_button ) {

			echo '<style>#submitdiv {display: none;}</style>';

		}

		if ( $hide_sidebar ) {

			echo '<style>#servicesdiv, #document_typediv, #documents_labeldiv {display: none;}</style>';

		}

		/*end*/



		if ( $this_user_can_approve === false || $dco_review_status !== 'yes' ) {

			echo '<style>.acf-field[data-name="approval_status"], .acf-field[data-name="approval_denied_reason"] {display: none;}</style>';

			

		}

		if ( $this_user_can_review === false || $dco_review_status !== 'yes' ) {

			echo '<style>.acf-field[data-name="review_status"], .acf-field[data-name="review_denied_reason"] {display: none;}</style>';

		}



		if ( $this_user_assigned_dco === false ) {

			echo '<style>.acf-field[data-name="dco_review_status"], .acf-field[data-name="dco_review_denied_reason"] {display: none;}</style>';

		}



		if ( $dco_review_status !== 'yes' && ($dco_review_status !== null && $dco_review_status !== '' ) ) {

			echo '<style>[data-name="dco_review_denied_reason"] {display: block !important;}</style>';

		}



		$approved_by = get_post_meta( $this_post_id, '_user_approved', true );

		if ( $approved_by ) {



			$approval_status = get_field( 'approval_status' );

			$user = get_user_by('ID', $approved_by);

			$name = $user->data->display_name;

			$role = ( ($user->roles[0] ? $user->roles[0] : '') );



			$text = ( $approval_status == 'yes' ? ' — Accepted by: ' : ' — Denied by: ' ) .$name . ' (' . $role . ')' ;

			if($user_id  != $approved_by) {
				echo '<script>
				(function($){

					$(window).on(\'load\', function(){

						$(\'div[data-name="approval_status"]\').css( \'display\', \'block\' );
						$(\'div[data-name="approval_denied_reason"]\').css( \'display\', \'block\' );
						$(\'div[data-name="approval_status"] input\').attr( \'disabled\', \'true\' );
						$(\'div[data-name="approval_denied_reason"] textarea\').attr( \'disabled\', \'true\' );

					});

				})(jQuery);
				</script>';
			}

			echo '<script>



			(function($){

				$(window).on(\'load\', function(){

					$(\'div[data-name="approval_status"] .acf-label label\').append( \''.$text.'\' );

					

				});

			})(jQuery);



			</script>';

			/*$(\'div[data-name="approval_status"] input[type="radio"]\').click( function(){ return false; } ).addClass(\'disabled-radio\');*/

		}



		$assigned_dco = get_post_meta( $this_post_id, 'assigned_dco', true );
		$prepared_by = get_post_meta( $this_post_id, 'users', true );

		$reviewed_by = get_post_meta( $this_post_id, '_user_reviewed', true );
		$approved_by = get_post_meta( $this_post_id, '_user_approved', true );
		$dco_reviewed_by = get_post_meta( $this_post_id, '_user_dco_reviewed', true );
		

		if($user_id  != get_post( get_the_id() )->post_author) {
			echo '<script>
			(function($){

				$(window).on(\'load\', function(){

					$(\'div[data-name="review_status"] input[value="review"]\').attr( \'disabled\', \'true\' );
					$(\'div[data-name="dco_review_status"] input[value="review"]\').attr( \'disabled\', \'true\' );
					$(\'div[data-name="approval_status"] input[value="review"]\').attr( \'disabled\', \'true\' );
					

				});

			})(jQuery);
			</script>';
		} 



		if($user_id.""  == get_post( get_the_id() )->post_author || in_array($user_id."", $assigned_dco) || in_array($user_id."", $prepared_by) ) {
			echo '<script>
			(function($){

				$(window).on(\'load\', function(){
					setTimeout(function(){
						$(\'div[data-name="review_status"] input[value="review"]\').removeAttr( \'disabled\');
						$(\'div[data-name="dco_review_status"] input[value="review"]\').removeAttr( \'disabled\');
						$(\'div[data-name="approval_status"] input[value="review"]\').removeAttr( \'disabled\');
					}, 1000);
					
				});

			})(jQuery);
			</script>';
		}


		if ( $reviewed_by ) {



			$review_status = get_field( 'review_status' );

			$user = get_user_by('ID', $reviewed_by);

			$name = $user->data->display_name;

			$role = ( ($user->roles[0] ? $user->roles[0] : '') );

			if($user_id  != $reviewed_by) {
				echo '<script>
				(function($){

					$(window).on(\'load\', function(){

						$(\'div[data-name="review_status"]\').css( \'display\', \'block\' );
						$(\'div[data-name="review_denied_reason"]\').css( \'display\', \'block\' );
						$(\'div[data-name="review_status"] input\').attr( \'disabled\', \'true\' );
						$(\'div[data-name="review_denied_reason"] textarea\').attr( \'disabled\', \'true\' );

					});

				})(jQuery);
				</script>';
			}

			$text = ( $review_status == 'yes' ? ' — Accepted by: ' : ' — Denied by: ' ) .$name . ' (' . $role . ')' ;

			echo '<script>



			(function($){

				$(window).on(\'load\', function(){

					$(\'div[data-name="review_status"] .acf-label label\').append( \''.$text.'\' );

					

				});

			})(jQuery);



			</script>';

			/*$(\'div[data-name="review_status"] input[type="radio"]\').click( function(){ return false; } ).addClass(\'disabled-radio\');*/

		}


		//////////////////////////////////////////////////


		if ( $dco_reviewed_by ) {



			$dco_review_status = get_field( 'dco_review_status' );

			$user = get_user_by('ID', $dco_reviewed_by);

			$name = $user->data->display_name;

			$role = ( ($user->roles[0] ? $user->roles[0] : '') );

			if($user_id  != $dco_reviewed_by) {
				echo '<script>
				(function($){

					$(window).on(\'load\', function(){

						$(\'div[data-name="dco_review_status"]\').css( \'display\', \'block\' );
						$(\'div[data-name="dco_review_denied_reason"]\').css( \'display\', \'block\' );
						$(\'div[data-name="dco_review_status"] input\').attr( \'disabled\', \'true\' );
						$(\'div[data-name="dco_review_denied_reason"] textarea\').attr( \'disabled\', \'true\' );

					});

				})(jQuery);
				</script>';
			}

			$text = ( $dco_review_status == 'yes' ? ' — Accepted by: ' : ' — Denied by: ' ) .$name . ' (' . $role . ')' ;

			echo '<script>



			(function($){

				$(window).on(\'load\', function(){

					$(\'div[data-name="dco_review_status"] .acf-label label\').append( \''.$text.'\' );

					

				});

			})(jQuery);



			</script>';

			/*$(\'div[data-name="review_status"] input[type="radio"]\').click( function(){ return false; } ).addClass(\'disabled-radio\');*/

		}

		if($review_status == 'no' || $dco_review_status == 'no' || $approval_status == 'no'){
			if(in_array($user_id."", $prepared_by)){
				echo '<style>#submitdiv {display: block !important;}</style>';
			}
		}



	}

}