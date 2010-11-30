<?php

class UploadedFile {

	private $filearray = array();
	private $friendlyStatus = '';
	private $contents = null;
	private $csv = null;

	public function __construct( $filearray ) {
		$this->filearray = $filearray;

		// Process
		switch( $this->getError() ) {

			case UPLOAD_ERR_OK:
				$this->friendlyStatus = 'The file uploaded successfully.';
				break;

			case UPLOAD_ERR_INI_SIZE:
				AuditLog::Error( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', $this, $_SERVER );
				$this->friendlyStatus = 'The uploaded file ' . $this->getName() . ' exceeded ' . $this->GetMaxFileSize();
				break;

			case UPLOAD_ERR_FORM_SIZE:
				AuditLog::Error( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', $this, $_SERVER );
				$this->friendlyStatus = 'The uploaded file ' . $this->getName() . ' exceeded ' . $this->GetMaxFileSize();
				break;

			case UPLOAD_ERR_PARTIAL:
				$this->friendlyStatus = 'The file was only partially uploaded - please try again.';
				break;

			case UPLOAD_ERR_NO_FILE:
				$this->friendlyStatus = 'No file was uploaded - please try again.';
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				AuditLog::Error( 'File upload error: Missing a temporary folder!' );
				$this->friendlyStatus = 'An internal error occured while trying to upload the file. An administrator has been notified.';
				break;

			case UPLOAD_ERR_CANT_WRITE:
				AuditLog::Error( 'File upload error: Failed to write file to disk!' );
				$this->friendlyStatus = 'An internal error occured while trying to upload the file. An administrator has been notified.';
				break;

			case UPLOAD_ERR_EXTENSION:
				AuditLog::Error( 'File upload error: File upload stopped by extension!' );
				$this->friendlyStatus = 'An internal error occured while trying to upload the file. An administrator has been notified.';
				break;
		}
	}

	public function getError() {
		return $this->filearray['error'];
	}

	public function getName() {
		return $this->filearray['name'];
	}

	public function getType() {
		return $this->filearray['type'];
	}

	public function getSize() {
		return $this->filearray['size'];
	}

	public function getTmpName() {
		return $this->filearray['tmp_name'];
	}

	public function ok() {
		return( $this->getError() == UPLOAD_ERR_OK );
	}

	static public function GetMaxFileSize() {
		return max( $_POST['MAX_FILE_SIZE'], ini_get( 'upload_max_filesize' ) );
	}

	public function getFriendlyStatus() {
		return $this->friendlyStatus;
	}

	public function getContents() {
		if( is_null( $this->contents ) ) {
			$this->contents = file_get_contents( $this->getTmpName() );
		}
		return $this->contents;
	}

	public function getCSV() {
		if( is_null( $this->csv ) ) {
			$this->csv = array();
			$handle = fopen( $this->getTmpName(), 'r');
			while( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) {
				$this->csv[] = $data;
			}
			fclose($handle);
		}
		return $this->csv;
	}

}
