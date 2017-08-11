<?php

/*
	Entry point for API calls.
*/


require_once 'config.php';

// setup Cloudinary cloud image host
require_once 'lib/3rdparty/cloudinary/Cloudinary.php';
require_once 'lib/3rdparty/cloudinary/Uploader.php';
require_once 'lib/3rdparty/cloudinary/Api.php';
\Cloudinary::config(MEMORY_ATLAS_CONFIG['cloudinary']);

// setup mongodb document database
require_once 'vendor/autoload.php';
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);


function succeed($result) {
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($result);
	die();
}

function fail($message) {
	echo $message;
	http_response_code(400);
	die();
}

// Returns string with non hex characters removed
function filter_hex($string) {
	return preg_replace('/[^a-fA-F0-9]+/', '', $string);
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
	
	switch($_GET['action']) {

		// Return all revisions of single page, newest first
		case 'history':
			$page_id = filter_hex($_GET['page_id']);

			$filter = ['page_id' => $page_id];
			$options = ['sort' => ['_id' => -1]];
			$query = new MongoDB\Driver\Query($filter, $options);
			$cursor = $mongo->executeQuery('memoryatlas.pages', $query, $readPreference);

			$result = [];
			foreach($cursor AS $doc) {
				$result[] = $doc;
			}
			succeed($result);
			break;

		// Return all pages (newest revision)
		case 'list':
			$command = new MongoDB\Driver\Command([
				'aggregate' => 'pages',
				'pipeline' => [
					['$group' => ['_id' => '$page_id', 'revisions' => ['$sum' => 1]]],
					['$project' => ['_id' => 0, 'page_id' => '$_id', 'revisions' => 1]]
				]
			]);

			$cursor = $mongo->executeCommand('memoryatlas', $command);

			// Couldn't figure out how to get JUST the first doc from the cursor :-/
			foreach($cursor AS $doc) {
				succeed($doc);
				break;
			}
			break;

		// Return newest revision of single page
		case 'page':
			$page_id = filter_hex($_GET['page_id']);

			$filter = ['page_id' => $page_id];
			$options = ['sort' => ['_id' => -1], 'limit' => 1];
			$query = new MongoDB\Driver\Query($filter, $options);
			$cursor = $mongo->executeQuery('memoryatlas.pages', $query, $readPreference);

			// Couldn't figure out how to get JUST the first doc from the cursor :-/
			foreach($cursor AS $doc) {
				succeed($doc);
				break;
			}
			break;

		default:
			fail("Unknown GET action '$_GET[action]'");

	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	switch($_POST['action']) {

		case 'image':
			$result = \Cloudinary\Uploader::upload($_FILES['upload']['tmp_name']);
			succeed(['image_url'=>$result['secure_url']]);
			break;

		default:
			fail("Unknown POST action '$_POST[action]'");

	}
}

