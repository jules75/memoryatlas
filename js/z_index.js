
let saveIsRequired = false;

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
  $('#image-container img').click(function (e) {
    $.featherlight($(this));
  });

}

function initHoverHandlers() {
  $('span[data-tagtype="image"]').hover(ImageBlot.onHover);
}

function initBackgroundSlideshow() {

  let imageTags = $.makeArray($('span[data-tagtype="image"]'));
  let index = 0;

  function changeBackground() {
    if (index >= imageTags.length) {
      index = 0;
    }
    setBackground(imageTags[index].dataset.url);
    index++;
  }

  // if images tags present, update once, then every 60 seconds
  if (imageTags.length > 0) {
    changeBackground();
    setInterval(changeBackground, 60 * 1000);
  }
}

function initApp() {
  loadImages();
  initHoverHandlers();
  initBackgroundSlideshow();
}

function setBackground(imageUrl) {
  $('html').css('background-image', `url(${imageUrl})`);
  $('html').css('background-size', 'cover');

  $('body').css('animation', 'fade-in-out 60s');
  $('body').css('animation-iteration-count', 'infinite');
}

// set flag if save is required
quill.on('text-change', function (delta, oldDelta, source) {
  saveIsRequired = true;
});

function onSaveSuccess(i) {
  console.log(i);
  saveIsRequired = false;
}

function urlPageId() {
  return window.location.href.match(/entry_id=([a-fA-F0-9]+)/)[1];
}

// periodically check if save requied
setInterval(function () {
  if (saveIsRequired) {
    console.log("Saving document...");
    let contents = quill.getContents();
    contents = JSON.parse(JSON.stringify(contents));  // breaks if you don't do this
    contents.entry_id = urlPageId();
    $.post('/api.php',
      {
        action: 'save',
        payload: contents
      },
      onSaveSuccess
    ).fail(function(data) {
      alert(data.responseText);
      saveIsRequired = false; // TODO: this is not an optimal solution!!!!!
    });
  }
}, 2000);


// warn if unsaved document (adapted from https://stackoverflow.com/a/7317311)
window.onload = function() {
    window.addEventListener("beforeunload", function (e) {
        if (!saveIsRequired) {
            return undefined;
        }

        // Most modern browsers don't show this message
        var confirmationMessage = 'You should wait for your work to finish saving. Leave now?';

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
};
