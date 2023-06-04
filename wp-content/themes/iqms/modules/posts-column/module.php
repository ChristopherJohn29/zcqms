<?php

add_action('pre_get_posts', 'filter_posts_list');

function filter_posts_list($query)
{
    //$pagenow holds the name of the current page being viewed
     global $pagenow, $typenow;  

     	$user = wp_get_current_user();
		$cur_id = $user->ID;
        $allowed_roles = array('author');
        //Shouldn't happen for the admin, but for any role with the edit_posts capability and only on the posts list page, that is edit.php
        if('edit.php' == $pagenow &&  $typenow == 'dcm' && $query->query['fields'] == 'id=>parent')
        { 
        //global $query's set() method for setting the author as the current user's id
			
			$post_ids = array();

			$args = array(
				'post_type' => 'dcm',
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
				
				$author_id = get_post_field( 'post_author', get_the_ID() );
				
				if
				(
					in_array($cur_id, $assigned_dco) || 
					in_array($cur_id, $approved_by) || 
					in_array($cur_id, $review_by) || 
					in_array($cur_id, $users) 
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
				$display =  '<label class="table-label-primary"> Initial Review</label>';
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