<?php

/**
 * Create index files for fast lookup of coord/date/tag/user data.
 * These are long-running tasks not intended to be called by web user.
 */

require_once 'db.php';


// returns new array with $value added, unchanged if $value already present
function array_add($arr, $value) {

    if(in_array($value, $arr)) {
        return $arr;
    }
    $arr[] = $value;
    return $arr;
}


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


function create_user_cache() {
    
    $result = [];
    
    foreach (get_entries()->result AS $e) {

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
    }

    file_put_contents('cache/users.json', json_encode($result, JSON_PRETTY_PRINT));
}



echo "Creating JSON index files...";
create_coord_cache();
create_date_cache();
create_tag_cache();
create_user_cache();
echo "Done.";

