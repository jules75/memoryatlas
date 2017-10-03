<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

// sessions required for user authentication
session_start();


function entryCreator($entry_id) {
 
    $entries = get_entry_history($entry_id);
    $user_ids = [];
    foreach ($entries as $entry) {
        $user_ids[] = $entry->user->id;
    }

    return array_slice($user_ids, -1)[0];
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['id'])) {

        $entry_id = filter_hex($_GET['id']);

        $entry = get_entry($entry_id);
        if ($entry) {
            succeed(['data' => $entry]);
        }
        fail("No entry found for id $entry_id");
    }

    else {
        succeed(['data' => get_entries()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_SESSION['user'])) {
        $_POST['payload']['user']['id'] = $_SESSION['user']['id'];                
        succeed(insert_entry([$_POST['payload']]));
    }

    fail("You must be logged in to save changes");

}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $entry_id = filter_hex($_GET['id']);
    $entry = get_entry($entry_id);

    if ($entry) {

        unset($entry->_id); // strip ID to avoid duplicate error

        $entry->hidden = true;

        // user must be logged in to save
        if (isset($_SESSION['user'])) {

            if (entryCreator($entry_id) == $_SESSION['user']['id']) {
                $entry->user->id = $_SESSION['user']['id'];                
                $result = insert_entry([$entry]);
                succeed("Entry marked as hidden");
            }
            else {
                fail("You did not create this entry, you can't delete it");
            }
        }

        else {
            fail("You must be logged in to delete this entry");
        }

    }
    
    else {
        fail("No entry found for id $entry_id");
    }

}
