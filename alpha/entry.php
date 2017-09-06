<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/media.css">

<?php require_once('../db.php'); ?>

  <noscript>
  <pre style="white-space: pre-line;">
  <?php 
    $entry = get_entry($_GET['entry_id']);
    foreach ($entry->ops AS $op) {
      echo ($op->insert);
    }
    ?>
  </pre>
  </noscript>

  <div id="tooltip-controls">
    <button id="coord-button" title="Place on map"> <i class="fa fa-map-marker" ></i></button>
    <button id="date-button" title="Calendar date"> <i class="fa fa-calendar" ></i></button>
    <button id="image-button" title="Upload image"><i class="fa fa-file-image-o"></i></button>
    <button id="link-button" title="Link web page"><i class="fa fa-link"></i></button>
    <button id="youtube-button" title="Link a Youtube"><i class="fa fa-youtube"></i></button>
    <button id="erase-button" title="Erase formatting"><i class="fa fa-ban"></i></button>
  </div>

  <div id="scrolling-container">
    <div id="editor-container"></div>
  </div>

  <div id="media-panel">
  </div>

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
  <script src="//cdn.quilljs.com/1.3.0/quill.min.js"></script>
  <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

  <script src="/entry.compiled.js"></script>

  <script src="//www.google.com/jsapi?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4"></script>
  <script async src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4&callback=initApp"></script>

  <script> google.load('picker', '1', {'language':'en'}); </script>

<?php include_once('_bottom.php'); ?>