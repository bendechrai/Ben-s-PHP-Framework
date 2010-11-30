<?php

class Email extends ActiveRecord {

	protected $__tablename = 'email';


	public function send() {

		parent::save();

		$headers = array();
		$headers[] = "From: {$this->getSender()}";
		$headers[] = "Reply-to: {$this->getReplyTo()}";
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-Type: multipart/alternative;'."\n".'             boundary="==Multipart_Boundary_xc75j85x"';

		$body = <<<EOEMAIL
This is a multi-part message in MIME format.

--==Multipart_Boundary_xc75j85x
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

{$this->getPlainText()}

--==Multipart_Boundary_xc75j85x
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

{$this->getBody()}

--==Multipart_Boundary_xc75j85x--
EOEMAIL;


		if( !mail( $this->getRecipient(), $this->getSubject(), $body, implode( "\n", $headers ) ) ) {
			AuditLog::Log( 'Could not send email', $this );
			return false;
		}

		// Email sent - update DB
		$this->setSentDate( date( 'Y-m-d H:i:s' ) );
		$saved = parent::save();

		AuditLog::Log( 'Email sent', $this );

		return $saved;

	}

	private function getPlainText() {
		$html2text = new Html2Text( $this->getBody() );
		return $html2text->get_text();


		$plaintext = $this->getBody();

		// Remove HTML
		$plaintext = strip_tags( $plaintext );

		// Remove whitespace on either side of a new line
		$plaintext = trim( preg_replace( '#[ \t]+\n#', "\n", $plaintext ) );
		$plaintext = trim( preg_replace( '#\n[ \t]+#', "\n", $plaintext ) );

		// Compact any multitabs or spaces into one space
		$plaintext = trim( preg_replace( '#[ \t]+#', ' ', $plaintext ) );

		// Convert any occurances of 3 or more new lines to 2 new lines
		$plaintext = preg_replace( '#\n\n+#', "\n\n", $plaintext );

		// Wrap
		$plaintext = wordwrap( $plaintext );

		return $plaintext;

	}

}
