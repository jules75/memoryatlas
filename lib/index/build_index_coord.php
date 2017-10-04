<?php

/*
 * This code is called every 60 seconds from cron job.
 * It builds index file for fast lookup of entry contents.
 */
 
require_once __DIR__ . '/../../db.php';
    
$result = [];

foreach (get_entries()->result AS $e) {

    $entry = get_entry($e->entry_id);

    if (isset($entry->hidden) && $entry->hidden) {
        continue;
    }

    if (isset($entry->ops)) {
        foreach($entry->ops AS $op) {
            if (isset($op->attributes->coord)) {
                $result[] = [
                    'coord' => [
                        'lat' => floatval($op->attributes->coord->lat),
                        'lng' => floatval($op->attributes->coord->lng)
                    ],
                    'title' => $op->insert,
                    'entry_id' => $entry->entry_id,
                    'indexed' => time()
                    ];
            }
        }
    }

}

print_r(json_encode($result, JSON_PRETTY_PRINT));
