<?php

include_once '_top.php';

// setup mongodb document database
require_once '../vendor/autoload.php';
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);

if (isset($_POST['email'])) {

	// retrieve user account with given email address
	$filter = ['email' => $_POST['email']];
	$options = ['limit' => 1];
	$query = new MongoDB\Driver\Query($filter, $options);
	$cursor = $mongo->executeQuery('memoryatlas.users', $query, $readPreference);

	foreach($cursor AS $doc) {	// need better way to get first object from cursor

		if (password_verify($_POST['password'], $doc->password_hash)) {
			$_SESSION['user']['email'] = $_POST['email'];
			header('Location: /alpha/home.php');
		}
	}

}

?>

<form method="post">
	<label for="email">Email address</label>
	<input type="email" autofocus="autofocus" name="email" id="email" />
	<label for="password">Password</label>
	<input type="password" name="password" id="password" />
	
	<button>Login</button>

	</form>


<?php include_once('_bottom.php'); ?>