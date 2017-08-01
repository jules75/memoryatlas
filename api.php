<?php

/*
	Entry point for API calls.
*/


require_once 'config.php';

require_once 'lib/3rdparty/cloudinary/Cloudinary.php';
require_once 'lib/3rdparty/cloudinary/Uploader.php';
require_once 'lib/3rdparty/cloudinary/Api.php';


\Cloudinary::config(MEMORY_ATLAS_CONFIG['cloudinary']);


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

if ($_SERVER['REQUEST_METHOD'] == "GET") {
	
	switch($_GET['action']) {

		case 'hello':
			succeed(['response'=>'hi there!']);
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

