<?php

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

