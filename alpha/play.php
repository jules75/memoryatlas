<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/play.css">

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php'); ?>

  <div id="ytplayer"></div>

  <button id="prev">prev</button>
  <button id="next">next</button>

  <div id="slideshow">
  </div>

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
  <script src="//www.google.com/jsapi?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4"></script>
  
  <script src="/lib/3rdparty/underscore-min.js"></script>
  <script src="/js/QuillDoc.js"></script>

  <script>

let apiUrl = "/api/v1/entry.php?id=<?php echo $_GET['entry_id'] ?>";

var panelIndex = 0;

function createSlideshowPane(op) {
  
  var p = $('<p>');
  var span = $('<span>');
  var img = $('<img>');

  $(span).text(op.insert);

  if (op.hasOwnProperty('attributes')) {
    if (op.attributes.hasOwnProperty('image')) {
      $(img).attr('src', op.attributes.image);
    }
  }

  $(p).append(img);
  $(p).append(span);  
  $("#slideshow").append(p);
}

function prevPanel(e) {
  panelIndex--;
  if (panelIndex<0) panelIndex=0;
  updateUI();
}

function nextPanel(e) {
  panelIndex++;
  updateUI();
}

function updateUI() {
  console.log(panelIndex);
  $('#slideshow p').removeClass('active');
  $(`#slideshow p:nth-child(${panelIndex+1})`).addClass('active');
}

function onYouTubePlayerAPIReady() {

  $.getJSON(apiUrl)
    .done(function(data) {
      
      let paras = QuillDoc.coalesceParagraphs(data.data.ops);
      let vid = QuillDoc.firstYouTubeId(data.data.ops);
      
      youTubePlayer = new YT.Player('ytplayer', {
        width: '480',
        height: '270',
        videoId: vid,
        playerVars: {
          autoplay: 1,
          loop: 1,
          playlist: vid
        }
      });

      _.map(paras, createSlideshowPane);
      updateUI();
      
      $('#prev').on('click', prevPanel);
      $('#next').on('click', nextPanel);
    })
    .fail(function(data) {
      alert("Error getting entry data");
    });
}

  </script>

<script src="//www.youtube.com/player_api"></script>

<?php include_once('_bottom.php'); ?>

