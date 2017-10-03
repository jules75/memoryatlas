<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/entry.php');


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
