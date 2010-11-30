<?php

abstract class ActiveRecord {

	protected $data = array();
	private $errorInfo = array();

	public function __construct( $id=null ) {
		if( !is_null( $id ) ) $this->load( $id );
	}

	public function load( $id ) {
		return( $this->loadBy( 'id', $id ) );
	}

	public function loadBy( $field, $value ) {
		$dbh = DBH::GetInstance();
		$sql = 'SELECT * FROM ' . $this->__tablename . ' WHERE ' . DBH::CleanField( $field ) . '=:value';
		$sth = $dbh->prepare( $sql );
		$sth->bindParam( ':value', $value );
		$sth->execute();
		$result = $sth->fetch( PDO::FETCH_ASSOC );
		$sth->closeCursor();
		if( $result ) $this->populate( $result );
		return $result ? true : false;
	}

	public function delete() {
		// If id, then delete
		if( isset( $this->data['id'] ) && $this->data['id'] != '' ) {
			$dbh = DBH::GetInstance();
			$sql = 'DELETE FROM ' . $this->__tablename . ' WHERE id=' . intval( $this->getId() );
			$sth = $dbh->prepare( $sql );
			$result = $sth->execute();
			$sth->closeCursor();
			return $result;
		}
	}

	public function save() {

		$dbh = DBH::GetInstance();

		// If no id, then new
		if( !isset( $this->data['id'] ) || $this->data['id'] == '' ) {
			$sql = 'INSERT INTO ' . $this->__tablename . ' ( ' . implode( ', ', array_keys( $this->data ) ) . ' ) VALUES ( :' . implode( ', :', array_keys( $this->data ) ) . ' )';
		} else {
			$sql = 'UPDATE ' . $this->__tablename . ' SET ';
			foreach( array_keys( $this->data ) as $key ) {
				if( $key == 'id' ) continue;
				$sql .= $key . '=:' . $key . ', ';
			}

			// There's an extra ", " at the end of the string - remove it!
			$sql = preg_replace( '#, $#', '', $sql );
			$sql .= ' WHERE id=' . intval( $this->getId() );
		}

		$sth = $dbh->prepare( $sql );
		if( !( $sth instanceof PDOStatement ) ) {
			AuditLog::Error( 'Call to a member function bindValue() on a non-object: ' . $sql );
		}
		foreach( $this->data as $key=>$datum ) {
			if( $key == 'id' ) continue;
			$sth->bindValue( ':' . $key, $datum );
		}

		if( $sth->execute() ) {
			// If no ID, set it
			if( !isset( $this->data['id'] ) || $this->data['id'] == '' ) $this->setId( $dbh->lastInsertId() );
			return true;
		} else {
			$this->errorInfo = $sth->errorInfo();
			return false;
		}

	}

	public function getTablename() {
		return $this->__tablename;
	}

	public function populate( $data ) {
		$this->data = $data;
	}

	public function __call( $method, $params ) {

		// Getter
		if( preg_match( '#^get(.*)$#', $method, $matches ) ) {
			$attribute = strtolower( $matches[1] );
			if( isset( $this->data[$attribute] ) ) return $this->data[$attribute];
			return false;
		}

		// Setter
		if( preg_match( '#^set(.*)$#', $method, $matches ) ) {
			$attribute = strtolower( $matches[1] );
			$this->data[$attribute] = $params[0];
			return true;
		}

		AuditLog::Error( 'Invalid call to ' . $method . '()', $this );
	}

	public function getErrorInfo() {
		if( preg_match( "#^Duplicate entry '([^']+)' for key (.+)$#", $this->errorInfo[2], $matches ) ) {
			return "Duplicate '{$matches[1]}' for " . $this->getFieldName( $matches[2] );;
		} else {
			AuditLog::Log( 'Uncaught error info', $this );
			return false;
		}
	}

	private function getFieldName( $i ) {
		$select = DBH::GetInstance()->query( 'SELECT * FROM ' . $this->__tablename );
		$meta = $select->getColumnMeta( $i );
		return $meta['name'];
	}

}
