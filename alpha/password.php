<?php

include_once '_top.php';

// redirect if not logged in
if (!isset($_SESSION['user'])) {
	header('Location: /alpha/login.php');
}

// setup mongodb document database
require_once '../vendor/autoload.php';
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);

if (isset($_POST['old_password'])) {

	if(strlen($_POST['new_password']) < 5) {
		echo "<p class='error'>New password must be at least 5 characters</p>";
	}	

	else {

		// retrieve user account with given email address
		$filter = ['email' => $_SESSION['user']['email']];
		$options = ['limit' => 1];
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $mongo->executeQuery('memoryatlas.users', $query, $readPreference);

		foreach($cursor AS $doc) {	// need better way to get first object from cursor

			if (password_verify($_POST['old_password'], $doc->password_hash)) {

				$bulk = new MongoDB\Driver\BulkWrite;
				$bulk->update(
					['email' => $_SESSION['user']['email']],
					['$set' => ['password_hash' => password_hash($_POST['new_password'], PASSWORD_DEFAULT)]]
					);

				$result = $mongo->executeBulkWrite('memoryatlas.users', $bulk);
				if ($result->getModifiedCount() == 1) {
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