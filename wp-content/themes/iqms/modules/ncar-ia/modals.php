
<!-- modals -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="add-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
	<?php

		$terms = get_terms( array(
			'taxonomy'   => 'services',
			'hide_empty' => false,
		) );

		$child_terms = array();

		// Loop through the terms and store only the child terms (with non-zero parent)
		foreach( $terms as $term ) {
			if ( $term->parent != 0 ) {
				$child_terms[] = $term;
			}
		}

		usort($child_terms, function($a, $b) {
			return strcmp($a->name, $b->name);
		});


	?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-plus"></i>  Improvement Action (IA)</h4>
				</div>
				<div class="modal-body">
					<div>

						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#" role="tab"> Description of Improvement </a></li>
							<li role="presentation"><a href="#" role="tab" > Improvement Actions </a></li>
							<li role="presentation"><a href="#" role="tab" > Follow-up </a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active">

    						<form id="ncar_main_form">
    							<input type="hidden" name="_user_id" value="<?= $this_user ?>">
									<div class="row">
										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="ncar_no">IA No.</label>
												<input type="text" class="form-control" placeholder="System Generated" disabled name="ncar_ia">
												<input type="hidden" class="form-control" placeholder="System Generated" disabled name="ncar_no">
											</div>

										</div>

										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="add_date">Date</label>
												<input type="date" class="form-control" name="add_date" value="<?= date('Y-m-d') ?>">
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="clause_no">Clause No. (Optional)</label>
												<input type="text" name="clause_no" class="form-control" value="">
											</div>
										</div>

										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="department">Department</label>
												<select id="department" name="department" class="form-control">
													<?php 
														foreach( $child_terms as $term ) {
															echo '<option value="' . esc_attr($term->name) . '">' . esc_html($term->name) . '</option>';
														}
													
													?>
												</select>
											</div>

										</div>

									</div>

									<div class="row">
										<div class="col-sm-12">
											<label for="source_of_nc">Source of Improvement Action</label>
											<i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="This is a warning message."></i>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Management Review"> Management Review
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Feedbacks"> Customer Feedbacks
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal Audit"> Internal Audit 
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Process Monitoring"> Process Monitoring
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="External Audit"> External Audit
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Others"> Others
												</label>
												
												<div class="form-group">
													<input type="text" name="other_source" class="other_source" style="display:none; float: right;">
												</div>
												

											</div>
										</div>
									</div>


									<div class="row">
										<div class="col-sm-3">

											<div class="form-group">
												<label for="reviewed_by">Person responsible</label>
												<select id="reviewed_by" name="reviewed_by" class="form-control">
													<option value="">-</option>
													<?php
														$users = get_users();
														foreach( $users as $user ) {
															echo '<option value="'.$user->data->ID.'">'.$user->data->display_name.'</option>';
														}
													?>
												</select>
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="followup_by">Follow-up By</label>
												<select id="followup_by" name="followup_by" class="form-control">
													<option value="">-</option>
													<?php
													// Get users by specific roles
													$users = get_users( array(
														'role__in' => array('division-chief', 'mcc'), // Filter users by these roles
													));

													// Initialize an array to store users with their first names
													$user_array = array();

													// Loop through the users and retrieve their first names
													foreach( $users as $user ) {
														$first_name = get_user_meta($user->ID, 'first_name', true); // Get the first name of the user
														$user_array[] = array(
															'id'         => $user->ID,
															'first_name' => $first_name,
															'display_name' => $user->display_name,
														);
													}

													// Sort the users by first name
													usort($user_array, function($a, $b) {
														return strcmp($a['first_name'], $b['first_name']);
													});

													// Now display the sorted users
													foreach( $user_array as $user ) {
														echo '<option value="' . esc_attr($user['id']) . '" ' . selected( $user['id'], $this_user, false ) . '>' . esc_html($user['display_name']) . '</option>';
													}
													?>
												</select>
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="approved_by">Verified By</label>
												<select id="approved_by" name="approved_by" class="form-control">
													<option value="">-</option>
													<?php
														// Get users by specific roles
														$users = get_users( array(
															'role__in' => array('pgsqms-chair'), // Filter users by these roles
														));

														// Initialize an array to store users with their first names
														$user_array = array();

														// Loop through the users and retrieve their first names
														foreach( $users as $user ) {
															$first_name = get_user_meta($user->ID, 'first_name', true); // Get the first name of the user
															$user_array[] = array(
																'id'         => $user->ID,
																'first_name' => $first_name,
																'display_name' => $user->display_name,
															);
														}

														// Sort the users by first name
														usort($user_array, function($a, $b) {
															return strcmp($a['first_name'], $b['first_name']);
														});

														// Now display the sorted users
														foreach( $user_array as $user ) {
															echo '<option value="' . esc_attr($user['id']) . '" ' . selected( $user['id'], $this_user, false ) . '>' . esc_html($user['display_name']) . '</option>';
														}
														?>
												</select>
											</div>

										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">

											<div class="form-group">
												<label for="description_of_the_noncomformity">Description of Improvement Action</label>
												<textarea class="form-control" rows="5" name="description_of_improvement_action" id="description_of_improvement_action"></textarea>
											</div>

										</div>
									</div>

									<div class="submit-group" style="display: flex; flex-direction: row-reverse;">
										<button type="submit" class="btn btn-success" id="main_form_save">Save changes</button>
									</div>
									
								</form>
							</div>
						</div>

					</div>
				</div>

    </div>
  </div>
</div>
<!-- edit modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="edit-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    		
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-plus"></i>  Improvement Action (IA)</h4>
				</div>
				<div class="modal-body">
					<div>

						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#part1" aria-controls="part1" role="tab" data-toggle="tab"> Description of Improvement </a></li>
							<li role="presentation"><a href="#part2" aria-controls="part2" role="tab" data-toggle="tab"> Improvement Actions </a></li>
							<li role="presentation"><a href="#part2b" aria-controls="part2b" role="tab" data-toggle="tab"> Follow-up </a></li>
							<li role="presentation"><a href="#part3" aria-controls="part3" role="tab" data-toggle="tab"> Verifications </a></li>

						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="part1">

    						<form id="ncar_edit_form">
									<div class="row">
										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="ncar_no">IA No.</label>
												<input type="text" class="form-control" placeholder="" disabled name="ncar_ia">
												<input type="hidden" class="form-control" placeholder="System Generated" disabled name="ncar_no">
											</div>

										</div>

										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="add_date">Date</label>
												<input type="date" class="form-control" name="add_date" value="<?= date('Y-m-d') ?>">
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="clause_no">Clause No. (Optional)</label>
												<input type="text" name="clause_no" class="form-control" value="">
											</div>
										</div>

										

										<div class="col-sm-3">
											
											<div class="form-group">
												<label for="department">Department</label>
												<select id="department" name="department" class="form-control">
													<?php 
														foreach( $child_terms as $term ) {
															echo '<option value="' . esc_attr($term->name) . '">' . esc_html($term->name) . '</option>';
														}
													
													?>
												</select>
											</div>

										</div>

									</div>

									<div class="row">
										<div class="col-sm-12">
											<label for="source_of_nc">Source of Improvement Action</label>
											<i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="This is a warning message."></i>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Management Review"> Management Review
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Feedbacks"> Customer Feedbacks
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal Audit"> Internal Audit 
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Process Monitoring"> Process Monitoring
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="External Audit"> External Audit
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Others"> Others
												</label>

												<div class="form-group">
													<input type="text" name="other_source" class="other_source" style="display:none; float: right;">
												</div>

											</div>

											
										</div>
									</div>


									<div class="row">
										<div class="col-sm-3">

											<div class="form-group">
												<label for="reviewed_by">Reviewed By</label>
												<select id="reviewed_by" name="reviewed_by" class="form-control">
													<option value="">-</option>
													<?php
														$users = get_users();
														foreach( $users as $user ) {
															echo '<option value="'.$user->data->ID.'">'.$user->data->display_name.'</option>';
														}
													?>
												</select>
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="followup_by">Follow-up by</label>
												<select id="followup_by" name="followup_by" class="form-control">
													<option value="">-</option>
													<?php
													// Get users by specific roles
													$users = get_users( array(
														'role__in' => array('division-chief', 'mcc'), // Filter users by these roles
													));

													// Initialize an array to store users with their first names
													$user_array = array();

													// Loop through the users and retrieve their first names
													foreach( $users as $user ) {
														$first_name = get_user_meta($user->ID, 'first_name', true); // Get the first name of the user
														$user_array[] = array(
															'id'         => $user->ID,
															'first_name' => $first_name,
															'display_name' => $user->display_name,
														);
													}

													// Sort the users by first name
													usort($user_array, function($a, $b) {
														return strcmp($a['first_name'], $b['first_name']);
													});

													// Now display the sorted users
													foreach( $user_array as $user ) {
														echo '<option value="' . esc_attr($user['id']) . '" ' . selected( $user['id'], $this_user, false ) . '>' . esc_html($user['display_name']) . '</option>';
													}
													?>
												</select>
											</div>

										</div>

										<div class="col-sm-3">

											<div class="form-group">
												<label for="approved_by">Verified By</label>
												<select id="approved_by" name="approved_by" class="form-control">
													<option value="">-</option>
														<?php
														// Get users by specific roles
														$users = get_users( array(
															'role__in' => array('pgsqms-chair'), // Filter users by these roles
														));

														// Initialize an array to store users with their first names
														$user_array = array();

														// Loop through the users and retrieve their first names
														foreach( $users as $user ) {
															$first_name = get_user_meta($user->ID, 'first_name', true); // Get the first name of the user
															$user_array[] = array(
																'id'         => $user->ID,
																'first_name' => $first_name,
																'display_name' => $user->display_name,
															);
														}

														// Sort the users by first name
														usort($user_array, function($a, $b) {
															return strcmp($a['first_name'], $b['first_name']);
														});

														// Now display the sorted users
														foreach( $user_array as $user ) {
															echo '<option value="' . esc_attr($user['id']) . '" ' . selected( $user['id'], $this_user, false ) . '>' . esc_html($user['display_name']) . '</option>';
														}
														?>
												</select>
											</div>

										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-12">

											<div class="form-group">
												<label for="description_of_the_noncomformity">Description of Improvement Action</label>
												<textarea class="form-control" rows="5" name="description_of_improvement_action" id="description_of_improvement_action"></textarea>
											</div>

										</div>
									</div>

									<div class="submit-group" style="display: flex; flex-direction: row-reverse;">
										<button type="submit" class="btn btn-success" id="edit_form_save">Save changes</button>
									</div>
									
								</form>
							</div>
							<div role="tabpanel" class="tab-pane" id="part2">
								<form id="ncar_edit_form2">
									<table class="table">
										<tbody>
											<tr>
												<td colspan="2">2.1 Improvement Action</td>
												<td>Target Date</td>
												<td>Completion Date</td>
												<td colspan="2">Attachment</td>
												<td></td>
											</tr>
										</tbody>

										<tbody id="form_2_1">
											
										</tbody>

										<tbody id="form_foot_2_1">
											<tr>
												<td colspan="2"></td>
												<td><button class="pull-right btn btn-primary" id="add_correction">Add improvement action</button></td>
												<td></td>
											</tr>
										</tbody>

										<tbody class="hidden">
											
											<tr>
												<td colspan="6">2.2 Root Cause Analysis</td>
											</tr>

										</tbody>
										<tbody id="form_2_2" class="hidden">
											
											<tr class="file-upload" data-multiple-upload="true">
												<td colspan="6">
													<div class="hidden file-group evidences"></div>
													<button class="btn btn-sm btn-primary upload-btn">Upload </button>
													<input class="selected_files" type="text" readonly placeholder="selected file" readyonly>
												</td>
											</tr>

										</tbody>
										<tbody class="hidden">
											
											<tr>
												<td colspan="6">2.3 Corrective Action: (Action to eliminate the cause of the detected nonconformity)</td>
											</tr>
											<tr>
												<td>Root Cause/s</td>
												<td>Corrective Action</td>
												<td>Completion Date</td>
												<td>Attachment</td>
												<td colspan="2">Implemented As Planned?</td>
												<td></td>
											</tr>

										</tbody>
										<tbody id="form_2_3" class="hidden">

										</tbody>
										<tbody id="form_foot_2_3" class="hidden">
											<tr>
												<td colspan="4"></td>
												<td><button class="pull-right btn btn-primary" id="add_corrective_action">Add Corrective Action</button></td>
												<td></td>
											</tr>
										</tbody>

									</table>

									<div class="submit-group" style="display: flex; flex-direction: row-reverse;">
										<button type="submit" class="btn btn-success" id="edit_form2_save">Save changes</button>
									</div>
								</form>
							</div>
							<div role="tabpanel" class="tab-pane" id="part2b">
								
								<form id="ncar_edit_form3">
									<table class="table">
										<tbody>
											<tr>
												<td colspan="1">3. Follow-up on Implementation of action</td>
												<td>Target Date</td>
												<td>Completion Date</td>
												<td colspan="2">Implemented As Planned?</td>
												<td></td>
											</tr>
										</tbody>

										<tbody id="form_3_1">
											
										</tbody>
								

									</table>


									<div class="submit-group" style="display: inline-block; text-align: right; width: 100%;">
										<button type="submit" class="btn btn-success" id="edit_form3_save_satisfactory">Satisfactory</button>
										<button type="submit" class="btn btn-success" id="edit_form3_save_not_satisfactory">Not Satisfactory</button>
									</div>

								</form>

							</div>

							<div role="tabpanel" class="tab-pane" id="part3">
								
								<form id="ncar_edit_form3b">
									
									<table class="table">
										<tbody>
											<tr>
												<td colspan="2">3. Verification on the effectiveness of action</td>
												<td>Remarks</td>
												<td>Date Stamp</td>
												<td></td>
											</tr>
										</tbody>

										<tbody id="form_3_1_b">
											
										</tbody>
										<tbody id="form_foot_3_1_b">
											<tr>
												<td colspan="3"></td>
												<td><button class="pull-right btn btn-primary" id="add_verification">Add Verification</button></td>
												<td></td>
											</tr>
										</tbody>

									</table>

									<div class="submit-group" style="display: inline-block; text-align: right; width: 100%;">
										<button type="submit" class="btn btn-success" id="edit_form3b_save_satisfactory">Satisfactory</button>
										<button type="submit" class="btn btn-success" id="edit_form3b_save_not_satisfactory">Not Satisfactory</button>
									</div>
								</form>

							</div>
						</div>

					</div>
				</div>

    </div>
  </div>
</div>
<!-- remarks modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="remarks-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    		
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-file"></i> NCAR Remarks</h4>
				</div>
				<div class="modal-body">
					<div id="remarks-form">
						<input type="hidden" name="ncar_no">
						<div class="form-group">
							<textarea class="form-control" rows="5" id="ncar_remarks" placeholder="Write a remarks for this NCAR"></textarea>
						</div>
						<div class="submit-group" style="display: flex; flex-direction: row-reverse;">
							<button type="submit" class="btn btn-success" id="save_remarks">Save changes</button>
						</div>

					</div>
				</div>

    </div>
  </div>
</div>