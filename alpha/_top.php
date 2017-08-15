<?php

session_start();

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Memory Atlas</title>

  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Open+Sans%3A300,400,600,700'>
  <link rel='stylesheet prefetch' href='http://cdn.quilljs.com/1.0.0/quill.core.css'>
  <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.css" type="text/css" rel="stylesheet" />

  <link href="//cdn.quilljs.com/1.3.0/quill.bubble.css" rel="stylesheet">  

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/map.css">
  <link rel="stylesheet" href="/css/calendarPicker.css">
  <link rel="stylesheet" href="/css/imageUploader.css">

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>  

</head>

<body>

  <nav>
    <div>
      <h1>The Memory Atlas</h1>
      <h2>Stories worth telling</h2>
      </div>
    <ul>
      <li><a href="/alpha/home.php">Home</a></li>
        <?php if (isset($_SESSION['user'])) : ?>
      <li><a href="/alpha/logout.php" onclick="return confirm('Log out now?');">Logout <?php echo $_SESSION['user']['email'] ?></a></li>
        <?php else : ?>
      <li><a href="/alpha/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
      </nav>

  <div id="content">