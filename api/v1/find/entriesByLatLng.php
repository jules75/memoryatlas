<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

// get bounds, defaults to 0 on bad/missing entries
$north = floatval($_GET['north']);
$south = floatval($_GET['south']);
$east = floatval($_GET['east']);
$west = floatval($_GET['west']);


// error on bad boundaries
if ($north < $south) {
    fail("'north' must be greater than 'south'");
}
if ($east < $west) {
    fail("'east' must be greater than 'west'");
}
if ($north > 90) {
    fail("'north' must be less than 90");
}
if ($south < -90) {
    fail("'south' must be greater than -90");
}
if ($east > 180) {
    fail("'east' must be less than -180");
}
if ($west < -180) {
    fail("'west' must be greater than -180");
}


$json = file_get_contents('../../../cache/coords.json');

$result = [];

foreach (json_decode($json) AS $item) {

    $lat = $item->coord->lat;
    $lng = $item->coord->lng;

    if ($north > $lat && $lat > $south &&
        $east > $lng && $lng > $west) {
        $result[] = $item->entry_id;
    }
}

$ids = array_values(array_unique($result));
succeed(['data' => ['entry_ids' => $ids]]);
