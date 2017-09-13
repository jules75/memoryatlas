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

function cloudinaryThumbnailUrl($imageUrl) {
    $pattern = '/upload\/(.*?)\//';
    $replacement = 'upload/w_200,h_300,c_thumb,g_auto/$1/';
    return preg_replace($pattern, $replacement, $imageUrl);
}

function firstImage($entry) {

    foreach ($entry->ops AS $op) {
        if (isset($op->attributes->image)) {
            return $op->attributes->image;
        }
    }

    return null;
}

function title($entry) {
    return $entry->ops[0]->insert;
}

$entry_id = filter_hex($_GET['id']);
$entry = get_entry($entry_id);
if ($entry) {
    succeed([
        'data' => [
            'entry_id' => $entry->entry_id,
            'image_url' => cloudinaryThumbnailUrl(firstImage($entry)),
            'title' => title($entry)
        ]
    ]);
}
fail("No entry found for id $entry_id");
