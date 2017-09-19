<?php

require_once '../../config.php';
require_once '../../db.php';
require_once '../../api/fns.php';

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

function coords($entry) {

    $result = [];

    foreach ($entry->ops AS $op) {
        if (isset($op->attributes->coord)) {
            $row = $op->attributes->coord;
            $row->entry_id = $entry->entry_id;
            $result[] = $row;
        }
    }

    return $result;
}

$entry_id = filter_hex($_GET['id']);
$entry = get_entry($entry_id);
if ($entry) {
    succeed([
        'data' => [
            'entry_id' => $entry->entry_id,
            'image_url' => cloudinaryThumbnailUrl(firstImage($entry)),
            'title' => title($entry),
            'coords' => coords($entry)
        ]
    ]);
}
fail("No entry found for id $entry_id");
