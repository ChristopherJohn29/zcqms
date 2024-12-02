<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<!-- resources -->
<div class="wrap">
	<div class="section-head">
		<button class="btn btn-primary pull-right" id="btn-add"><i class="glyphicon glyphicon-plus"></i> Create new NCAR</button>
		<button class="btn btn-primary pull-right" id="btn-download"><i class="glyphicon glyphicon-download"></i> Download NCAR Report</button>
		<h1> NCAR Dashboard </h1>
	</div>

	<div class="filters">
        <div class="row">
            <div class="col-md-3">
                <label for="filter-source">Source</label>
				<select id="filter-source" class="form-control">
					<option value="">All</option>
					<option value="Internal/ 3rd Party Audit">Internal/ 3rd Party Audit</option>
					<option value="Occupational/ Patient Safety Event">Occupational/ Patient Safety Event</option>
					<option value="Improvement Potential">Improvement Potential</option>
					<option value="Sentinel Event">Sentinel Event</option>
					<option value="Unmet Goals/ Objectives">Unmet Goals/ Objectives</option>
					<option value="Material or Product">Material or Product</option>
					<option value="Customer Complaints">Customer Complaints</option>
					<option value="Service Nonconformity">Service Nonconformity</option>
					<option value="Customer Satisfaction Survey">Customer Satisfaction Survey</option>
					<option value="Internal Control Unit">Internal Control Unit</option>
				</select>
            </div>
            <div class="col-md-3">
                <label for="filter-department">Department</label>
                <input type="text" id="filter-department" class="form-control" placeholder="Department">
            </div>
			<div class="col-md-3">
				<label for="filter-date-issued-from">Date Issued From</label>
				<input type="date" id="filter-date-issued-from" class="form-control">
			</div>
			<div class="col-md-3">
				<label for="filter-date-issued-to">Date Issued To</label>
				<input type="date" id="filter-date-issued-to" class="form-control">
			</div>
            <div class="col-md-3">
                <label for="filter-clause-no">Clause No.</label>
                <input type="text" id="filter-clause-no" class="form-control" placeholder="Clause No.">
            </div>
            <div class="col-md-3">
                <label for="filter-status">Status</label>
                <select id="filter-status" class="form-control">
                    <option value="">All</option>
                    <option value="For Action">For Action</option>
                    <option value="For Follow up">For Follow up</option>
					<option value="For Verification">For Verification</option>
					<option value="Closed">Closed</option>
                    <!-- Add more status options as needed -->
                </select>
            </div>
        </div>
    </div>
	<div class="section-body" style="margin-top:10px">
		<table class="table table-striped table-hover" id="ncar-main">
			<thead>
				<tr>
					<th>NCAR No.</th>
					<th>Detected By</th>
					<th>Responsible person</th>
					<th>Follow-up by</th>
					<th>Verified by</th>
					<th>Source</th>
					<th>Department</th>
					<th>Date Issued</th>
					<th>Date Closed</th>
					<th>Clause No.</th>
					<th>Status</th>
					<th>Action Items</th>
					<th>Remarks</th>
				</tr>
			</thead>

			<tbody>
				<?php

					$cur_user = wp_get_current_user();
					$roles = $cur_user->roles;

					$this_user = get_current_user_id();
					$args = [
						'post_type' => 'ncar',
						'posts_per_page' => -1,
						// 'meta_query' => [
						// 	'relation' => 'OR',
						// 	[
						// 		'key' => '_user_id',
						// 		'value' => $this_user,
						// 		'compare' => '='
						// 	],
						// 	[
						// 		'key' => 'reviewed_by',
						// 		'value' => $this_user,
						// 		'compare' => '='
						// 	],
						// 	[
						// 		'key' => 'approved_by',
						// 		'value' => $this_user,
						// 		'compare' => '='
						// 	]
						// ]
					];
					$query = new WP_Query( $args );

					$source_of_nc = array(
						'iqa-lead' => array(
							'Internal/ 3rd Party Audit',
							'Improvement Potential',
							'Unmet Goals/ Objectives',
							'Service Nonconformity',
						),
						'iqa-lead / safety-environment-unit-head' => array(
							'Occupational/ Patient Safety Event',
							'Sentinel Event',
						),
						'iqa-lead / materials-management-head' => array(
							'Material or Product',
						),
						'iqa-lead / paccu-supervisor' => array(
							'Customer Complaints',
							'Customer Satisfaction Survey',
						),
						'iqa-lead / internal-control-unit-head' => array(
							'Internal Control Unit',
						),
					);
					
					// Function to find matching NC sources for the user based on their roles
					function find_sources_for_user($source_of_nc, $roles) {
						$matched_sources = [];
						foreach ($source_of_nc as $role => $sources) {
							$role_parts = array_map('trim', explode('/', $role));
							foreach ($roles as $user_role) {
								if (in_array($user_role, $role_parts)) {
									$matched_sources = array_merge($matched_sources, (array)$sources);
								}
							}
						}
						return $matched_sources;
					}

					foreach( $query->posts as $ncar ) {

						$id = $ncar->ID;

						$author = get_user_by( 'ID', $ncar->post_author );
						$author = $author->data->display_name;
						$ncar_no_new = get_post_meta( $id, 'ncar_no_new', true );
						$status = get_post_meta( $id, 'status', true );
						$source = get_post_meta($id, 'source_of_nc', true);
						$department = get_post_meta( $id, 'department', true );
						$nc_desc = get_post_meta( $id, 'description_of_the_noncomformity', true );
						$date = get_post_meta( $id, 'add_date', true );
						$close_date = get_post_meta( $id, 'close_date', true );
						$clause_no = get_post_meta( $id, 'clause_no', true );
						$verification = get_post_meta( $id, 'verification', true );

						$reviewed_by = get_post_meta( $id, 'reviewed_by', true );
						$reviewed_by_raw = get_user_by( 'ID', $reviewed_by );
						$reviewed_by_person = $reviewed_by_raw->data->display_name;

						$followup_by = get_post_meta( $id, 'followup_by', true );
						$followup_by_raw = get_user_by( 'ID', $followup_by );
						$followup_by_person = $followup_by_raw->data->display_name;

						$approved_by = get_post_meta( $id, 'approved_by', true );
						$approved_by_raw = get_user_by( 'ID', $approved_by );
						$approved_by_person = $approved_by_raw->data->display_name;

						$verified = 1;

						$final_decision = get_post_meta( $id, 'final_decision', true );

						

						// if(is_array($verification)){
						// 	foreach ($verification as $key => $value) {
						// 		if(isset($value['verification_implemented'])){
						// 			if($value['verification_implemented'] == 'No'){
						// 				$verified = 0;
						// 			}
						// 		}
						// 	}
						// } else {
						// 	$verified = 0;
						// }

						$sources_for_user = find_sources_for_user($source_of_nc, $roles);

						if(
							$reviewed_by == $this_user || 
							$followup_by == $this_user || 
							$approved_by == $this_user || 
							$ncar->post_author == $this_user || 
							$roles[0] == 'administrator' || 
							$roles[0] == 'dco' ||
							in_array($source, $sources_for_user)
							){

						?>
						<tr data-id="<?= $ncar->ID ?>">
							<td><?= $ncar_no_new ? $ncar_no_new : $ncar->ID ?></td>
							<td><?= $author ?></td>
							<td><?= $reviewed_by_person ?></td>
							<td><?= $followup_by_person ?></td>
							<td><?= $approved_by_person ?></td>
							<td><?= $source ?></td>
							<td><?= $department ?></td>
							<td><?= $date ?></td>
							<td><?= $close_date ?></td>
							<td><?= $clause_no ?></td>
							<td><?=$status ?></td>
							<td class="action-group">
								<button class="btn btn-sm btn-success btn-edit"><i class="glyphicon glyphicon glyphicon-eye-open"></i> <i class="glyphicon glyphicon glyphicon-pencil"></i></button> 
								<button class="btn btn-sm btn-primary btn-remarks"><i class="glyphicon glyphicon glyphicon-file"></i></button>
								<?= ( get_post_meta( $id, '_user_id', true ) == $this_user ? '<button class="btn btn-sm btn-danger btn-delete"><i class="glyphicon glyphicon glyphicon-trash"></i></button>' : '' ) ?>
							</td>
							<td>-</td>
						</tr>

						<?php
						}
					}

				?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#btn-download').on('click', function(e) {
            e.preventDefault();
            window.location.href = '<?php echo admin_url('admin-post.php?action=download_ncar_report'); ?>';
        });
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize DataTable
        var table = $('#ncar-main').DataTable();

        // Custom filtering for "Source"
        $('#filter-source').on('change', function() {
            table.column(5).search(this.value).draw(); // Column index 5 is "Source"
        });

        // Custom filtering for "Department"
        $('#filter-department').on('keyup', function() {
            table.column(6).search(this.value).draw(); // Column index 6 is "Department"
        });

        // Custom filtering for "Clause No."
        $('#filter-clause-no').on('keyup', function() {
            table.column(9).search(this.value).draw(); // Column index 9 is "Clause No."
        });

        // Custom filtering for "Status"
        $('#filter-status').on('change', function() {
            table.column(10).search(this.value).draw(); // Column index 10 is "Status"
        });

        // Custom filtering for "Date Issued From" and "Date Issued To"
        $('#filter-date-issued-from, #filter-date-issued-to').on('change', function() {
            var fromDate = $('#filter-date-issued-from').val();
            var toDate = $('#filter-date-issued-to').val();

            // Filter logic
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var dateIssued = data[7] || ''; // Assuming column index 7 is "Date Issued"
                if (!dateIssued) return true;

                // Convert to Date objects
                var dateIssuedObj = new Date(dateIssued);
                var fromDateObj = fromDate ? new Date(fromDate) : null;
                var toDateObj = toDate ? new Date(toDate) : null;

                // Check if the date falls within the range
                if (
                    (!fromDateObj || dateIssuedObj >= fromDateObj) &&
                    (!toDateObj || dateIssuedObj <= toDateObj)
                ) {
                    return true;
                }
                return false;
            });

            table.draw();
        });

        // Download button functionality
        $('#btn-download').on('click', function(e) {
            e.preventDefault();
            window.location.href = '<?php echo admin_url('admin-post.php?action=download_ncar_report'); ?>';
        });
    });
</script>
