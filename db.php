<?php

/*
	Database in/out
*/


// setup mongodb document database
require_once 'vendor/autoload.php';
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);


// Return all entries (newest revision)
function get_entries()
{
    global $mongo;

    $command = new MongoDB\Driver\Command([
        'aggregate' => 'entries',
        'pipeline' => [
            ['$group' => ['_id' => '$entry_id', 'revisions' => ['$sum' => 1]]],
            ['$sort' => ['_id' => 1]],
            ['$project' => ['_id' => 0, 'entry_id' => '$_id', 'revisions' => 1]]
        ]
    ]);

    $cursor = $mongo->executeCommand('memoryatlas', $command);
    foreach ($cursor as $doc) {
        return $doc;
    }
}


// Return newest revision of single entry
function get_entry($entry_id) {

    global $mongo;

    $filter = ['entry_id' => $entry_id];
    $options = ['sort' => ['_id' => -1], 'limit' => 1];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('memoryatlas.entries', $query, $readPreference);

    foreach ($cursor as $doc) {
        return $doc;
    }
}


// Return all revisions of single entry, newest first
function get_entry_history($entry_id) {

    global $mongo;

    $filter = ['entry_id' => $entry_id];
    $options = ['sort' => ['_id' => -1]];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('memoryatlas.entries', $query, $readPreference);

    $result = [];
    foreach ($cursor as $doc) {
        $result[] = $doc;
    }
    return $result;
}


// Inserts new entry into database
// No entries are ever overwritten - full history of entry changes is saved
function insert_entry($entry_contents) {

    global $mongo;

    $command = new MongoDB\Driver\Command([
        'insert' => 'entries',
        'documents' => $entry_contents
    ]);

    $cursor = $mongo->executeCommand('memoryatlas', $command);

    foreach ($cursor as $doc) {
        return $doc;
    }
}

