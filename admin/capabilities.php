<?php

class Brizy_Admin_Capabilities {

	const CAP_EDIT_WHOLE_PAGE   = 'brizy_edit_whole_page';
	const CAP_EDIT_CONTENT_ONLY = 'brizy_edit_content_only';

	public static function current_user_can( $cap ) {
		// Compatibility with the old versions.
		if ( current_user_can( $cap ) ) {
			return true;
		}

		$stored_roles = Brizy_Editor_Storage_Common::instance()->get( 'roles', false );
		$roles        = self::get_current_user_roles();
		$has_access   = false;

		foreach( $roles as $role ) {
			if ( isset( $stored_roles[ $role ] ) && $stored_roles[ $role ] === $cap ) {
				$has_access = true;
				break;
			}
		}

		return $has_access;
	}

	public static function get_current_user_roles() {

		if ( ! is_user_logged_in() ) {
			return [];
		}

		$user = wp_get_current_user();

		return (array) $user->roles;
	}
}