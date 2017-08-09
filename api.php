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
$mongo = new MongoDB\Client("mongodb://localhost:27017");


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

		case 'page':
			$page_id = filter_hex($_GET['page_id']);
			$collection = $mongo->memoryatlas->pages;
			$result = $collection->findOne(['page_id' =>  $page_id]);
			succeed($result);
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

