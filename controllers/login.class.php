<?php

class LoginController extends Controller {

	public function doIndex() {
		include( TEMPLATE_DIR . 'views/login/index.php' );
	}

	public function doLogin() {

		if( isset( $_POST['slug'] ) && Flash::CheckSlug( $_POST['slug']) ) {

			$user = new User();
			if( $user->loadBy( 'email', $_POST['email'] ) && $user->checkPassword( $_POST['password'] ) ) {

				session_regenerate_id();
				$_SESSION['user'] = $user;
				AuditLog::Log( 'Logon successful: ' . $_POST['email'] );
				header( 'location: /' );
				exit;

			} else {
				Flash::AddMessage( 'The email address and password combination did not match any users of this system. Please try again.', FLASH::MESSAGE_WARNING );
				AuditLog::Log( 'Logon failed: ' . $_POST['email'] );
				header( 'location: /login/' );
				exit;
			}

		}

		header( 'location: /login/' );
		exit;

	}

	public function doReset() {

		if( isset( $_POST['slug'] ) && Flash::CheckSlug( $_POST['slug']) ) {

			AuditLog::Log( 'Password reset request for ' . $_POST['email'] );

			// Load user and reset password if found
			$user = new User();
			if( $user->loadBy( 'email', $_POST['email'] ) ) $user->resetPassword();

			// Don't tell user if email address was found to avoid divulging email addresses stored in the system
			Flash::AddMessage( 'We won&rsquo;t tell you if that email address was valid for privacy reasons, but if it was, a new password was just sent to that address.', FLASH::MESSAGE_INFO );

		}

		header( 'location: /login/' );
		exit;

	}

}
