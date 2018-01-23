<?php

session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/lib/common.php';


function mongoIdToTimestamp($id) {
  return hexdec(substr($id, 0, 8));
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
  <link rel='stylesheet prefetch' href='//fonts.googleapis.com/css?family=Kreon|Cinzel|Open+Sans%3A300,400,600,700'>
  <link rel='stylesheet prefetch' href='//cdn.quilljs.com/1.0.0/quill.core.css'>
  <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.css" type="text/css" rel="stylesheet" />

  <link href="//cdn.quilljs.com/1.3.0/quill.bubble.css" rel="stylesheet">  

  <link rel="stylesheet" href="/css/common.css">

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>  
  <script src="/js/cloudinary.js"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>  

</head>

<body>

  <nav>
    
    <div>
      <h1>The Memory Atlas</h1>
      <h2>History is You</h2>
      </div>    
    
    <ul>
      <li><a href="/home.php">Explore map</a></li>
      <li><a href="/timeline.php">Explore timeline</a></li>
      <li>&nbsp;</li>
        <?php if (isset($_SESSION['user'])) : ?>
      <li><a href="/entry.php?entry_id=<?php echo generate_token() ?>">Add entry</a></li>
      <li><a href="/entries.php">My entries</a></li>
      <li>&nbsp;</li>
      <li><a href="/password.php">My account</a></li>
      <li><a href="#" onclick="alert('Not implemented yet')">Help &amp; tutorials</a></li>
      <li>&nbsp;</li>
      <li><a href="/logout.php" onclick="return confirm('Log out now?');">Logout <?php echo $_SESSION['user']['username'] ?></a></li>
        <?php else : ?>
      <li><a href="/login.php" title="Login">Login</a></li>
      <li><a href="/signup.php" title="Login">Sign up</a></li>
        <?php endif; ?>        
      </ul>
    </nav>


  <div id="content">