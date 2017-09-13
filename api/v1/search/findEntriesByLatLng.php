<?php

require_once '../../../config.php';
require_once '../../../db.php';


function succeed($result)
{
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($result);
    die();
}

function fail($message)
{
    echo $message;
    http_response_code(400);
    die();
}


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

    $lat = floatval($item->coord->lat);
    $lng = floatval($item->coord->lng);

    if ($north > $lat && $lat > $south &&
        $east > $lng && $lng > $west) {
        $result[] = $item->entry_id;
    }
}

$ids = array_values(array_unique($result));
succeed(['data' => ['entry_ids' => $ids]]);
