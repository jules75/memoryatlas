<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');

$entry_id = filter_hex($_GET['id']);
succeed(get_entry_history($entry_id));
