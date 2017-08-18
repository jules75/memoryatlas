<?php

/**
 * Server background tasks, long-running tasks not intended to be called by web user
 */

require_once 'db.php';


function create_coord_cache() {
    
    $result = [];
    
    foreach (get_entries()->result AS $e) {

        $entry = get_entry($e->entry_id);
        foreach($entry->ops AS $op) {
            if (isset($op->attributes->coord)) {
                $result[] = [
                    'coord' => $op->attributes->coord,
                    'title' => $op->insert,
                    'entry_id' => $entry->entry_id
                    ];
            }
        }

    }
    
    file_put_contents('cache/coords.json', json_encode($result, JSON_PRETTY_PRINT));
}


create_coord_cache();

