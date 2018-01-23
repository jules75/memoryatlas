<?php

include_once '_top.php';

// redirect if logged in
if (isset($_SESSION['user'])) {
	header('Location: /login.php');
}

include_once 'db.php';

$email = $_POST['email'];
$username = htmlspecialchars($_POST['username']);
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];

if (isset($email)) {

	if(get_user($email)) {
		echo '<p class="error">Account with that email address already exists</p>';
	}
	else if(get_user_by_username($username)) {
		echo '<p class="error">Account with that username already exists</p>';
	}
	else if(strlen($password1) < 5) {
		echo "<p class='error'>Password must be at least 5 characters</p>";
	}
	else if($password1 !== $password2) {
		echo "<p class='error'>Passwords must match</p>";
	}
	else if(strlen($username) < 4 || strlen($username) > 20) {
		echo "<p class='error'>Username must be between 4 and 20 characters</p>";
	}
	else {
		insert_user($email, $username, password_hash($password1, PASSWORD_DEFAULT));
		echo "<p>Your account has been created.</p><p>Please <a href='login.php'>log in</a>.</p>";
		die();
	}
}

?>

<h3>Sign up</h3>

<form method="post">
	
	<label for="email">Email address</label>
	<input autofocus="autofocus" type="email" required="required" name="email" id="email" value="<?php echo $email ?>"/>

	<label for="username">Username (4-20 characters, used to identify your content to visitors)</label>
	<input type="text" required="required" name="username" id="username" value="<?php echo $username ?>" /> 

	<label for="password1">Password</label>
	<input type="password" required="required" name="password1" id="password1" />
	<i title="Show password" class="unmask fa fa-eye" aria-hidden="true"></i>

	<label for="password2">Confirm password</label>
	<input type="password" required="required" name="password2" id="password2" />
	<i title="Show password" class="unmask fa fa-eye" aria-hidden="true"></i>

	<button>Create account</button>

	</form>

<script>
function hoverIn(e) { e.target.previousSibling.previousSibling.type = "text"; }
function hoverOut(e) { e.target.previousSibling.previousSibling.type = "password"; }
$('input[type="password"] + .unmask').hover(hoverIn, hoverOut);
</script>

<?php include_once('_bottom.php'); ?>