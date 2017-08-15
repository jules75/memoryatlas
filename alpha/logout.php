<?php

include_once '_top.php';

unset($_SESSION['user']);

header('Location: /alpha/login.php');

