<?php

session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/lib/common.php';
require $_SERVER['DOCUMENT_ROOT'] . '/lib/entry.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';

function mongoIdToTimestamp($id) {
  return hexdec(substr($id, 0, 8));
}

// returns appropriate page title (string) for current page
function pageTitle() {

  $title = "Memory Atlas";

  if ($_SERVER['SCRIPT_NAME'] == '/entry.php') {
    $entry = get_entry($_GET['entry_id']);
    $title = title($entry);
  }

  return $title;
}

// set session timeout to 1 week - not sure if this is working
ini_set('session.gc_maxlifetime', '604800');
ini_set('session.cookie_lifetime', '604800');


?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title><?php echo pageTitle(); ?></title>

  <link rel='stylesheet prefetch' href='//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel='stylesheet prefetch' href='//fonts.googleapis.com/css?family=Montserrat|Kreon|Cinzel|Open+Sans%3A300,400,600,700'>
  <link rel='stylesheet prefetch' href='//cdn.quilljs.com/1.0.0/quill.core.css'>
  <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.css" type="text/css" rel="stylesheet" />

  <link href="//cdn.quilljs.com/1.3.0/quill.bubble.css" rel="stylesheet">  

  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/thirst.css">

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>  
  <script src="/js/cloudinary.js"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>  

  <script>

  function onYouTubePlayerAPIReady() {
  
    console.log('onYouTubePlayerAPIReady');
  
    youTubePlayer = new YT.Player('ytplayer', {
      width: '480',
      height: '270'
    });

    /* 
    Had trouble getting first video to cue. 
    It depends on two things happening; YouTube API is ready, and Quill has finished loading.
    Instead set video to load after 3 seconds, ugly but it works.
    */
    setTimeout(loadFirstVideo, 3000);
  }

  </script>

</head>

<body>

  <nav>
    <ul>
      <li>
        <a class="logo" href="/home.php">
          <img src="/img/logo4.png" alt="The Memory Atlas" title="The Memory Atlas" />
          </a>
        </li>
      <li><h2>The Memory Atlas</h2></li>
      <li><a href="/home.php">Explore the map</a></li>
      <li><a href="/about.php">About</a></li>
      <li><a href="/search.php">Search</a></li>
        <?php if (isset($_SESSION['user'])) : ?>
      <li><a href="/entry.php?entry_id=<?php echo generate_token() ?>">Add entry</a></li>
      <li><a href="/entries.php">My entries</a></li>
      <li><a href="/password.php">My password</a></li>
      <li><a href="/logout.php" onclick="return confirm('Log out now?');">Logout <?php echo $_SESSION['user']['username'] ?></a></li>
        <?php else : ?>
      <li><a href="/login.php" title="Login">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>


  <div id="content">