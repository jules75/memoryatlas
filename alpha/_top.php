<?php

session_start();

function generateRandomEntryId() {
  return sha1(mt_rand().mt_rand().mt_rand());
}


// set session timeout to 1 week - not sure if this is working
ini_set('session.gc_maxlifetime', '604800');
ini_set('session.cookie_lifetime', '604800');


?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Memory Atlas</title>

  <link rel='stylesheet prefetch' href='//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel='stylesheet prefetch' href='//fonts.googleapis.com/css?family=Open+Sans%3A300,400,600,700'>
  <link rel='stylesheet prefetch' href='//cdn.quilljs.com/1.0.0/quill.core.css'>
  <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.css" type="text/css" rel="stylesheet" />

  <link href="//cdn.quilljs.com/1.3.0/quill.bubble.css" rel="stylesheet">  

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/map.css">
  <link rel="stylesheet" href="/css/calendarPicker.css">
  <link rel="stylesheet" href="/css/imageUploader.css">

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>  

</head>

<body>

  <nav>
    <div>
      <h1>The Memory Atlas</h1>
      <h2>Stories worth telling</h2>
      </div>
    <ul>
      <li><a href="/alpha/home.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <?php if (isset($_SESSION['user'])) : ?>
      <li><a href="/alpha/entry.php?entry_id=<?php echo generateRandomEntryId() ?>"><i class="fa fa-plus" aria-hidden="true"></i> New entry</a></li>
      <li><a href="/alpha/password.php"><i class="fa fa-user" aria-hidden="true"></i> Account</a></li>
      <li><a href="/alpha/logout.php" onclick="return confirm('Log out now?');"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout <?php echo $_SESSION['user']['email'] ?></a></li>
        <?php else : ?>
      <li><a href="/alpha/login.php"><i class="fa fa-user" aria-hidden="true"></i> Login</a></li>
        <?php endif; ?>
      </ul>
      </nav>

  <div id="content">