<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

$entry_id = filter_hex($_GET['id']);
$comments = get_comments($entry_id);

if ($comments) {
    succeed(['data' => $comments]);
}
else {
    fail("No comments found for that entry id");
}
