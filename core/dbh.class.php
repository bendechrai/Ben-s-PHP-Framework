<?php

class DBH {

	static private $dbh = null;

	static public function getInstance() {
		if( is_null( self::$dbh ) ) {
			self::$dbh = new PDO( "mysql:host={$_SERVER['MYSQL_HOST']};dbname={$_SERVER['MYSQL_NAME']}", $_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PASS'] );
		}
		return self::$dbh;
	}

	static public function CleanField( $field ) {
		return preg_replace( '#[^a-z0-9_]#', '', $field );
	}

}
