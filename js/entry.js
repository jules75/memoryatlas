
// globals
let saveIsRequired = false;
let mapShow = {};
let mapShowMarkers = [];

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

function isMap(quillOp) {
  if (quillOp.hasOwnProperty('attributes')) {
    if (quillOp.attributes.hasOwnProperty('coord')) {
      return true;
    }
  }
  return false;
}

Quill.register(CoordBlot);
Quill.register(DateBlot);
Quill.register(HashtagBlot);
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

      case 'hashtag':
        HashtagBlot.popupShow(t.dataset.hashtag);
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


function createMap() {

  let mapOps = quill.getContents().ops.filter(isMap);
  
  if (mapOps.length == 0) {
    return;
  }

  let mapDiv = $(`<div id="mapShow"></div>`);
  let bounds = new google.maps.LatLngBounds();

  // create map
  $('#map-container').append(mapDiv);
  mapShow = new google.maps.Map(document.getElementById('mapShow'), {
    center: { lat: -37.397, lng: 143.644 },
    zoom: 8
  });

  function createMarker(quillOp) {

    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(quillOp.attributes.coord.lat, quillOp.attributes.coord.lng),
      map: mapShow,
      title: quillOp.insert
    });

    mapShowMarkers.push(marker);

    bounds.extend(marker.getPosition());    
  }

  // create markers, fit to bounds
  mapOps.map(createMarker);
  mapShow.fitBounds(bounds);  

}

// Takes string, returns array of string with hashtags in their own entries
// e.g. "A #sentence with #hashtags in it" => ["A ", "#sentence", " with ", "#hashtags", " in it"]
function splitHashtags(s) {

  var arr=[], i=0, word='', inhash=false, c;

  while(i < s.length) {

    c = s[i];

    if (c == '#') {
        if (i > 0) {
          arr.push(word);
        }
        inhash=true;
        word = '';
    }

    // break hashtag on whitespace or certain punctuation marks
    if (' \t\r\n,.:;'.indexOf(c) > -1) {
        if (inhash) {
          arr.push(word);
          inhash=false;
          word = '';
        }
    }

    word += c;
    i++;
  } 

  arr.push(word);
  return arr;
}

function autolinkHashtags() { 

  function makeHashtagOp(s) {
    if (s[0] == '#') {
      return {insert: s, attributes: {hashtag: s.slice(1)}};
    }
    return {insert: s};
  }

  // lock editor
  quill.disable();

  // iterate ops, split by hashtags
  var result = [];  
  for (let op of quill.getContents().ops) {

    // don't search for hashtags in tagged text
    if (op.hasOwnProperty('attributes')) {
      result = result.concat(op);
      continue;
    }

    result = result.concat(splitHashtags(op.insert).map(makeHashtagOp));
  }

  // save to editor, unlock
  quill.setContents({ops: result});
  quill.enable();
}

function initApp() {

  let userUrl = new URL(location.href);
  let entryId = userUrl.searchParams.get('entry_id');
  let apiUrl = `/api.php?action=entry&entry_id=${entryId}`;

  let newEntryOps = {
    "ops": [
      {
        "insert": "Welcome to your new entry. To get started, just click somewhere in here and start typing. Start by deleting this text!\n\nYou can add "
      },
      {
        "attributes": {
          "image": "https://res.cloudinary.com/dtnrj96uf/image/upload/v1502854465/dowczp3wbndh6og3wpaq.jpg"
        },
        "insert": "images and photos"
      },
      {
        "insert": ", "
      },
      {
        "attributes": {
          "coord": {
            "lat": -37.56261960591671,
            "lng": 143.85745019340519
          }
        },
        "insert": "map coordinates"
      },
      {
        "insert": ", "
      },
      {
        "attributes": {
          "date": "19410414"
        },
        "insert": "dates"
      },
      {
        "insert": " and more. Just highlight some text with your mouse and choose from the menu.\n\nTell your story!\n"
      }
    ]
  };

  $.getJSON(apiUrl, function (data) {
    quill.setContents(data.ops);
  }).fail(function(data) {
    quill.setContents(newEntryOps);
  }).always(function() {
    createMap();
    loadImages();
    initHoverHandlers();
    initBackgroundSlideshow();

    autolinkHashtags();

    // set flag when editor contents changes
    quill.on('text-change', function (delta, oldDelta, source) {
      saveIsRequired = true;
    });
  });
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

function setBackground(imageUrl) {
  $('html').css('background-image', `url(${cloudinaryGrayscaleUrl(imageUrl)})`);
  $('html').css('background-size', 'cover');

  $('body').css('animation', 'fade-in-out 60s');
  $('body').css('animation-iteration-count', 'infinite');
}

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
    ).fail(function (data) {
      alert(data.responseText);
      saveIsRequired = false; // TODO: this is not an optimal solution!!!!!
    });
  }
}, 2000);


// warn if unsaved document (adapted from https://stackoverflow.com/a/7317311)
window.onload = function () {
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
