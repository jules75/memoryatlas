<?php

require_once '../../config.php';
require_once '../../db.php';
require_once '../../api/fns.php';

$entry_id = filter_hex($_GET['id']);
succeed(get_entry_history($entry_id));
