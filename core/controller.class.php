<?php

abstract class Controller {

	protected $frontcontroller = null;
	protected $request = null;

	public function __construct( $frontcontroller ) {

		// Flash object should not rotate if this is a popup
		if( isset( $_GET['popup'] ) && $_GET['popup'] === '1' ) Flash::DisableAutoRotate();

		$this->frontcontroller = $frontcontroller;
		$this->request = $frontcontroller->getRequest();;

		// If this is not the logon controller and the user is unset or invalid
		if( !( $this instanceof LoginController ) && ( !isset( $_SESSION['user'] ) || !( $_SESSION['user'] instanceof User ) ) ) {
			header( 'location: /login/' );
			exit;
		}
	}

	public function getFrontController() {
		return $this->frontcontroller;
	}

}
