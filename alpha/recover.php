<?php

include_once '_top.php';

include_once '../db.php';

if (isset($_POST['email'])) {
	$token = generate_token();
	$result = update_login_token($_POST['email'], $token);
	if ($result) {
		echo 'y';
	}
	else {
		echo 'n';
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