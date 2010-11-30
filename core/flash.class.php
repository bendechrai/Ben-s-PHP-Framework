<?php

class Flash {

	const MESSAGE_INFO = 'info';
	const MESSAGE_SUCCESS = 'success';
	const MESSAGE_WARNING = 'warning';

	static private $loaded = false;
	static private $autorotate = true;
	static private $rotateddata = null;

	static private function load() {
		if( !self::$loaded && self::$autorotate ) {
			if( !isset( $_SESSION['flash'] ) ) $_SESSION['flash'] = array();
			if( !isset( $_SESSION['flash']['previous'] ) ) $_SESSION['flash']['previous'] = array();
			if( !isset( $_SESSION['flash']['current'] ) ) $_SESSION['flash']['current'] = array();
			self::$rotateddata = $_SESSION['flash']['previous'];
			$_SESSION['flash']['previous'] = $_SESSION['flash']['current'];
			$_SESSION['flash']['current'] = array( '__ps_slug' => md5( rand() . time() ), '__ps_messages' => array( FLASH::MESSAGE_INFO=>array(), FLASH::MESSAGE_SUCCESS=>array(), FLASH::MESSAGE_WARNING=>array() ) );
		}
		self::$loaded = true;
	}

	static public function DisableAutoRotate() {
		self::$autorotate = false;
		// If loaded already, undo the rotation
		if( self::$loaded ) {
			$_SESSION['flash']['current'] = $_SESSION['flash']['previous'];
			$_SESSION['flash']['previous'] = self::$rotateddata;
		}
	}

	static public function Get( $key ) {
		self::load();
		if( isset( $_SESSION['flash']['previous'][$key] ) ) return $_SESSION['flash']['previous'][$key];
		return false;
	}

	static public function CanGet( $key ) {
		self::load();
		if( isset( $_SESSION['flash']['previous'][$key] ) ) return true;
		return false;
	}

	static public function Set( $key, $value ) {
		self::load();
		$_SESSION['flash']['current'][$key] = $value;
	}

	static public function Slug() {
		self::load();
		return $_SESSION['flash']['current']['__ps_slug'];
	}

	static public function CheckSlug( $slug ) {
		self::load();
		return $slug == $_SESSION['flash']['previous']['__ps_slug'];
	}

	static public function AddMessage( $message, $type ) {
		self::load();
		$_SESSION['flash']['current']['__ps_messages'][$type][] = $message;
	}

	static public function InstantAddMessage( $message, $type ) {
		self::load();
		$_SESSION['flash']['previous']['__ps_messages'][$type][] = $message;
	}

	static public function GetMessages( $type ) {
		self::load();
		if( isset( $_SESSION['flash']['previous']['__ps_messages'] ) && isset(  $_SESSION['flash']['previous']['__ps_messages'][$type] ) )
			return $_SESSION['flash']['previous']['__ps_messages'][$type];
	}

}
