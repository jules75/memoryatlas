<?php

/*
	Database in/out
*/


// setup mongodb document database
require_once 'vendor/autoload.php';
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);


// Return all entries (newest revision)
// Note that this WILL INCLUDE hidden entries
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


// Return newest revision of single entry, EVEN IF it is hidden
function get_entry($entry_id) {

    global $mongo, $readPreference;

    $filter = ['entry_id' => $entry_id];
    $options = ['sort' => ['_id' => -1], 'limit' => 1];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('memoryatlas.entries', $query, $readPreference);

    foreach ($cursor as $doc) {
        return $doc;
    }
}


// Return requested revision of single entry
function get_entry_revision($entry_id, $revision_id) {

    global $mongo, $readPreference;

    $filter = ['entry_id' => $entry_id, '_id' => new MongoDB\BSON\ObjectID($revision_id)];
    $options = ['sort' => ['_id' => -1], 'limit' => 1];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('memoryatlas.entries', $query, $readPreference);

    foreach ($cursor as $doc) {
        return $doc;
    }
}


// Return all revisions of single entry, newest first
function get_entry_history($entry_id) {

    global $mongo, $readPreference;

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


// Inserts new comment into database
// Comments are the same as entries, but don't have a history
function insert_comment($entry_contents) {
    
    global $mongo;

    $command = new MongoDB\Driver\Command([
        'insert' => 'comments',
        'documents' => $entry_contents
    ]);

    $cursor = $mongo->executeCommand('memoryatlas', $command);

    foreach ($cursor as $doc) {
        return $doc;
    }
}


// Return all comments for given entry 
function get_comments($entry_id)
{
    global $mongo, $readPreference;
    
    $filter = ['parent_entry_id' => $entry_id];
    $query = new MongoDB\Driver\Query($filter);
    $cursor = $mongo->executeQuery('memoryatlas.comments', $query, $readPreference);

    $result = [];
    foreach($cursor AS $doc) {
        $result[] = $doc;
    }
    return $result;
}


// Return user account with given email address
function get_user($email) {

    global $mongo, $readPreference;

	$filter = ['email' => $email];
	$options = ['limit' => 1];
	$query = new MongoDB\Driver\Query($filter, $options);
	$cursor = $mongo->executeQuery('memoryatlas.users', $query, $readPreference);

	foreach($cursor AS $doc) {
        return $doc;
	}
}


// Return user account with given Mongo id string
function get_user_by_id($id) {

    global $mongo, $readPreference;

	$filter = ['_id' => new MongoDB\BSON\ObjectID($id)];
	$options = ['limit' => 1];
	$query = new MongoDB\Driver\Query($filter, $options);
	$cursor = $mongo->executeQuery('memoryatlas.users', $query, $readPreference);

	foreach($cursor AS $doc) {
        return $doc;
	}
}


// Return user account with given username
function get_user_by_username($username) {
    
    global $mongo, $readPreference;

    $filter = ['username' => $username];
    $options = ['limit' => 1];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('memoryatlas.users', $query, $readPreference);

    foreach($cursor AS $doc) {
        return $doc;
    }
}


// Inserts new user into database
function insert_user($email, $username, $password_hash) {
    
        global $mongo;
    
        $command = new MongoDB\Driver\Command([
            'insert' => 'users',
            'documents' => [[
                'email' => $email,
                'username' => $username,
                'password_hash' => $password_hash
            ]]
        ]);
    
        $cursor = $mongo->executeCommand('memoryatlas', $command);
    
        foreach ($cursor as $doc) {
            return $doc;
        }
    }


// Update password hash for user with given email, returns # of modified rows
function update_password_hash($email, $password_hash) {

    global $mongo;

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['email' => $email],
        ['$set' => ['password_hash' => $password_hash]]);

    $result = $mongo->executeBulkWrite('memoryatlas.users', $bulk);
    return $result->getModifiedCount();
}


// Returns # of modified rows
function update_login_token($email, $login_token) {

    global $mongo;

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['email' => $email],
        ['$set' => 
            ['login_token' => $login_token,
            'login_token_created' => time()]]);

    $result = $mongo->executeBulkWrite('memoryatlas.users', $bulk);
    return $result->getModifiedCount();
}