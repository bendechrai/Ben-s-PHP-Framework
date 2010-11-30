<?php

/**
 * Performs audit logging and is capable of:
 * - Sending email notifications to the site administrator
 * - Producing a E_USER_WARNING for in-site debgging
 * - Logging the message to the audit log database table
 */

class AuditLog extends ActiveRecord {

	const SENDEMAIL = true;
	const NOEMAIL = false;

	protected $__tablename = 'auditlog';

	/**
	 * Save to the database
	 * @static
	 * @access public
	 * @param String $message The error message
	 * @param Object $source  The object this log is related to (optional)
	 * @param Object $debug   Debug information (optional)
	 */
	static public function Log( $message, $source=null, $debug=null ) {
		self::SaveToDB( $message, $source, $debug );
	}

	/**
	 * Error() sends an email, produces a warning and save to the database
	 * @static
	 * @access public
	 * @param String $message The error message
	 * @param Object $source  The object this log is related to (optional)
	 * @param Object $debug   Debug information (optional)
	 */
	static public function Error( $message, $source=null, $debug=null ) {
		self::Email( $message, $source, $debug );
		self::Warn( $message, $source, $debug );
	}

	/**
	 * Saves log information to the database table
	 * @static
	 * @access private
	 * @param String $message The error message
	 * @param Object $source  The object this log is related to
	 * @param Object $debug   Debug information
	 * @return void
	 */
	static private function SaveToDB( $message, $source, $debug ) {
		$auditlog = new AuditLog();
		if( isset( $_SESSION['user'] ) && $_SESSION['user'] instanceof User ) {
			$auditlog->setUser_ID( $_SESSION['user']->getId() );
		} else {
			$auditlog->setUser_ID( 0 );
		}
		$auditlog->setMessage( $message );
		$auditlog->setClass( get_class( $source ) );
		if( is_object( $source ) && method_exists( $source, 'getID' ) ) {
			$auditlog->setObject_ID( $source->getId() );
		} else {
			$auditlog->setObject_ID( 0 );
		}

		// If debug not set, use source
		// Else if debug is an array, add $source to the start
		// Else create array with $source and $debug
		if( is_null( $debug ) ) $debug = $source; elseif( is_array( $debug ) ) array_unshift( $debug, $source ); else $debug = array( $source, $debug );

		$auditlog->setExtra( print_r( $debug, true ) );
		$auditlog->save();
	}

	/**
	 * Sends a PHP user_error regarding the error and then calls Save so debug information isn't lost
	 * @static
	 * @access private
	 * @param String $message The error message
	 * @param Object $source  The object this log is related to
	 * @param Object $debug   Debug information
	 * @return void
	 */
	static private function Warn( $message, $source, $debug ) {
		user_error( $message, E_USER_WARNING );
		self::SaveToDB( $message, $source, $debug );
	}

	/**
	 * Sends an email to the site administrator
	 * @static
	 * @access private
	 * @param String $message The error message
	 * @param Object $source  The object this log is related to
	 * @param Object $debug   Debug information
	 * @return void
	 */
	static private function Email( $message, $source, $debug ) {
		// If debug not set, use source
		// Else if debug is an array, add $source to the start
		// Else create array with $source and $debug
		if( is_null( $debug ) ) $debug = $source; elseif( is_array( $debug ) ) array_unshift( $debug, $source ); else $debug = array( $source, $debug );

		$emailmessage = "An error occured at {$_SERVER['SERVER_NAME']}\n\n";
		$emailmessage .= "Message: $message\n\n";
		$emailmessage .= "Source:\n\n";
		$emailmessage .= print_r( $source, true ) . "\n\n";
		$emailmessage .= "Debug:\n\n";
		$emailmessage .= print_r( $debug, true ) . "\n\n";
		mail( 'ben.balbo@sputnikagency.com', 'SCMS Error', $emailmessage );
	}

}
