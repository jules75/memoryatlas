<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $entry = get_entry($_POST['id']);

    if ($entry) {
        $comment = json_decode($_POST['content']);
        $comment->parent_entry_id = $_POST['id'];
        succeed(insert_comment(array($comment)));
    }
    else {
        fail("no entry found");
    }

}
