<?php

include_once '_top.php';

// redirect if already logged in
if (isset($_SESSION['user'])) {
	header('Location: /alpha/home.php');
}

require_once '../db.php';

if (isset($_POST['email'])) {

	$result = get_user($_POST['email']);

	if (password_verify($_POST['password'], $result->password_hash)) {
		$_SESSION['user']['id'] = (String)$result->_id;
		$_SESSION['user']['email'] = $_POST['email'];
		$_SESSION['user']['username'] = (String)$result->username;
		header('Location: /alpha/home.php');
	}

	echo "<p class='error'>Could not log you in</p>";
}

?>

<form method="post">
	<label for="email">Email address</label>
	<input type="email" required="required" autofocus="autofocus" name="email" id="email" />
	<label for="password">Password</label>
	<input type="password" required="required" name="password" id="password" />
	
	<button>Login</button>

	</form>


<?php include_once('_bottom.php'); ?>