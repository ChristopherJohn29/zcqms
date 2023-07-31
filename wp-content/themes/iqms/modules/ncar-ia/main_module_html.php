<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<!-- resources -->
<div class="wrap">
	<div class="section-head">
		<button class="btn btn-primary pull-right" id="btn-add"><i class="glyphicon glyphicon-plus"></i> Create new AI</button>
		<h1> Improvement Action Dashboard </h1>
	</div>
	<div class="section-body">
		<table class="table table-striped table-hover" id="ncar-main">
			<thead>
				<tr>
					<th>AI No.</th>
					<th>Issued By</th>
					<th>Source</th>
					<th>Review by</th>
					<th>Follow-up by</th>
					<th>Date Issued</th>
					<th>Status</th>
					<th>Action Items</th>
					<th>Remarks</th>
				</tr>
			</thead>

			<tbody>
				<?php
					$this_user = get_current_user_id();
					$args = [
						'post_type' => 'ncar-ia',
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

					foreach( $query->posts as $ncar ) {

						$id = $ncar->ID;

						$author = get_user_by( 'ID', $ncar->post_author );
						$author = $author->data->display_name;
						$reviewed_by_id = get_post_meta( $id, 'reviewed_by', true );
						$reviewed_by = get_user_by( 'ID', $reviewed_by_id );
						$approved_by_id = get_post_meta( $id, 'approved_by', true );
						$approved_by = get_user_by( 'ID', $approved_by_id );
						$source = get_post_meta( $id, 'source_of_nc', true );
						$department = get_post_meta( $id, 'department', true );
						$nc_desc = get_post_meta( $id, 'description_of_the_noncomformity', true );
						$date = get_post_meta( $id, 'add_date', true );
						$clause_no = get_post_meta( $id, 'clause_no', true );

						?>
						<tr data-id="<?= $ncar->ID ?>">
							<td><?= $ncar->ID ?></td>
							<td><?= $author ?></td>
							<td><?= $reviewed_by->data->display_name; ?></td>
							<td><?= $approved_by->data->display_name; ?></td>
							<td><?= $source ?></td>
							<td><?= $department ?></td>
							<td><?= $date ?></td>
							<td>N/A</td>
							<td class="action-group">
								<button class="btn btn-sm btn-success btn-edit"><i class="glyphicon glyphicon glyphicon-eye-open"></i> <i class="glyphicon glyphicon glyphicon-pencil"></i></button> 
								<button class="btn btn-sm btn-primary btn-remarks"><i class="glyphicon glyphicon glyphicon-file"></i></button>
								<?= ( get_post_meta( $id, '_user_id', true ) == $this_user ? '<button class="btn btn-sm btn-danger btn-delete"><i class="glyphicon glyphicon glyphicon-trash"></i></button>' : '' ) ?>
							</td>
							<td>-</td>
						</tr>

						<?php
					}

				?>
			</tbody>
		</table>
	</div>
</div>