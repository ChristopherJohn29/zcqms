<?php

add_action('pre_get_posts', 'filter_posts_list');

function filter_posts_list($query)
{

    //$pagenow holds the name of the current page being viewed
     global $pagenow, $typenow;  

     	$user = wp_get_current_user();
		$cur_id = $user->ID;
		$roles = $user->roles;
		

        $allowed_roles = array('author');
        //Shouldn't happen for the admin, but for any role with the edit_posts capability and only on the posts list page, that is edit.php
        if('edit.php' == $pagenow &&  $typenow == 'dcm' && $query->query['fields'] == 'id=>parent')
        { 

		
        //global $query's set() method for setting the author as the current user's id
			
			if($roles[0] == 'administrator'){
				return;
			}
			
			$post_ids = array();

			$args = array(
				'post_type' => 'dcm',
				'posts_per_page' => -1
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) : $the_query->the_post();

				$assigned_dco = [];
				$assigned_dco_raw =  get_field('assigned_dco', get_the_ID());

				if(is_array($assigned_dco_raw)){
					foreach ($assigned_dco_raw as $key => $value) {
						$assigned_dco[] = $value['ID'];
					}
				}

				$approved_by = [];
				$approved_by_raw =  get_field('approved_by', get_the_ID());
				if(is_array($approved_by_raw)){
					foreach ($approved_by_raw as $key => $value) {
						$approved_by[] = $value['ID'];
					}
				}
				
				$review_by = [];
				$review_by_raw =  get_field('review_by', get_the_ID());
				if(is_array($review_by_raw)){
					foreach ($review_by_raw as $key => $value) {
						$review_by[] = $value['ID'];
					}
				}

				$users = [];
				$users_raw =  get_field('users', get_the_ID());
				if(is_array($users_raw)){
					foreach ($users_raw as $key => $value) {
						$users[] = $value['ID'];
					}
				}

				$author_id = get_post_field('post_author', get_the_ID());

				if (
					(
						in_array($cur_id, $assigned_dco) ||
						in_array($cur_id, $approved_by) ||
						in_array($cur_id, $review_by) ||
						in_array($cur_id, $users) ||
						in_array('dco', $roles) ||
						$cur_id == $author_id // Include posts where the current user is the author
					) &&
					(!in_array('dco', $roles) || get_post_status(get_the_ID()) != 'draft') // Exclude drafts for 'dco' role
				) {
					$post_ids[] = get_the_ID();
				}

				
			endwhile; 
			wp_reset_postdata();
			endif;


			$query->set( 'post__in', empty( $post_ids ) ? [ 0 ] : $post_ids );

        }

		if('edit.php' == $pagenow &&  $typenow == 'qms-documents' && $query->query['fields'] == 'id=>parent')
        { 

		
        //global $query's set() method for setting the author as the current user's id
			
			if($roles[0] == 'dco' || $roles[0] == 'administrator'){
				return;
			}
			
			$post_ids = array();

			$args = array(
				'post_type' => 'qms-documents',
				'posts_per_page' => -1
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) : $the_query->the_post();

				$approved_by = [];
				$approved_by[] = get_post_meta( get_the_ID(), '_user_approved', true );
				
				$review_by = [];
				$review_by[] = get_post_meta( get_the_ID(), '_user_reviewed', true );

				$author = [];
				$author_id =  get_post_field('post_author',get_the_ID());
				$author[] = $author_id;
				
				if
				(
					in_array($cur_id, $approved_by) || 
					in_array($cur_id, $review_by) || 
					in_array($cur_id, $author)
					
				)
				{
					$post_ids[] = get_the_ID();
				}

			endwhile; 
			wp_reset_postdata();
			endif;


			$query->set( 'post__in', empty( $post_ids ) ? [ 0 ] : $post_ids );

        }

		

		if('edit.php' == $pagenow &&  $typenow == 'printing' && $query->query['fields'] == 'id=>parent')
        { 

        //global $query's set() method for setting the author as the current user's id
			
			if($roles[0] == 'dco' || $roles[0] == 'administrator'){
				return;
			}
			
			$post_ids = array();

			$args = array(
				'post_type' => 'printing',
				'posts_per_page' => -1
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) : $the_query->the_post();

				$requestor = [];
				$requestor_id =  get_post_field('post_author',get_the_ID());
				$requestor[] = $requestor_id;
				$approve_by = [];
				$approve_by_raw =  get_field('initial_approver', get_the_ID());
				$approve_by[] = $approve_by_raw['ID'];
				$approve_by_final = [];
				$approve_by_final_raw =  get_field('final_approver', get_the_ID());
				$approve_by_final[] = $approve_by_final_raw['ID'];
				
				if
				(
					in_array($cur_id.'', $approve_by) || 
					in_array($cur_id.'', $approve_by_final) || 
					in_array($cur_id.'', $requestor) 
				)
				{
					$post_ids[] = get_the_ID();
				}

			endwhile; 
			wp_reset_postdata();
			endif;

			$query->set( 'post__in', empty( $post_ids ) ? [ 0 ] : $post_ids );

        }
}


add_filter( 'manage_dcm_posts_columns', 'set_custom_edit_dcm_columns', 99 );
add_action( 'manage_dcm_posts_custom_column' , 'set_custom_edit_dcm_column_column', 10, 2 );
function set_custom_edit_dcm_columns( $columns ) {
	
	$columns['author'] = 'Uploaded by';
	$columns['application-status'] = 'Application Status';
	$columns['dco-review-status'] = 'DCO Review Status';
	$columns['review-status'] = 'Review Status';
	$columns['approve-status'] = 'Approve Status';

	return $columns;
}

function set_custom_edit_dcm_column_column( $column, $post_id ) {

	switch ( $column ) {
		case 'dco-review-status' :
			$display = '—';
			$dco_reviewed_by = get_post_meta( $post_id, '_user_dco_reviewed', true );
			if ( $dco_reviewed_by ) {

				$user = get_user_by('ID', $dco_reviewed_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$dco_reviewed_status = get_field( 'dco_review_status', $post_id );
				$display = ( $dco_reviewed_status == 'yes' ? '<label class="table-label-success">Accepted by: ' : '<label class="table-label-danger">Denied by: ' ) . $name . ' ('.$role.')</label>';

			}
			echo $display;
			break;
		case 'review-status' :
			$display = '—';
			$reviewed_by = get_post_meta( $post_id, '_user_reviewed', true );
			if ( $reviewed_by ) {

				$user = get_user_by('ID', $reviewed_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$reviewed_status = get_field( 'review_status', $post_id );
				$display = ( $reviewed_status == 'yes' ? '<label class="table-label-success">Accepted by: ' : '<label class="table-label-danger">Denied by: ' ) . $name . ' ('.$role.')</label>';

			}
			echo $display;
			break;
		case 'approve-status' :

			$display = '—';
			$approved_by = get_post_meta( $post_id, '_user_approved', true );
			if ( $approved_by ) {

				$user = get_user_by('ID', $approved_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$approval_status = get_field( 'approval_status', $post_id );
				$display = ( $approval_status == 'yes' ? '<label class="table-label-success">Accepted by: ' : '<label class="table-label-danger">Denied by: ' ) . $name . ' ('.$role.')</label>';

			}
			echo $display;

			break;

		case 'application-status' :

			$display = '—';
			$dco = get_field( 'dco_review_status' );

			// if ( $dco == 'yes' ) {

			// } else {
				// $display = '<label class="table-label-success">For DCO Approval </label>';
			// }
			// $approved_by = get_post_meta( $post_id, '_user_approved', true );
			// if ( $approved_by ) {

			// 	$user = get_user_by('ID', $approved_by);
			// 	$name = $user->data->display_name;
			// 	$role = ( ($user->roles[0] ? $user->roles[0] : '') );

			// 	$approval_status = get_field( 'approval_status', $post_id );
			// 	$display = ( $approval_status == 'yes' ? '<label class="table-label-success">Accepted by: ' : '<label class="table-label-danger">Denied by: ' ) . $name . ' ('.$role.')</label>';

			// }

			$dco_reviewed_status = get_field( 'dco_review_status', $post_id );
			
			$reviewed_status = get_field( 'review_status', $post_id );
			$approval_status = get_field( 'approval_status', $post_id );
			$for_revision = get_field( 'for_revision', $post_id );
			$assigned_dco_raw =  get_field('assigned_dco', $post_id);
			
			// Check if the post is a draft
			if (get_post_status($post_id) == 'draft') {
				$display = '<label class="table-label-primary"> Unpublished </label>';
			} else {
				if($dco_reviewed_status == 'review'){
					$display = '<label class="table-label-primary"> For Review (DCO Complied) </label> ';
				} else if($dco_reviewed_status == 'yes') {

					if($reviewed_status == 'yes'){

						if($approval_status == 'no') {
							$display = '<label class="table-label-primary"> For Correction </label> ';	
						} else if($approval_status == 'review') {
							$display =  '<label class="table-label-primary"> For Review (Complied) </label> ';
						} else {
							$display = '<label class="table-label-primary"> For Approval </label> ';	
						}
						
					} else if($reviewed_status == 'no'){
						$display = '<label class="table-label-primary"> For Correction </label> ';
					} else if($reviewed_status == 'review'){
						$display =  '<label class="table-label-primary"> For Review (Complied) </label> ';
					} else {
						$display =  '<label class="table-label-primary"> For Recommendation </label> ';
					}

				} else if($dco_reviewed_status == 'no') {
					$display =  '<label class="table-label-primary"> For Correction</label> ';
				} else {
					if($for_revision[0]  == 'yes'){
						if(!empty($assigned_dco_raw)){
							$display =  '<label class="table-label-primary"> Initial Review</label>';
						} else {
							$display =  '<label class="table-label-primary"> For Revision</label>';
						}
						
					} else {
						$display =  '<label class="table-label-primary"> Initial Review</label>';
					}
					
				}

			}
		

			echo $display;

			break;
	}
}

/*qms documents*/
add_filter( 'manage_qms-documents_posts_columns', 'set_custom_edit_qms_documents_columns', 99 );
add_action( 'manage_qms-documents_posts_custom_column' , 'set_custom_edit_qms_documents_column_column', 10, 2 );
function set_custom_edit_qms_documents_columns( $columns ) {

	unset( $columns['taxonomy-services'] );
	$columns['services'] = 'Services';
	$columns['document-label'] = 'Document Label';
	$columns['reviewed_by'] = 'Reviewed By';
	$columns['approved_by'] = 'Approve By';
	$columns['date'] = 'Date of Approval';

	return $columns;
}

function set_custom_edit_qms_documents_column_column( $column, $post_id ) {

	switch ( $column ) {
		case 'reviewed_by' :
			$display = '—';
			$reviewed_by = get_post_meta( $post_id, '_user_reviewed', true );
			if ( $reviewed_by ) {

				$user = get_user_by('ID', $reviewed_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$display = ( $name ? '<label class="table-label-success">' . $name . ' ('.$role.')</label>' : '' );

			}
			echo $display;
			break;
		case 'approved_by' :

			$display = '—';
			$approved_by = get_post_meta( $post_id, '_user_approved', true );
			if ( $approved_by ) {

				$user = get_user_by('ID', $approved_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$display = ( $name ? '<label class="table-label-success">' . $name . ' ('.$role.')</label>' : '' );

			}
			echo $display;

			break;
		case 'services' :

			$display = '—';
			$terms = wp_get_post_terms( $post_id, 'services' );
			foreach( $terms as $i => $term ) {
				if ( $i == 0 ) {
					$display = '<a href="edit.php?post_type=qms-documents&services='.$term->slug.'">'.$term->name.'</a>';
				}

				if ( $term->parent ) {
					$display = '<a href="edit.php?post_type=qms-documents&services='.$term->slug.'">'.$term->name.'</a>';
				}
			}
			echo $display;
			break;

		case 'document-label' :

			$display = '—';

			$terms = wp_get_post_terms( $post_id, 'documents_label' );
			if ( $terms[0] ) {
				$display = '<a href="edit.php?post_type=qms-documents&documents_label='.$terms[0]->slug.'">'.$terms[0]->name.'</a>';
			}
			echo $display;
			break;
	}
}


/*printing documents*/
add_filter( 'manage_printing_posts_columns', 'set_custom_edit_printing_columns', 99 );
add_action( 'manage_printing_posts_custom_column' , 'set_custom_edit_printing_column_column', 10, 2 );
function set_custom_edit_printing_columns( $columns ) {

	$columns['document_title'] = 'Document Title';
	$columns['approval_status'] = 'Approval Status';
	$columns['initial_approver'] = 'Initial Approved By';
	$columns['final_approver'] = 'Final Approved By';
	$columns['requestor'] = 'Requestor';

	return $columns;
}


function set_custom_edit_printing_column_column( $column, $post_id ) {

	switch ( $column ) {
		case 'document_title' :

			$display = '';
			
			$document_title = get_field( 'document_title',  $post_id );
			if ( $document_title->guid ) {

				$display = '<label><a href="'.$document_title->guid.'" target="_blank">' . $document_title->post_title . '</a> </label>';
			}
			echo $display;
			break;
		case 'approval_status' :

			$display = '';
			
			$initial_approval_status = get_field( 'initial_approval_status',  $post_id );
			$final_approval_status = get_field( 'final_approval_status',  $post_id );

			if(!$initial_approval_status && !$final_approval_status){
				$display = 'For DCO Review';

				echo $display;

				break;
			}

			if ( $initial_approval_status == 'yes' && $final_approval_status == 'yes') {


				$display = '<label class="table-label-success">Approved</label>';

				echo $display;

				break;

			} 

			if ( $initial_approval_status == 'no' || $final_approval_status == 'no') {


				$display = '<label class="table-label-success">Disapproved</label>';

				echo $display;

				break;

			} 

			if ( $initial_approval_status == 'yes') {


				$display = '<label class="table-label-success">For Final Approval</label>';

				echo $display;

				break;

			} 

			$display = '<label class="table-label-success">For Initial Approval</label>';

			echo $display;

			break;
		case 'initial_approver' :

			$display = '';
			$approved_by = get_post_meta( $post_id, 'initial_approver', true );
			if ( $approved_by ) {

				$initial_approval_status = get_field( 'initial_approval_status',  $post_id );

				$user = get_user_by('ID', $approved_by);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				if ( $initial_approval_status == 'yes') {

					$display = ( $name ? '<label class="table-label-success">' . $name . ' ('.$role.')</label>' : '' );
				
				} else{

					$display = 'Assigned waiting for approval';
				}

			
			} else {
				$display = 'Not Assigned';
			}
			echo $display;
			break;

		case 'final_approver' :

			$display = '';
			$approved_by_final = get_post_meta( $post_id, 'final_approver', true );
			if ( $approved_by_final ) {

				$final_approval_status = get_field( 'final_approval_status',  $post_id );

				$user = get_user_by('ID', $approved_by_final);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				if ( $final_approval_status == 'yes') {

					$display = ( $name ? '<label class="table-label-success">' . $name . ' ('.$role.')</label>' : '' );
				
				} else{
					
					$display = 'Assigned waiting for approval';
				}
				

			} else {
				$display = 'Not Assigned';
			}
			echo $display;
			break;


		case 'requestor' :

			$display = '';
			// $requestor = get_post_meta( $post_id, 'requestor', true );
			$requestor = get_post_field('post_author',$post_id);

			if ( $requestor ) {

				$user = get_user_by('ID', $requestor);
				$name = $user->data->display_name;
				$role = ( ($user->roles[0] ? $user->roles[0] : '') );

				$display = ( $name ? '<label class="table-label-success">' . $name . ' ('.$role.')</label>' : '' );

			}
			echo $display;
			break;


	}
}


// Add custom column to the users list table and position it after 'Role'
function add_service_column($columns) {
    // Get the position of the 'role' column
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key == 'role') {
            $new_columns['service'] = 'Service'; // Add service column after role
        }
    }
    return $new_columns;
}
add_filter('manage_users_columns', 'add_service_column');

// Populate the custom "Service" column
function show_service_column_content($value, $column_name, $user_id) {
    if ('service' == $column_name) {
        // Get the saved service term ID from user meta
        $user_service_term = get_user_meta($user_id, 'user_service_term', true);


        // Get the term name from the 'services' taxonomy
        if ($user_service_term) {
            $term = get_term($user_service_term, 'services');
            if ($term && !is_wp_error($term)) {
                return esc_html($term->name); // Return the service name
            }
        }
        return 'No service selected'; // Fallback if no service is selected
    }
    return $value;
}
add_filter('manage_users_custom_column', 'show_service_column_content', 10, 3);