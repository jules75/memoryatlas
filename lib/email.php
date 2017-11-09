<?php

require '../vendor/autoload.php';
use Mailgun\Mailgun;


function send_recovery_email($email, $token, $mailgun_key) {
    
    $mg = Mailgun::create($mailgun_key);

    $url = "https://thememoryatlas.com/alpha/login.php?email=$email&token=$token";
    
    $mg->messages()->send('thememoryatlas.com', [
      'from' => 'noreply@thememoryatlas.com',
      'to' => $email,
      'subject' => 'The Memory Atlas login link',
      'text'    => "Visit the link below to login to The Memory Atlas. The link expires in 10 minutes.
      
$url"]);

}

