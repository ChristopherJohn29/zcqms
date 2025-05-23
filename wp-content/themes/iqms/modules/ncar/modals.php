
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
					<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-plus"></i> Nonconformity and Corrective Action Report (NCAR)</h4>
				</div>
				<div class="modal-body">
					<div>

						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#" role="tab"> Nonconformity Definition </a></li>
							<li role="presentation"><a href="#" role="tab" > Correction, Root Cause Analysis and Corrective Action </a></li>
							<li role="presentation"><a href="#" role="tab" > Follow-up </a></li>
							<li role="presentation"><a href="#" role="tab" > Verifications </a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active">

    						<form id="ncar_main_form">
    							<input type="hidden" name="_user_id" value="<?= $this_user ?>">
									<div class="row">
										<div class="col-sm-4">
											
											<div class="form-group">
												<label for="ncar_no">NCAR No.</label>
												<input type="text" class="form-control" placeholder="System Generated" disabled name="ncar_no">
												<input type="hidden" class="form-control" placeholder="" name="ncar_no_new">
											</div>

										</div>

										<div class="col-sm-4">
											
											<div class="form-group">
												<label for="add_date">Date</label>
												<input type="date" class="form-control" name="add_date" value="<?= date('Y-m-d') ?>">
											</div>

										</div>

										<div class="col-sm-4">
											
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
											<label for="source_of_nc">1.Source of NC</label>
											<i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="This is a warning message."></i>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal/ 3rd Party Audit"> Internal/ 3rd Party Audit
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Occupational/ Patient Safety Event"> Occupational/ Patient Safety Event
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Improvement Potential"> Improvement Potential
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Sentinel Event"> Sentinel Event 
												</label>


											</div>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Unmet Goals/ Objectives"> Unmet Goals/ Objectives
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Material or Product"> Material or Product
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Complaints"> Customer Complaints
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Service Nonconformity"> Service Nonconformity
												</label>


											</div>

											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Satisfaction Survey"> Customer Satisfaction Survey 
												</label>
												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal Control Unit"> Internal Control Unit
												</label>
												
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6">

											<div class="form-group">
												<label for="clause_no">Clause No. (Optional)</label>
												<input type="text" name="clause_no" class="form-control" value="">
										
											</div>

										</div>

										<div class="col-sm-6">

											<div class="form-group file-upload" data-multiple-upload="true">
												<label for="evidences">Evidences (If available) <button type="" class="btn btn-info btn-sm upload-btn">Select files</button></label>
												<div class="hidden file-group evidences"></div>
												<input type="text" readonly class="selected_files form-control" value="">
											</div>

										</div>
									</div>

									<div class="row">
										<div class="col-sm-3">

											<div class="form-group">
												<label for="reviewed_by">Responsible Person</label>
												<select id="reviewed_by" name="reviewed_by" class="form-control">
													<option value="">-</option>
													<?php
														$users = get_users('orderby=meta_value&meta_key=first_name');
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
												<label for="description_of_the_noncomformity">2.Description of the Noncomformity</label>
												<textarea class="form-control" rows="5" name="description_of_the_noncomformity" id="description_of_the_noncomformity"></textarea>
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

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="view-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    		
		<div class="modal-header">
			<button type="button" class="close" data-toggle="modal" data-target="#view-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-file"></i> View</h4>
		</div>
		<div class="modal-body">
			<div id="view-form">
				

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
					<h4 class="modal-title" id="gridSystemModalLabel"><i class="glyphicon glyphicon-plus"></i> Nonconformity and Corrective Action Report (NCAR)</h4>
				</div>
				<div class="modal-body">
					<div>

						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#part1" aria-controls="part1" role="tab" data-toggle="tab"> Nonconformity Definition </a></li>
							<li role="presentation"><a href="#part2" aria-controls="part2" role="tab" data-toggle="tab"> Correction, Root Cause Analysis and Corrective Action </a></li>
							<li role="presentation"><a href="#part2b" aria-controls="part2b" role="tab" data-toggle="tab"> Follow-up </a></li>
							<li role="presentation"><a href="#part3" aria-controls="part3" role="tab" data-toggle="tab"> Verifications </a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="part1">

    							<form id="ncar_edit_form">
									<div class="row">
										<div class="col-sm-4">
											
											<div class="form-group">
												<label for="ncar_no">NCAR No.</label>
												<input type="hidden" class="form-control" placeholder="System Generated" disabled name="ncar_no">
												<input type="text" class="form-control" placeholder="" disabled name="ncar_no_new">
											</div>

										</div>

										<div class="col-sm-4">
											
											<div class="form-group">
												<label for="add_date">Date</label>
												<input type="date" class="form-control" name="add_date" value="<?= date('Y-m-d') ?>">
											</div>

										</div>

										<div class="col-sm-4">
											
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
											<label for="source_of_nc">1.Source of NC</label>
											<i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="This is a warning message."></i>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal/ 3rd Party Audit"> Internal/ 3rd Party Audit
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Occupational/ Patient Safety Event"> Occupational/ Patient Safety Event
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Improvement Potential"> Improvement Potential
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Sentinel Event"> Sentinel Event 
												</label>


											</div>
											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Unmet Goals/ Objectives"> Unmet Goals/ Objectives
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Material or Product"> Material or Product
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Complaints"> Customer Complaints
												</label>

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Service Nonconformity"> Service Nonconformity
												</label>


											</div>

											<div class="form-group">

												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Customer Satisfaction Survey "> Customer Satisfaction Survey 
												</label>
												<label class="radio-inline">
													<input type="radio" name="source_of_nc" value="Internal Control Unit"> Internal Control Unit
												</label>
												
											</div>
											
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6">

											<div class="form-group">
												<label for="clause_no">Clause No. (Optional)</label>
												<input type="text" name="clause_no" class="form-control" value="">
											</div>

										</div>

										<div class="col-sm-6">

											<div class="form-group file-upload noncoformity-evidence-file-upload" data-multiple-upload="true">
												<label for="evidences">Evidences (If available) <button type="" class="btn btn-info btn-sm upload-btn">Select files</button></label>
												<div class="hidden file-group evidences" id="noncoformity-evidence"></div>
												<input type="text" readonly class="selected_files form-control" id="noncoformity-evidence-file" value="">
											</div>

										</div>
									</div>

									<div class="row">
										<div class="col-sm-3">

											<div class="form-group">
												<label for="reviewed_by">Responsible Person</label>
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
												<label for="description_of_the_noncomformity">2.Description of the Noncomformity</label>
												<textarea class="form-control" rows="5" name="description_of_the_noncomformity" id="description_of_the_noncomformity"></textarea>
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
												<td colspan="4"><button for="noncoformity-evidence" class="btn btn-primary noncoformity-evidence-file-view">View uploaded non conformity evidences</button></td>
											</tr>
										</tbody>
										<tbody>
											<tr>
												<td colspan="5">4. Correction: (Action to eliminate detected Nonconformity)</td>
												<td>Date</td>
												<td></td>
											</tr>
										</tbody>

										<tbody id="form_2_1">
											
										</tbody>

										<tbody id="form_foot_2_1">
											<tr>
												<td colspan="4"></td>
												<td><button class="pull-right btn btn-primary" id="add_correction">Add Correction</button></td>
												<td></td>
											</tr>
										</tbody>

										<tbody>
											
											<tr>
												<td colspan="6">5. Root Cause Analysis</td>
											</tr>

										</tbody>
										<tbody id="form_2_2">
											
											<tr class="file-upload" data-multiple-upload="true">
												<td colspan="6">
													<div class="hidden file-group evidences root-cause-analysis-file-upload"></div>
													<button class="btn btn-sm btn-primary upload-btn">Upload </button>
													<input class="selected_files root-cause-analysis-file" type="text" readonly placeholder="selected file" readyonly>
												</td>
											</tr>

										</tbody>
										<tbody id="form_foot_2_2">
											<tr>
												<td colspan="4"></td>
												<td><button class="pull-right btn btn-primary" id="add_correction_rca">Add Root Cause Analysis</button></td>
												<td></td>
											</tr>
										</tbody>
										<tbody>
											
											<tr>
												<td colspan="6">6. Corrective Action Plan: (Action to eliminate the cause of the detected nonconformity)</td>
											</tr>
											<tr>
												<td colspan="4">Root Cause/s</td>
												<td>Corrective Action</td>
												<td>Target Date</td>
												<td></td>
											</tr>

										</tbody>
										<tbody id="form_2_3">

										</tbody>
										<tbody id="form_foot_2_3">
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
								<form id="ncar_edit_form2">
									<table class="table">
										<tbody>
											<tr>
												<td colspan="6">7. Follow up on implementation of Action</td>
											</tr>
											<tr>
												<td colspan="6"><button for="noncoformity-evidence" class="btn btn-primary noncoformity-evidence-file-view">View uploaded non conformity evidences</button></td>
											</tr>
										</tbody>
										<tbody>
											<tr>
												<td colspan="4">Correction: (Action to eliminate detected Nonconformity)</td>
												<td>Date</td>
												<!-- <td colspan="2">Implemented As Planned?</td>
												<td></td> -->
											</tr>
										</tbody>

										<tbody id="form_2_1_b">
											
										</tbody>

				

										<tbody>
											
											<tr>
												<td colspan="6"> Root Cause Analysis</td>
											</tr>

										</tbody>
										<tbody id="form_2_2_b">
											
											<tr class="file-upload" data-multiple-upload="true">
												<td colspan="6">
													<button for="noncoformity-evidence" class="btn btn-primary root-cause-analysis-file-view">View uploaded root cause analysis</button>
												</td>
											</tr>

										</tbody>
										<tbody>
											
											<tr>
												<td colspan="6"> Corrective Action Plan: (Action to eliminate the cause of the detected nonconformity)</td>
											</tr>
											<tr>
												<td colspan="">Root Cause/s</td>
												<td>Corrective Action</td>
												<td>Target Date</td>
												<td colspan="2">Implemented As Planned?</td>
												<td></td>
											</tr>

										</tbody>
										<tbody id="form_2_3_b">

										</tbody>
				

									</table>

									<!-- <div class="submit-group" style="display: flex; flex-direction: row-reverse;">
										<button type="submit" class="btn btn-success" id="edit_form2_save_b">Save changes</button>
									</div> -->

									<div class="submit-group" style="display: inline-block; text-align: right; width: 100%;">
										<button type="submit" class="btn btn-success" id="edit_form2_save_satisfactory">Satisfactory</button>
										<button type="submit" class="btn btn-success" id="edit_form2_save_not_satisfactory">Not Satisfactory</button>
									</div>

								</form>
							</div>
							<div role="tabpanel" class="tab-pane" id="part3">
								
								<form id="ncar_edit_form3">
									
									<table class="table">
										<tbody>
											<tr>
												<td colspan="4">
													<button for="noncoformity-evidence" class="btn btn-primary noncoformity-evidence-file-view">View uploaded non conformity evidences</button>
													<button for="noncoformity-evidence" class="btn btn-primary root-cause-analysis-file-view">View uploaded root cause analysis</button>
												</td>
											</tr>
										</tbody>
										<tbody>
											<tr>
												<td colspan="2">8. verifications: On the Effectiveness of Action?</td>
												<td>Remarks</td>
												<td>Date Stamp</td>
												<td></td>
											</tr>
										</tbody>

										<tbody id="form_3_1">
											
										</tbody>
										<tbody id="form_foot_3_1">
											<tr>
												<td colspan="3"></td>
												<td><button class="pull-right btn btn-primary" id="add_verification">Add Verification</button></td>
												<td></td>
											</tr>
										</tbody>

									</table>

									<div class="submit-group" style="display: inline-block; text-align: right; width: 100%;">
										<button type="submit" class="btn btn-success" id="edit_form3_save_satisfactory">Satisfactory</button>
										<button type="submit" class="btn btn-success" id="edit_form3_save_not_satisfactory">Not Satisfactory</button>
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


<!-- remarks modal -->
