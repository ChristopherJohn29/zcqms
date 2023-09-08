<link href="<?= get_stylesheet_directory_uri() ?>/modules/custom-dashboard/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= get_stylesheet_directory_uri() ?>/modules/custom-dashboard/assets/css/font-awesome.min.css" rel="stylesheet">
<link href="<?= get_stylesheet_directory_uri() ?>/modules/custom-dashboard/assets/css/nprogress.css" rel="stylesheet">
<link href="<?= get_stylesheet_directory_uri() ?>/modules/custom-dashboard/assets/css/custom.min.css" rel="stylesheet">
<!-- page content -->
<div class="right_col" role="main">
	<!-- top tiles -->
	<div class="row" style="display: inline-block;" >
		<div class="tile_count">
			<?php
				$users = count_users();
				/*dcm*/
				$dcm = new WP_Query( [
					'post_type' => 'dcm',
					'posts_per_page' => 6
				] );

				$qms = new WP_Query( [
					'post_type' => 'qms-documents',
					'posts_per_page' => 6
				] );

				$ncar = new WP_Query( [
					'post_type' => 'ncar',
					'posts_per_page' => 6
				] );

				$ia = new WP_Query( [
					'post_type' => 'ncar-ia',
					'posts_per_page' => 6
				] );
				

				if(get_option('visitor_count')){
					$v_count = get_option('visitor_count');
				} else {
					$v_count = 0;
				}

			?>
			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Total Users</span>
				<div class="count red"><?= $users['total_users'] ?></div>
				<span class="count_bottom"><a href="<?= get_site_url() ?>/wp-admin/users.php" target="_blank">View All Users</a></span>
			</div>

			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Total DCM</span>
				<div class="count green"><?= $dcm->found_posts ?></div>
				<span class="count_bottom"><a href="<?= get_site_url() ?>/wp-admin/edit.php?post_type=dcm" target="_blank">View All DCM</a></span>
			</div>

			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Total QMS Documents</span>
				<div class="count blue"><?= $qms->found_posts ?></div>
				<span class="count_bottom"><a href="<?= get_site_url() ?>/wp-admin/edit.php?post_type=qms-documents" target="_blank">View All QMS Documents</a></span>
			</div>

			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Total NCAR</span>
				<div class="count"><?= $ncar->found_posts ?></div>
				<span class="count_bottom"><a href="<?= get_site_url() ?>/wp-admin/admin.php?page=ncar" target="_blank">View All NCAR</a></span>
			</div>

			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Total Improvement Action</span>
				<div class="count"><?= $ia->found_posts ?></div>
				<span class="count_bottom"><a href="<?= get_site_url() ?>/wp-admin/admin.php?page=ncar-ia" target="_blank">View All Improvement Action</a></span>
			</div>

			<div class="col-md-2 col-sm-4  tile_stats_count">
				<span class="count_top"><i class="glyphicon glyphicon-user"></i> Visitor Count</span>
				<div class="count"><?= $v_count ?></div>
			</div>

		</div>
	</div>
	<!-- /top tiles -->
</div>

<div class="row">
	<div class="col-lg-3">
		<h2>Recent DCM</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ( $dcm->have_posts() ) {
						foreach( $dcm->posts as $post ) {
							echo '<tr><td>'.$post->post_title.'</td><td><a class="btn btn-primary btn-sm" href="'.get_site_url().'/wp-admin/post.php?post='.$post->ID.'&action=edit" target="_blank">View</a></td></tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>

	<div class="col-lg-3">
		<h2>Recent QMS Documents</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ( $qms->have_posts() ) {
						foreach( $qms->posts as $post ) {
							echo '<tr><td>'.$post->post_title.'</td><td><a class="btn btn-primary btn-sm" href="'.get_site_url().'/wp-admin/post.php?post='.$post->ID.'&action=edit" target="_blank">View</a></td></tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>

	<div class="col-lg-3">
		<h2>Recent NCAR</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ( $ncar->have_posts() ) {
						foreach( $ncar->posts as $post ) {
							$title = get_post_meta( $post->ID, 'source_of_nc', true );
							echo '<tr><td>'.$title.'</td><td><a class="btn btn-primary btn-sm" href="'.get_site_url().'/wp-admin/admin.php?page=ncar" target="_blank">View</a></td></tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>

	<div class="col-lg-3">
		<h2>Recent Improvement Action</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ( $ia->have_posts() ) {
						foreach( $ia->posts as $post ) {
							$title = get_post_meta( $post->ID, 'source_of_nc', true );
							echo '<tr><td>'.$title.'</td><td><a class="btn btn-primary btn-sm" href="'.get_site_url().'/wp-admin/admin.php?page=ncar-ia" target="_blank">View</a></td></tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>