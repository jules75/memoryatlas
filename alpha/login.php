<?php

include_once '_top.php';

// redirect if already logged in
if (isset($_SESSION['user'])) {
	header('Location: /alpha/home.php');
}

require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');



function under_ten_minutes_old($timestamp) {
	return (time() - $timestamp) < (60 * 10);
}


// login by token (sent by recovery email)
if (isset($_GET['email']) && isset($_GET['token'])) {
	
	$result = get_user($_GET['email']);

	if (($_GET['token'] == $result->login_token) && under_ten_minutes_old($result->login_token_created)) {

		$_SESSION['user']['id'] = (String)$result->_id;
		$_SESSION['user']['email'] = $_GET['email'];
		$_SESSION['user']['username'] = (String)$result->username;

		$new_password = generate_token();
		update_password_hash($_GET['email'], password_hash($new_password, PASSWORD_DEFAULT));

		echo "<p>You are now logged in.</p>";
		echo "<p>Your password has been changed to <code>$new_password</code></p>";
		echo "<p>Please copy it and <a href='password.php'>change it</a> to something you'll remember.</p>";
		die();
	}

	echo "<p class='error'>Could not log you in</p>";
}

else if (isset($_POST['email'])) {

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

<p><a href="signup.php">Sign up for an account</a></p>

<p><a href="recover.php">Forgotten your password?</a></p>

<?php include_once('_bottom.php'); ?>