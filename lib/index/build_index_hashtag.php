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
            
            $words = preg_split("/\s/", $op->insert);

            foreach($words AS $word) {
                if (isset($word[0]) && $word[0] == '#') {
                    // same data structure as 'list' API call
                    $result[$word]['result'][] = [
                        'entry_id' => $entry->entry_id, 
                        'revisions' => $e->revisions,
                        'indexed' => time()
                        ];
                }
            }
        }
    }

}

print_r(json_encode($result, JSON_PRETTY_PRINT));

