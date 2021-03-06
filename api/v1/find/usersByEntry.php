<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

$entry_id = filter_hex($_GET['id']);
$entries = get_entry_history($entry_id);
$contributors = [];

foreach ($entries as $entry) {
    $contributors[] = $entry->user->id;
}


succeed(['data' =>
    [
        ['creator' => ['user_id' => array_slice($contributors, -1)[0]]],
        ['contributors' => ['user_ids' => array_values(array_unique($contributors))]]
    ]]);
