<?php
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

			if($dco_reviewed_status == 'review'){
				$display = '<label class="table-label-primary"> For Review (DCO) </label> ';
			} else if($dco_reviewed_status == 'yes') {

				if($reviewed_status == 'yes'){
					$display = '<label class="table-label-primary"> For Approval </label> ';
				} else if($reviewed_status == 'no'){
					$display = '<label class="table-label-primary"> For Compliance </label> ';
				} else {
					$display =  '<label class="table-label-primary"> For Review (Complied) </label> ';
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