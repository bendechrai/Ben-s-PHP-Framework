<?php

class Client {

	static private $cache;

	static private function load() {

		if( is_null( self::$cache ) ) self::$cache = array();

		if( !isset( self::$cache[$_SERVER['HTTP_HOST']] ) ) {
			switch( $_SERVER['HTTP_HOST'] ) {
				case 'test.localhost':
					self::$cache[$_SERVER['HTTP_HOST']] = array (
						'id' => 1,
						'name' => 'No Name',
						'template' => 'default',
						'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/'
					);
					break;
				default:
					die( 'Site not found: ' . $_SERVER['HOST_NAME'] );
			}
		}
	}

	static private function Get( $attribute ) {
		self::load();
		return self::$cache[$_SERVER['HTTP_HOST']][$attribute];
	}

	static public function GetId() {
		return self::Get( 'id' );
	}

	static public function GetName() {
		return self::Get( 'name' );
	}

	static public function GetTemplate() {
		return self::Get( 'template' );
	}

	static public function GetUrl() {
		return self::Get( 'url' );
	}

}
