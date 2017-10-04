<?php

/*
 * This code is called every 60 seconds from cron job.
 * It builds index file for fast lookup of entry contents.
 */

require_once __DIR__ . '/../../db.php';

$result = [];

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE); // yuck

// returns new array with $value added, unchanged if $value already present
function array_add($arr, $value) {

    if(in_array($value, $arr)) {
        return $arr;
    }
    $arr[] = $value;
    return $arr;
}

foreach (get_entries()->result AS $e) {

    if (!isset($e->entry_id)) {
        continue;
    }

    $entry = get_entry($e->entry_id);

    if (isset($entry->hidden) && $entry->hidden) {
        continue;
    }

    $entry_history = get_entry_history($e->entry_id);
    
    // get creator of original entry
    $oldest_entry = end($entry_history);
    if (isset($entry->ops)) {
        $result[$oldest_entry->user->id]['created'][] = $e->entry_id;
    }

    // get contributors of each revision
    foreach($entry_history AS $entry) {
        $result[$entry->user->id]['contributed'] = array_add($result[$entry->user->id]['contributed'], $entry->entry_id);
    }

    // exclude 'created' entries from 'contributed'
    $result[$entry->user->id]['contributed'] = array_diff($result[$entry->user->id]['contributed'], $result[$oldest_entry->user->id]['created']);
}

print_r(json_encode($result, JSON_PRETTY_PRINT));