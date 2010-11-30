<?php

class LogoutController extends Controller {

	public function __call( $a, $b ) {
		AuditLog::Log( 'Logged out' );
		unset( $_SESSION['user'] );
		session_regenerate_id();
		Flash::AddMessage( 'You have been logged out', FLASH::MESSAGE_SUCCESS );
		header( 'location: /login/' );
		exit;
	}

}
