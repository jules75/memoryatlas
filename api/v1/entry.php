<?php

require_once '../../config.php';
require_once '../../db.php';


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

$entry = get_entry($entry_id);
if ($entry) {
    succeed(['data' => $entry]);
}
fail("No entry found for id $entry_id");
