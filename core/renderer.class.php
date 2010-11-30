<?php

class Renderer {

	static public function Render( $controller ) {
		$content = ob_get_contents();
		ob_clean();
		include( TEMPLATE_DIR . 'top.php' );
		echo $content;
		include( TEMPLATE_DIR . 'bottom.php' );
		exit;
	}

}
