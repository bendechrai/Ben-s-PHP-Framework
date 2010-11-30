<div id="login-index">

	<h2>Login</h2>

	<form action="/login/login/" method="post">
		<?php echo FORM::Slug(); ?>
		<p><?php echo FORM::TextInputHtml( 'Email Address', 'email', 'email', 'required', '', true ) ?></p>
		<p><?php echo FORM::PasswordInputHtml( 'Password', 'password', 'password', 'required', '', true ) ?></p>
		<p><?php echo FORM::SubmitButtonHtml( 'Login', true ) ?></p>
	</form>

	<h2>Password Reset</h2>

	<form action="/login/reset/" method="post">
		<?php echo FORM::Slug(); ?>
		<p><?php echo FORM::TextInputHtml( 'Email Address', 'reminder-email', 'email', 'required', '', true ) ?></p>
		<p><?php echo FORM::SubmitButtonHtml( 'Reset Password', true ) ?></p>
	</form>

</div>
