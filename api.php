<?php

require_once 'config.php';
require_once 'db.php';
require_once 'api/fns.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['action']) {

        case 'save':
            
			// user must be logged in to save
            if (isset($_SESSION['user'])) {
				$_POST['payload']['user']['id'] = $_SESSION['user']['id'];                
				succeed(insert_entry([$_POST['payload']]));
            }

			else {
				fail("You must be logged in to save changes");
			}

            break;

    }
}
