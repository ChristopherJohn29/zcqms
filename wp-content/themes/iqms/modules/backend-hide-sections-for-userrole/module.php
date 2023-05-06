<?php
add_filter( 'admin_body_class', function( $classes ) {

	$this_user = wp_get_current_user();
	$this_user_roles = $this_user->roles;

	$roles = [
		'process-owners',
	];

	foreach( $this_user_roles as $r ) {
		if ( in_array($r, $roles) ) {
			return $classes . ' hide-add-events';
		}
	}
	return $classes;
} );