<?php
if ( !class_exists( 'User_Restrictions_Module' ) ) {
	class User_Restrictions_Module {

		function __construct() {
			$this->URM_backend_hooks();
			$this->URM_frontend_hooks();
		}

		function URM_backend_hooks() {
			add_action( 'admin_menu', array( $this, 'URM_admin_menu' ) );
			add_action( 'wp_ajax_urm_update_users', array( $this, 'urm_update_users' ) );
			add_action( 'wp_ajax_nopriv_urm_update_users', array( $this, 'urm_update_users' ) );

			/*class*/
			add_filter( 'admin_body_class', array( $this, 'urm_body_class' ) );
		}

		function urm_body_class( $classes ) {
			$k = 'urm_roles';
			$options = ( get_option( $k ) ? get_option( $k ) : [] );

			$this_role = wp_get_current_user()->roles;
			$user_cant_edit = false;
			$user_cant_create = false;

			foreach( $this_role as $r ) {
				if ( $options[$r] ) {
					
					if ( $options[$r]['edit'] === 'false' ) {
						$user_cant_edit = true;
					}

					if ( $options[$r]['create'] === 'false' ) {
						$user_cant_create = true;
					}
				}
				
			}

			if ( $user_cant_edit ) {
				$classes .= ' urm-user-cant-edit' ;
			}
			if ( $user_cant_create ) {
				$classes .= ' urm-user-cant-create' ;
			}

		    return $classes;
		}

		function urm_update_users() {
    		$k = 'urm_roles';

    		$options = ( get_option( $k ) ? get_option( $k ) : [] );

    		$data = $_POST['data'];
    		$index = $data['value'];
    		$role = $data['type'];

    		if ( $options[$index] ) {
    			$options[$index][$role] = $data['checked'];
    		} else {
    			$options[$index] = [ $role => $data['checked'] ];
    		}
    		
    		if ( update_option( $k, $options ) ) {
    			echo json_encode( [
    				'id' => $data['id'],
    				'checked' => $data['checked'],
    			] );
    		} else {
    			echo json_encode( ['err' => true] );
    		}
			exit;
		}

		function URM_admin_menu() {

			add_options_page(
				__( 'User Restrictions', 'textdomain' ),
				__( 'User Restrictions', 'textdomain' ),
				'manage_options',
				'user_restrictions',
				array(
					$this,
					'URM_settings_page'
				)
			);
		}

		function URM_settings_page() {

			global $wp_roles;
    		$all_roles = $wp_roles->roles;

    		/*check option*/
    		$k = 'urm_roles';
    		$options = [];
    		if ( get_option( $k ) === false ) {
    			add_option( $k, [], 'no' );
    		} else {
    			$options = get_option( $k );
    		}

			?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

				<style type="text/css">
					.cmn-toggle {
						position: absolute;
						margin-left: -9999px;
						visibility: hidden;
					}
					.cmn-toggle + label {
						display: block;
						position: relative;
						cursor: pointer;
						outline: none;
						user-select: none;
					}
					input.cmn-toggle-round + label {
						padding: 2px;
						width: 60px;
						height: 30px;
						background-color: #dddddd;
						border-radius: 30px;
					}
					input.cmn-toggle-round + label:before,
					input.cmn-toggle-round + label:after {
						display: block;
						position: absolute;
						top: 1px;
						left: 1px;
						bottom: 1px;
						content: "";
					}
					input.cmn-toggle-round + label:before {
						right: 1px;
						background-color: #f1f1f1;
						border-radius: 30px;
						transition: background 0.4s;
					}
					input.cmn-toggle-round + label:after {
						width: 29px;
						background-color: #fff;
						border-radius: 100%;
						box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
						transition: margin 0.4s;
					}
					input.cmn-toggle-round:checked + label:before {
						background-color: #8ce196;
					}
					input.cmn-toggle-round:checked + label:after {
						margin-left: 30px;
					}
					.settings_page_user_restrictions table.table tr th:first-child {
						width: 70%;
					}
					.settings_page_user_restrictions table.table {
						border: 1px solid;
					}
					
				</style>
				<script type="text/javascript">
					(function($){

						$(document).ready(function(){

							$('.create-toggle').on('click', function(e){
								e.preventDefault();

								data = {
									type: 'create',
									value: $(this).val(),
									checked: ($(this).is(':checked')),
									id: $(this).data('id')
								}

								ajaxRequest( data );
							});

							$('.edit-toggle').on('click', function(e){
								e.preventDefault();

								data = {
									type: 'edit',
									value: $(this).val(),
									checked: ($(this).is(':checked')),
									id: $(this).data('id')
								}

								ajaxRequest( data );
							});

							function ajaxRequest(data) {
								$.ajax({
									url: location.origin + '/wp-admin/admin-ajax.php',
									type: 'POST',
									dataType: 'json',
									data: {
										action: 'urm_update_users',
										data: data
									},
									success: function(r) {

										if ( r.err ) {
											/*err, do nothing*/
										} else {
											c = ( r.checked == 'true' ? true : false );
											$('#'+r.id).prop('checked', c);
										}

									}
								});
							}
						});

					})(jQuery)
				</script>
				<div class="wrap">
					<h1>User Restrictions</h1>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>User Role</th>
								<th>Can View?</th>
								<th>Can Create?</th>
								<th>Can Edit?</th>
							</tr>
						</thead>

						<tbody>
							<?php
								$i = 0;
								foreach( $all_roles as $slug => $role ) {
									if ( $slug == 'administrator' ) 
										continue;

									$create_checked = ( $options[$slug]['create'] ? $options[$slug]['create'] : 'false' );
									$edit_checked = ( $options[$slug]['edit'] ? $options[$slug]['edit'] : 'false' );
									?>
									<tr>
										<td><?= $role['name'] ?></td>
										<td>
											<input id="can-view-<?=$i?>" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled checked>
  											<label for="can-view-<?=$i?>"></label>
										</td>
										<td>
											<input id="can-create-<?=$i?>" data-id="can-create-<?=$i?>" class="cmn-toggle cmn-toggle-round create-toggle" type="checkbox" value="<?= $slug ?>" <?= ( $create_checked == 'true' ? 'checked' : '' ) ?> >
  											<label for="can-create-<?=$i?>"></label>
										</td>
										<td>
											<input id="can-edit-<?=$i?>" data-id="can-edit-<?=$i?>" class="cmn-toggle cmn-toggle-round edit-toggle" type="checkbox" value="<?= $slug ?>" <?= ( $edit_checked == 'true' ? 'checked' : '' ) ?>>
  											<label for="can-edit-<?=$i?>"></label>
										</td>
									</tr>
									<?php
									$i++;
								}
							?>
						</tbody>
					</table>
				</div>
			<?php
		}

		function URM_frontend_hooks() {

		}

	}
	$User_Restrictions_Module = new User_Restrictions_Module();
}