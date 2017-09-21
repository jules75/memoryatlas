<?php

/* 
    Temporary utility to create user account. 
    Returns string that can be pasted to mongo console.
    */

$arr = [
    'email' => $_GET['email'],
    'username' => $_GET['username'],
    'password_hash' => password_hash($_GET['password'], PASSWORD_DEFAULT)
];

$str = json_encode($arr);
echo "db.users.insert($str)";