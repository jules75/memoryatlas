<?php

require_once '../../../config.php';
require_once '../../../db.php';


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



$entry_id = filter_hex($_GET['id']);
$entries = get_entry_history($entry_id);
$contributors = [];

foreach ($entries as $entry) {
    $contributors[] = $entry->user->id;
}


succeed(['data' =>
    [
        ['creator' => ['user_id' => array_slice($contributors, -1)[0]]],
        ['contributors' => ['user_ids' => array_values(array_unique($contributors))]]
    ]]);
