<?php

date_default_timezone_set('Australia/Melbourne');

// If this is not being called from the CLI
if( !isset( $_SERVER['SHELL'] ) || $_SERVER['SHELL'] == '' ) {

	// check apache environment variables are in
	if( !isset( $_SERVER['MYSQL_HOST'] ) || $_SERVER['MYSQL_HOST'] == '' ||
    	!isset( $_SERVER['MYSQL_NAME'] ) || $_SERVER['MYSQL_NAME'] == '' ||
    	!isset( $_SERVER['MYSQL_USER'] ) || $_SERVER['MYSQL_USER'] == '' ||
    	!isset( $_SERVER['MYSQL_PASS'] ) || $_SERVER['MYSQL_PASS'] == '' ) {
		user_error( 'Apache misconfiguration: missing environment settings MYSQL_HOST, MYSQL_NAME, MYSQL_USER and/or MYSQL_PASS', E_USER_ERROR );
	}

	session_start();

	ob_start();

	DEFINE( 'TEMPLATE_DIR', dirname( __FILE__ ) . '/templates/' . Client::GetTemplate() . '/' );
	DEFINE( 'RESOURCE_URL', '/templates/' . Client::GetTemplate() . '/resources/' );
	DEFINE( 'CAMPAIGN_IMAGE_DIR', dirname( dirname( __FILE__ ) ) . '/images/' );

}

function __autoload( $class ) {

	$lowerclass = strtolower( $class );

	// If core class
	if( file_exists( dirname( __FILE__ ) . '/core/' . $lowerclass . '.class.php' ) ) {
		require_once( dirname( __FILE__ ) . '/core/' . $lowerclass . '.class.php' );
		return;
	}

	// If Controller
	if( preg_match( '#(.+)(Controller)#', $class, $matches ) ) {
		require_once( dirname( __FILE__ ) . '/controllers/' . strtolower( $matches[1] ) . '.class.php' );
	} else {
		// Load model
		require_once( dirname( __FILE__ ) . '/models/' . $lowerclass . '.class.php' );
	}

}
