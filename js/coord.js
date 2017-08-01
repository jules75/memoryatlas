
let Inline = Quill.import('blots/inline');

class CoordBlot extends Inline {

  static create(coordLatLng) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'coord');
    node.setAttribute('data-lat', coordLatLng.lat());
    node.setAttribute('data-lng', coordLatLng.lng());
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
    <div id="mapContainer">
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
      onOk(map.getCenter());
    });

  }

  static popupShow(coord) {
    let url = `https://google.com/maps?q=${coord.lat},${coord.lng}`;
    window.open(url, '_blank');
  }

}

CoordBlot.blotName = 'coord';
CoordBlot.tagName = 'span';