
function createOverlay() {
  var div = $("<div/>");
  div.attr("id", "fullScreenOverlay");
  div.css("position", "fixed");
  div.css("background-color", "#ccc");
  div.css("top", 0);
  div.css("bottom", 0);
  div.css("left", 0);
  div.css("right", 0);
  div.css("opacity", 0.85);
  $("body").append(div);
}

function destroyOverlay() {
  $("#fullScreenOverlay").remove();
}

Quill.register(CoordBlot);
Quill.register(DateBlot);
Quill.register(ImageBlot);
Quill.register(YoutubeBlot);

let quill = new Quill('#editor-container', {
  modules: {
    toolbar: '#tooltip-controls'
  },
  scrollingContainer: '#scrolling-container',
  theme: 'bubble'
});

// handle clicks on tagged text
$("#editor-container").click(function (e) {

  let t = e.target;

  if (t.dataset.tagtype !== undefined) {

    switch (t.dataset.tagtype) {

      case 'coord':
        let coord = { lat: t.dataset.lat, lng: t.dataset.lng };
        CoordBlot.popupShow(coord);
        break;

      case 'date':
        DateBlot.popupShow(t.dataset.yyyymmdd);
        break;

      case 'image':
        ImageBlot.popupShow(t.dataset.url);
        break;

      case 'youtube':
        YoutubeBlot.popupShow(t.dataset.videoid);
        break;

    }

  }
});

// button listeners
$('#coord-button').click(function () {
  CoordBlot.popupEditor(function (gLatLng) {
    quill.format('coord', gLatLng);
  });
});
$('#date-button').click(function () {
  DateBlot.popupEditor(function (yyyymmdd) {
    quill.format('date', yyyymmdd);
  });
});
$('#image-button').click(function () {
  ImageBlot.popupEditor(function (url) {
    quill.format('image', url);
  });
});
$('#youtube-button').click(function () {
  YoutubeBlot.popupEditor(function (vid) {
    quill.format('youtube', vid);
  });
});
$('#erase-button').click(function () {
  let sel = quill.getSelection();
  quill.removeFormat(sel.index, sel.length, 'user');
});


function initMap() {
  console.log("initMap()");
}


function loadImages() {

  function injectImage(i, el) {
    let img = $('<img>').attr('src', el.dataset.url);
    $('#image-container').append(img);
  }

  $('span[data-tagtype="image"]').each(injectImage);
  $('#image-container img').click(function(e) { 
    $.featherlight($(this));
  });

}

function initHoverHandlers() {
  $('span[data-tagtype="image"]').hover(ImageBlot.onHover);
}

function initBackgroundSlideshow() {

  let imageElements = $('span[data-tagtype="image"]');
  let index = 0;

  function changeBackground() {

    // skip non-images
    while (imageElements[index] == undefined) {
      index++;
      if (index > imageElements.length) {
        index = 0;
      }
    }

    // set background
    if (imageElements[index]) {
      setBackground(imageElements[index].dataset.url);
      index++;
    }
  }

  setInterval(changeBackground, 1000);
}

function initApp() {
  loadImages();
  initHoverHandlers();
  initBackgroundSlideshow();
}

function setBackground(imageUrl) {
  $('html').css('background-image', `url(${imageUrl})`);
  $('html').css('background-size', 'cover');
  $('body').css('background-color', 'rgba(255,255,255,0.8)');
}