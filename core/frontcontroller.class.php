<?php

class FrontController {

	private $action = '';
	private $request = array();

	public static function CreateInstance() {
		$instance = new self();
		return $instance;
	}

	public function dispatch( $defaultPage='home', $defaultAction='index', $override=false ) {
		if( !$override ) {
			$request = explode( '/', trim( preg_replace( '#\??' . $_SERVER['QUERY_STRING'] . '$#', '', $_SERVER['REQUEST_URI'] ), '/' ) );
		} else {
			$request = array( $defaultPage, $defaultAction );
		}

		$page = !empty($request[0]) ? $request[0] : $defaultPage;
		$this->action = !empty($request[1]) ? $request[1] : $defaultAction;

		if( count( $request ) > 2 ) {
			$this->request = array_slice( $request, 2 );
		}

		$class = ucfirst($page) . 'Controller';
		$actionMethod = "do" . ucfirst($this->action);
		$controller = new $class( $this );
		$controller->$actionMethod();
		return( $controller );
	}

	public function getAction() {
		return $this->action;
	}

	public function getRequest() {
		return $this->request;
	}

}

