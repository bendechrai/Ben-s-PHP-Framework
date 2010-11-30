<?php

class Form {

	static public function Slug() {
		return '<input type="hidden" name="slug" value="' . Flash::Slug() . '"/>';
	}

	static public function TextInputHtml( $label, $id, $name, $class, $value, $editable=true ) {
		if( $editable ) {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label><input type="text" class="' . trim( 'text ' . $class ) . '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars( $value ) . '"/>';
		} else {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label>' . htmlspecialchars( $value ) . '';
		}
	}

	static public function PasswordInputHtml( $label, $id, $name, $class, $value, $editable=true ) {
		if( $editable ) {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label><input type="password" class="' . trim( 'text ' . $class ) . '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars( $value ) . '"/>';
		} else {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label>********';
		}
	}

	static public function TextAreaHtml( $label, $id, $name, $class, $value, $editable=true ) {
		if( $editable ) {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label><textarea class="' . trim( 'text ' . $class ) . '" name="' . $name . '" id="' . $id . '">' . htmlspecialchars( $value ) . '</textarea>';
		} else {
			return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label>' . htmlspecialchars( $value ) . '';
		}
	}

	static public function SelectHtml( $label, $id, $name, $class, $collection, $valueField, $textField, $value, $firstValue=null, $firstText=null, $editable=true ) {

		$valueMethod = 'get' . $valueField;
		$textMethod = 'get' . $textField;

		if( $editable ) {

			$html = '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label><select class="' . trim( $class ) . '" name="' . $name . '" id="' . $id . '">';
			if( !is_null( $firstValue ) && !is_null( $firstText ) ) {
				$html .= '<option value="' . $firstValue . '">' . htmlspecialchars( $firstText ) . '</option>';
			}
			foreach( $collection as $item ) {
				$html .= '<option value="' . $item->$valueMethod() . '"';
				if( $value == $item->$valueMethod() ) $html .= ' selected="selected"';
				$html .= '>' . htmlspecialchars( $item->$textMethod() ) . '</option>';
			}
			$html .= '</select>';
			return $html;

		} else {

			$item = $collection->getItemInstance();
			if( $item->loadBy( $valueField, $value ) ) {
				return '<label for="' . $id . '">' . htmlspecialchars( $label ) . '</label>' . htmlspecialchars( $item->$textMethod() ) . '';
			}
			AuditLog::Error( 'Could not load object in Form::SelectHTML { class: ' . get_class( $item ) . ', valueField: ' . $valueField . ', value: ' . $value . ' }' );

		}
	}

	static public function InputCheckboxHtml( $label, $id, $name, $class, $value, $editable=true ) {
		return '<input type="checkbox" class="' . trim( 'checkbox ' . $class ) . '" name="' . $name . '" id="' . $id . '"' . ($value?' checked="checked"':'') . ( $editable ? '' : ' disabled="disabled"' ) . '/><label class="nofloat" for="' . $id . '">' . htmlspecialchars( $label ) . '</label>';
	}

	static public function SubmitButtonHtml( $value, $editable=true, $name='submit', $class='' ) {
		if( $editable ) {
			return '<label>&nbsp;</label><input type="submit" name="' . htmlspecialchars( $name ) . '" class="' . trim( "submit button $class" ) . '" value="' . htmlspecialchars( $value ) . '"/>';
		} else {
			return '';
		}
	}

}
