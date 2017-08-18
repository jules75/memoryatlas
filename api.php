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

// sessions required for user authentication
session_start();


function succeed($result)
{
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($result);
    die();
}

function fail($message)
{
    echo $message;
    http_response_code(400);
    die();
}

// Returns string with non hex characters removed
function filter_hex($string)
{
    return preg_replace('/[^a-fA-F0-9]+/', '', $string);
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    switch ($_GET['action']) {
        
        // Return all revisions of single entry, newest first
        case 'history':
            $entry_id = filter_hex($_GET['entry_id']);
            succeed(get_entry_history($entry_id));
            break;

        // Return all entries (newest revision)
        case 'list':
            succeed(get_entries());
            break;

        // Return newest revision of single entry
        case 'entry':
            $entry_id = filter_hex($_GET['entry_id']);
            $entry = get_entry($entry_id);
            if ($entry) {
                succeed($entry);
            }
            fail("No entry found for id $entry_id");
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
