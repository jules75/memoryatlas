<?php

/*
 * Create static HTML representations of entries.
 */

require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');


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
    $lines = explode("\n", $entry->ops[0]->insert);
    return $lines[0];
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

function entry_preview_html($entry_id)
{
    $entry = get_entry($entry_id);
    $img = cloudinaryThumbnailUrl(firstImage($entry));
    $title = title($entry);

    return "<li data-entry-id='$entry_id'><a href='/alpha/entry.php?entry_id=$entry_id'>" . 
            ($img ? "<img src='$img'>" : "")
            . "<span>$title</span></a></li>";
}

