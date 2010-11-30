<?php

class Collection implements Iterator
{

	const CONDITION_EQUALS = 1;
	const CONDITION_IN = 2;
	const CONDITION_NULL = 3;
	const CONDITION_NOTNULL = 4;

	const DIRECTION_ASC = 1;
	const DIRECTION_DESC = 2;

	private $var = array();
	private $modelInstance = null;
	private $modelClassName = '';
	private $conditions = array();
	private $orderby = array();
	private $limit = null;

	private $execConditionFields = array();
	private $execConditionValues = array();
	private $execOrderBy = array();

	public function __construct( $modelInstance ) {
		$this->modelInstance = $modelInstance;
		$this->modelClassName = get_class( $this->modelInstance );
	}

	public function getItemInstance() {
		$className = $this->modelClassName;
		return new $className();
	}

	public function rewind() {
		reset($this->var);
	}

	public function current() {
		$var = current($this->var);
		return $var;
	}

	public function key() {
		$var = key($this->var);
		return $var;
	}

	public function next() {
		$var = next($this->var);
		return $var;
	}

	public function valid() {
		$var = $this->current() !== false;
		return $var;
	}

	public function add($value) {
		$this->var[] = $value;
	}

	public function truncate() {
		$this->var = array();
	}

	public function length() {
		return count( $this->var );
	}

	public function getBy( $field, $value ) {
		$method = 'get' . DBH::CleanField( $field );
		foreach( $this as $item ) {
			if( $item->$method() == $value ) return $item;
		}
		return false;
	}

	public function execute() {

		$this->truncate();

		$dbh = DBH::getInstance();
		$sql = 'SELECT * FROM ' . $this->modelInstance->getTablename();

		$this->prepare();
		if( count( $this->execConditionFields ) > 0 ) $sql .= ' WHERE (' . implode( ') AND (', $this->execConditionFields ) . ')';
		if( count( $this->execOrderBy ) > 0 ) $sql .= ' ORDER BY ' . implode( ', ', $this->execOrderBy );
		if( !is_null( $this->limit ) ) $sql .= ' LIMIT ' . $this->limit;

		$sth = $dbh->prepare( $sql );
		if( !( $sth instanceof PDOStatement ) ) {
			AuditLog::Error( 'Call to a member function bindValue() on a non-object: ' . $sql );
		}
		foreach( $this->execConditionValues as $field=>$value ) {
			$sth->bindValue( $field, $value );
		}

		$sth->execute();
		$results = $sth->fetchAll( PDO::FETCH_ASSOC );

		$sth->closeCursor();

		foreach( $results as $result ) {
			$tmp = new $this->modelClassName();
			$tmp->populate( $result );
			$this->add( $tmp );
		}

	}

	/**
	 * Gets a count of the number of results without pulling out all the data. Ignores any limit set.
	 * @access public
	 * @return Integer The number of records
	 */
	public function getCount() {
		$dbh = DBH::getInstance();
		$sql = 'SELECT COUNT(*) FROM ' . $this->modelInstance->getTablename();

		$this->prepare();
		if( count( $this->execConditionFields ) > 0 ) $sql .= ' WHERE (' . implode( ') AND (', $this->execConditionFields ) . ')';

		$sth = $dbh->prepare( $sql );
		if( !( $sth instanceof PDOStatement ) ) {
			AuditLog::Error( 'Call to a member function bindValue() on a non-object: ' . $sql );
		}
		foreach( $this->execConditionValues as $field=>$value ) {
			$sth->bindValue( $field, $value );
		}

		$sth->execute();
		$results = $sth->fetch( PDO::FETCH_NUM );
		$sth->closeCursor();
		return $results[0];

	}

	public function addCondition( $field, $condition_type, $value=null ) {
		$this->conditions[] = array( 'field'=>$field, 'type'=>$condition_type, 'value'=>$value );
	}

	public function orderBy( $field, $direction=null ) {
		if( is_null( $direction ) ) $direction = self::DIRECTION_ASC;
		$this->orderby[] = array( 'field'=>$field, 'direction'=>$direction );
	}

	public function setLimit( $limit ) {
		if( intval( $limit ) > 0 ) {
			$this->limit = intval( $limit );
		} else {
			$this->limit = null;
		}
	}

	/**
	 * Prepares the condition fields and values before any execution is required
	 * @access private
	 * @return void
	 */
	public function prepare() {

		// reset
		$this->execConditionFields = array();
		$this->execConditionValues = array();
		$this->execOrderBy = array();
		$conditionnumber = 0;

		foreach( $this->conditions as $condition ) {

			switch( $condition['type'] ) {

				case self::CONDITION_EQUALS:
					$this->execConditionFields[] = DBH::CleanField( $condition['field'] ) . '=:condition' . $conditionnumber;
					$this->execConditionValues[':condition' . $conditionnumber] = $condition['value'];
					++$conditionnumber;
					break;

				case self::CONDITION_IN:

					// Get get a field name and an array of possible values
					// The sql gerenated needs to be "field IN (:paramX, :paramY, :paramZ)"
					// One param needs to be added for each value

					$namedvars = array();
					foreach( $condition['value'] as $value ) {
						$namedvars[] = ':condition' . $conditionnumber;
						$this->execConditionValues[':condition' . $conditionnumber] = $value;
						++$conditionnumber;
					}

					// Complete the sql
					$this->execConditionFields[] = DBH::CleanField( $condition['field'] ) . ' in (' . implode( ', ', $namedvars ) . ')';

					break;

				case self::CONDITION_NULL:
					$this->execConditionFields[] = DBH::CleanField( $condition['field'] ) . ' IS NULL';
					break;

				case self::CONDITION_NOTNULL:
					$this->execConditionFields[] = DBH::CleanField( $condition['field'] ) . ' IS NOT NULL';
					break;

				/* If adding to this list, remember to increment $conditionnumber where required */
			}
		}

		// Order by?
		foreach( $this->orderby as $orderitem ) {
			switch( $orderitem['direction'] ) {
				case self::DIRECTION_ASC:
					$this->execOrderBy[] = DBH::CleanField( $orderitem['field'] ) . ' ASC';
					break;
				case self::DIRECTION_DESC:
					$this->execOrderBy[] = DBH::CleanField( $orderitem['field'] ) . ' DESC';
					break;
			}
		}

	}

	/**
	 * Extracts other objects as defined by a foreign key
	 * @access public
	 * @param Object $object The object to extract
	 * @param String $fk The foreing key field
	 * @param String $field The lookup field name (default 'id')
	 * @return Collection A collection of extracted objects
	 */
	public function extract( $object, $fk, $field='id' ) {

		// Create a new result collection
		$results = new Collection( $object );

		// get the class name of the objects we're extracting
		$objectClass = get_class( $object );

		// create an empty array to store the FKs extracted to ensure results collection only conatins unique objects
		$resultshash = array();

		// Define the method to use to get the FK value
		$method = 'get' . DBH::CleanField( $fk );

		// Loop through each item in this collection
		foreach( $this as $item ) {

			// If the FK value is not in the resultshash
			if( !in_array( $item->$method(), $resultshash ) ) {

				// Try to load the foreigh object
				$newitem = new $objectClass();
				if( $newitem->loadBy( $field, $item->$method() ) ) {

					// Success - add it to the results collection and store fk value for deduping purposes
					$results->add( $newitem );
					$resultshash[] = $item->$method();
				}
			}
		}

		return $results;

	}

}
