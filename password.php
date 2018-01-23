<?php

include_once '_top.php';

// redirect if not logged in
if (!isset($_SESSION['user'])) {
	header('Location: /login.php');
}

include_once 'db.php';

if (isset($_POST['old_password'])) {

	if(strlen($_POST['new_password']) < 5) {
		echo "<p class='error'>New password must be at least 5 characters</p>";
	}	

	else {
		$result = get_user($_SESSION['user']['email']);

		if (password_verify($_POST['old_password'], $result->password_hash)) {
			$result = update_password_hash($_SESSION['user']['email'], password_hash($_POST['new_password'], PASSWORD_DEFAULT));
			if ($result == 1) {
				echo "<p class='success'>Password succesfully changed</p>";
			}
			else {
				echo "<p class='error'>Could not change password</p>";
			}
		}
		else {
			echo "<p class='error'>Old password not correct</p>";
		}
	}

}

?>

<h3>Change password</h3>

<form method="post">
	<label for="old_password">Old password</label>
	<input autofocus="autofocus" type="password" required="required" name="old_password" id="old_password" />
	<i title="Show password" class="unmask fa fa-eye" aria-hidden="true"></i>

	<label for="new_password">New password</label>
	<input type="password" required="required" name="new_password" id="new_password" />
	<i title="Show password" class="unmask fa fa-eye" aria-hidden="true"></i>

	<button>Change password</button>

	</form>

<script>
function hoverIn(e) { e.target.previousSibling.previousSibling.type = "text"; }
function hoverOut(e) { e.target.previousSibling.previousSibling.type = "password"; }
$('input[type="password"] + .unmask').hover(hoverIn, hoverOut);
</script>

<?php include_once('_bottom.php'); ?>