
let Inline = Quill.import('blots/inline');

class CoordBlot extends Inline {

  static create(coord) {
    console.log("CoordBlot create()");
    let node = super.create();
    node.setAttribute('data-tagtype', 'coord');
    node.setAttribute('data-lat', coord.lat);
    node.setAttribute('data-lng', coord.lng);
    return node;
  }

  static formats(node) {
    let lat = Number(node.getAttribute('data-lat'));
    let lng = Number(node.getAttribute('data-lng'));
    return { lat: lat, lng: lng };
  }

  static popupEditor(onOk, onCancel) {

    createOverlay();

    let mapDiv = $(`
    <div id="mapContainer" class="modal">
      <div id="map"></div>
      <div class="buttonRow">
        <button id="ok">OK</button>
        <button id="cancel">Cancel</button>
        </div>
    </div>
    `);

    // create map
    $('body').append(mapDiv);
    let map = new google.maps.Map(document.getElementById('map'), {
      center: { lat: -37.397, lng: 143.644 },
      zoom: 8
    });

    // create crosshairs
    var chDiv = document.createElement("div");
    chDiv.style.background = "url(/img/center.png) no-repeat";
    chDiv.style.width = "50px";
    chDiv.style.height = "50px";
    map.controls[google.maps.ControlPosition.CENTER].push(chDiv);

    // close on any button
    $('#mapContainer button').click(function (e) {
      $('#mapContainer').remove();
      destroyOverlay();
    });

    // handle OK button
    $('#mapContainer #ok').click(function (e) {
      let center = map.getCenter();      
      onOk({lat: center.lat(), lng: center.lng()});
    });

  }

  static popupShow(coord) {
    
    function nearlyEqual(a, b) {
      return Math.abs(a-b) < 0.000001;
    }

    function isMatchingMarker(marker) {
      return nearlyEqual(marker.getPosition().lat(), coord.lat) 
        && nearlyEqual(marker.getPosition().lng(), coord.lng);
    }

    // find marker with matching coordinates
    let markers = mapShowMarkers.filter(isMatchingMarker);
    if (markers.length == 0) {
      console.warn("No map markers found for given coordinate");
    }
    else {
      mapShow.panTo(markers[0].getPosition());
      mapShow.setZoom(16);
    }

  }

}

CoordBlot.blotName = 'coord';
CoordBlot.tagName = 'span';
