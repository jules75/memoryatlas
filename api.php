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

// database calls
require_once 'db.php';

require_once 'api/fns.php';

// sessions required for user authentication
session_start();



if ($_SERVER['REQUEST_METHOD'] == "GET") {
    switch ($_GET['action']) {
        
        // Return all revisions of single entry, newest first
        case 'history':
            $entry_id = filter_hex($_GET['entry_id']);
            succeed(get_entry_history($entry_id));
            break;

        default:
            fail("Unknown GET action '$_GET[action]'");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['action']) {
        case 'image':
            $result = \Cloudinary\Uploader::upload($_FILES['upload']['tmp_name']);
            succeed(['image_url'=>$result['secure_url']]);
            break;

        case 'save':
            
			// user must be logged in to save
            if (isset($_SESSION['user'])) {
				$_POST['payload']['user']['id'] = $_SESSION['user']['id'];                
				succeed(insert_entry([$_POST['payload']]));
            }

			else {
				fail("You must be logged in to save changes");
			}

            break;

        default:
            fail("Unknown POST action '$_POST[action]'");
    }
}
