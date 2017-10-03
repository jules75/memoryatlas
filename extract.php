<?php

/**
 * Server background tasks, long-running tasks not intended to be called by web user
 */

require_once 'db.php';


function create_coord_cache() {
    
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
    
    file_put_contents('cache/coords.json', json_encode($result, JSON_PRETTY_PRINT));
}


function create_date_cache() {
    
    $result = [];
    
    foreach (get_entries()->result AS $e) {

        $entry = get_entry($e->entry_id);

        if (isset($entry->hidden) && $entry->hidden) {
            continue;
        }

        if (isset($entry->ops)) {
            foreach($entry->ops AS $op) {
                if (isset($op->attributes->date)) {
                    $result[$op->attributes->date][] = [
                        'title' => $op->insert,
                        'entry_id' => $entry->entry_id,
                        'indexed' => time()
                        ];
                }
            }
        }

    }

    // sort by keys
    ksort($result);
    
    file_put_contents('cache/dates.json', json_encode($result, JSON_PRETTY_PRINT));
}


function create_tag_cache() {
    
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
    
    file_put_contents('cache/hashtags.json', json_encode($result, JSON_PRETTY_PRINT));
}

echo "Creating JSON index files...";
create_coord_cache();
create_date_cache();
create_tag_cache();
echo "Done.";

