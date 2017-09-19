<?php

require_once '../../config.php';
require_once '../../db.php';

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


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $entry_id = filter_hex($_GET['id']);

    $entry = get_entry($entry_id);
    if ($entry) {
        succeed(['data' => $entry]);
    }
    fail("No entry found for id $entry_id");
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $entry_id = filter_hex($_GET['id']);
    $entry = get_entry($entry_id);

    if ($entry) {

        unset($entry->_id); // strip ID to avoid duplicate error

        $entry->hidden = true;

        // user must be logged in to save
        if (isset($_SESSION['user'])) {
            $entry->user->id = $_SESSION['user']['id'];                
            $result = insert_entry([$entry]);
            succeed("Entry marked as hidden");
        }

        else {
            fail("You must be logged in to delete this entry");
        }

    }
    
    else {
        fail("No entry found for id $entry_id");
    }

}
