<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/entry.css">

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php'); ?>

  <noscript>
  <pre style="white-space: pre-line;">
    <?php
    $entry = get_entry($_GET['entry_id']);
    foreach ($entry->ops as $op) {
        echo ($op->insert);
    }
    ?>
  </pre>
  </noscript>

  <?php $isReadOnly = isset($_GET['revision_id']); ?>

  <div id="tooltip-controls">
    <button id="image-button" title="Upload image"><i class="fa fa-file-image-o"></i></button>
    <button id="coord-button" title="Place on map"> <i class="fa fa-map-marker" ></i></button>
    <button id="youtube-button" title="Link a Youtube"><i class="fa fa-youtube"></i></button>
    <button id="date-button" title="Calendar date"> <i class="fa fa-calendar" ></i></button>
    <button id="link-button" title="Link web page"><i class="fa fa-link"></i></button>
    <button id="erase-button" title="Erase formatting"><i class="fa fa-ban"></i></button>
  </div>

  <div id="scrolling-container">

    <?php if($isReadOnly): ?>
    <div id="readOnlyWarning">
    <p>This revision created <?php echo date('Y-m-d H:i:s', mongoIdToTimestamp($_GET['revision_id'])); ?></p>
    <p>Editing is disabled</p>
    <p><a href="entry.php?entry_id=<?php echo $_GET['entry_id']; ?>">Go to newest revision</a></p>
    </div>
    <?php endif; ?>

    <div id="editor-container"></div>
  </div>

  <div id="media-panel">
    
    <ul>
    <li>Images <span id="image-count" class="counter"></span></li>
    <li>Map <span id="coord-count" class="counter"></span></li>
    <li>More</li>
    </ul>

    <div class="images"></div>

    <div id="mapShowContainer"></div>
    
    <div id="actions">
      <p><button id="delete">Delete this entry</button></p>
      <p><a href="history.php?entry_id=<?php echo $_GET['entry_id']; ?>">View old versions of this entry</a></p>
    </div>

  </div>

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
  <script src="//cdn.quilljs.com/1.3.0/quill.min.js"></script>
  <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

  <script src="/entry.compiled.js"></script>

  <script src="//www.google.com/jsapi?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4"></script>

  <script> 
    google.load('picker', '1', {'language':'en'}); 
    initApp();
  </script>

<?php include_once('_bottom.php'); ?>