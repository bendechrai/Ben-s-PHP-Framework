<?php

/**
 * The user object represents a user of the system
 */

class User extends ActiveRecord {
	protected $__tablename = 'user';

	/**
	 * Compares the provided password with the user's password and returns true if they match (else false)
	 * @access public
	 * @param String $password The password to check
	 * @return Boolean         The result of the comparison
	 */
	public function checkPassword( $password ) {
		return( $this->getPwd() == md5( $password ) );
	}

	/**
	 * Resets the password and emails it to the user
	 * @access oublic
	 * @return void
	 */
	public function resetPassword() {

		$password = $this->generatePassword();

		$this->setPwd( $password );
		if( $this->save() ) {
			mail( $this->getEmail(), Client::GetName() . ' Password Reset', "Your password has been reset for the " . Client::GetName() . " web site.\n\nYou may log in to " . Client::GetUrl() . " as:\nEmail: {$this->getEmail()}\nPassword: {$password}\n\nWe encourage you to change your password in the Manage:Administrators section of the site as soon as possible." );
			AuditLog::Log( 'Password reset', $this );
		} else {
			AuditLog::Log( 'Could not save user after password change', $this );
		}

	}

	/**
	 * Genarates a random password
	 * @access private
	 * @return string The random password
	 */
	private function generatePassword() {
		$validChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
		$passwordLength = 8;
		$password = '';
		for( $j = 1; $j <= $passwordLength; ++$j ) {
			$password .= $validChars{rand(0,strlen($validChars)-1)};
		}
		return $password;
	}

	/**
	 * Overrides setPwd to ensure the password is encrypted
	 * @access public
	 * @param String $password The new password
	 * @return Boolean Result of set operation (for compatibility with overridden method - always true)
	 */
	public function setPwd( $password ) {
		$this->data['pwd'] = md5( $password );
		return true;
	}

	/**
	 * Override save to autogenerate passwords for new users and send an email
	 * @access public
	 * @return boolean True if successful save
	 */
	public function save() {

		// If no id, then new
		$sendemail = false;
		if( !isset( $this->data['id'] ) || $this->data['id'] == '' ) {
			$password = $this->generatePassword();
			$this->setPwd( $password );
			$sendemail = true;
		}

		$success = parent::save();

		if( $success && $sendemail ) {
			mail( $this->getEmail(), Client::GetName() . ' Account Created', "An account has been created for you to use the " . Client::GetName() . " web site.\n\nYou may log in to " . Client::GetUrl() . " as:\nEmail: {$this->getEmail()}\nPassword: {$password}\n\nWe encourage you to change your password in the Manage:Administrators section of the site as soon as possible." );
			AuditLog::Log( 'Password sent', $this );
		}

		return $success;

	}

}
