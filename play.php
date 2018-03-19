<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/play.css">

  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php'); ?>

  <div id="ytplayer"></div>

  <button id="prev"><i class="fas fa-step-backward"></i></button>
  <button id="pause"><i class="fas fa-pause"></i></button>
  <button id="play"><i class="fas fa-play"></i></button>
  <button id="next"><i class="fas fa-step-forward"></i></button>

  <div id="slideshow">
  </div>

  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
  <script src="//www.google.com/jsapi?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4"></script>
  
  <script src="/lib/3rdparty/underscore-min.js"></script>
  <script src="/js/QuillDoc.js"></script>

  <script>

// app state
let apiUrl = "/api/v1/entry.php?id=<?php echo $_GET['entry_id'] ?>";
let delayMs = 5000;
let isPlaying = true;
let panelIndex = 0;
let panelCount = 0;

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
  if (panelIndex<0) {
    panelIndex=0;
  }
  updateUI();
}

function nextPanel(e) {
  if (panelIndex < panelCount) {
    panelIndex++;
    updateUI();
  }
}

function onPause() {
  isPlaying = false;
  updateUI();
}

function onPlay() {
  isPlaying = true;
  updateUI();
}

function delayComplete() {
  if (isPlaying) {
    nextPanel();
  }
}

function updateUI() {
  
  $('#pause').toggle(isPlaying);
  $('#play').toggle(!isPlaying);
  
  $('#slideshow p').removeClass('active');
  $(`#slideshow p:nth-child(${panelIndex+1})`).addClass('active');
}

function onYouTubePlayerAPIReady() {

  $.getJSON(apiUrl)
    .done(function(data) {
      
      let paras = QuillDoc.coalesceParagraphs(data.data.ops);
      let imgParas = _.filter(paras, QuillDoc.hasImage);
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

      _.map(imgParas, createSlideshowPane);
      updateUI();
      panelCount = $('#slideshow p').length;
      
      $('#prev').on('click', prevPanel);
      $('#next').on('click', nextPanel);
      $('#pause').on('click', onPause);
      $('#play').on('click', onPlay);

      // kick off auto slideshow
      setInterval(delayComplete, delayMs);

    })
    .fail(function(data) {
      alert("Error getting entry data");
    });
}

  </script>

<script src="//www.youtube.com/player_api"></script>

<?php include_once('_bottom.php'); ?>

