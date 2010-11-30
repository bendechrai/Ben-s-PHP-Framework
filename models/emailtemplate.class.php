<?php

class EmailTemplate extends ActiveRecord {
	protected $__tablename = 'emailtemplate';

	public function render( $data ) {

		if( !isset( $data['campaign'] ) && !( $data['campaign'] instanceof Campaign ) ) {
			AuditLog::Log( 'EmailTemplate->render called without campaign in data array' );
			return false;
		}

		$contents = $this->getBody();

		// Replace [image_location]
		$contents = str_replace( '[image_location]', Client::GetUrl() . 'images/' . $data['campaign']->getId() . '/', $contents );

		// If there are any placeholders such as [object->attribute]
		if( preg_match_all( '#\[([^\]]+)\]#', $contents, $placeholders ) ) {

			// get unique placeholders
			$placeholders = array_flip( array_flip( $placeholders[1] ) );

			foreach( $placeholders as $placeholder ) {

				// Split object and attribute out
				list( $object, $attribute ) = explode( '->', strtolower( $placeholder ), 2 );

				// Has object been provided?
				if( isset( $data[$object] ) ) {

					$value = '';

					// Is this an array and does the array have this attribute?
					if( is_array( $data[$object] ) && isset( $data[$object][$attribute] ) ) {

						$value = $data[$object][$attribute];

					// Is this an object and does it have this attribute?
					} else if( is_object( $data[$object] ) ) {

						// sanitise attribute
						$attribute = 'get' . DBH::CleanField( $attribute );
						$value = $data[$object]->$attribute();
						if( !$value ) $value = '';

					}

					// Replace all occurances of this placeholder with the value
					$contents = str_ireplace( '[' . $placeholder . ']', $value, $contents );
				}

			}

			return $contents;

		}

	}
}
