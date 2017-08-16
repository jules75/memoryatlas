<?php include_once '_top.php'; ?>

  <div id="image-container">
  </div>

  <div id="youtube-container">
  </div>

  <div id="tooltip-controls">
    <button id="coord-button" title="Place on map"> <i class="fa fa-map-marker" ></i></button>
    <button id="date-button" title="Calendar date"> <i class="fa fa-calendar" ></i></button>
    <button id="image-button" title="Upload image"><i class="fa fa-file-image-o"></i></button>
    <button id="youtube-button" title="Link a Youtube"><i class="fa fa-youtube"></i></button>
    <button id="erase-button" title="Erase formatting"><i class="fa fa-ban"></i></button>
  </div>

  <div id="scrolling-container">
    <div id="editor-container">Tell your story...</div>
  </div>

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
  <script src="//cdn.quilljs.com/1.3.0/quill.min.js"></script>
  <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.7/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>


  <script async defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4&callback=initMap"></script>

  <script src="/app.compiled.js"></script>

  <script>
    
    let userUrl = new URL(location.href);
    let entryId = userUrl.searchParams.get('entry_id');
    let apiUrl = `/api.php?action=page&entry_id=${entryId}`;

    $.getJSON(apiUrl, function (data) {
      quill.setContents(data.ops);
      initApp();
    });
  </script>

<?php include_once('_bottom.php'); ?>