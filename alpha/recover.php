<?php

require_once '_top.php';

require_once '../db.php';
require_once '../lib/email.php';

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');


if (isset($_POST['email'])) {
	$mailgun_key = MEMORY_ATLAS_CONFIG['mailgun']['key'];	
	$email = htmlspecialchars($_POST['email']);
	$token = generate_token();
	$result = update_login_token($email, $token);
	if ($result) {
		send_recovery_email($email, $token, $mailgun_key);
		echo "A login link has been sent to $email. The link expires in 10 minutes.";
	}
	else {
		echo 'Error: could not generate token for this account';
	}
	die();
}

?>

<p>Forgotten your password? We can email you a link to log you in and change your password.</p>

<form method="post">
	<label for="email">Your email address</label>
	<input type="email" required="required" autofocus="autofocus" name="email" id="email" />
	<button>Email me a login link</button>
	</form>


<?php include_once('_bottom.php'); ?>