
let Inline = Quill.import('blots/inline');

class CoordBlot extends Inline {

  static create(coord) {
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
    <div id="mapChooseContainer" class="modal">
      <form id="mapSearch">
        <input type="text" placeholder="Type address here"/>
        </form>
      <div id="mapChoose"></div>
      <div class="buttonRow">
        <button id="ok">OK</button>
        <button id="cancel">Cancel</button>
        </div>
    </div>
    `);

    // create map
    $('body').append(mapDiv);
    let map = L.map('mapChoose').setView([-37.56, 143.85], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // add crosshair (thanks to https://gis.stackexchange.com/a/90230)
    var crosshairIcon = L.icon({
        iconUrl: '/img/center.png',
        iconSize:     [50, 50], // size of the icon
        iconAnchor:   [25, 25], // point of the icon which will correspond to marker's location
    });
    var crosshair = new L.marker(map.getCenter(), {icon: crosshairIcon, clickable:false});
    crosshair.addTo(map);

    // center crosshair when map moves
    map.on('move', function(e) {
        crosshair.setLatLng(map.getCenter());
    });

    function onSearchAddressSuccess(data) {

      $("#mapSearch :input").prop("disabled", false);
      console.log(data);

      if (data.length == 0) {
        alert('Address not found');
      }
      else {
        map.panTo(new L.LatLng(data[0].lat, data[0].lon));
        map.setZoom(20);
      }
    }

    function searchByAddress(addr) {

      let url = `https://nominatim.openstreetmap.org/search?q=${encodeURI(addr)}&format=json`;

      $.ajax({
        url: url,
        data: null,
        cache: false,
        contentType: false,
        processData: false,
        type: 'GET',
        success: onSearchAddressSuccess
      }).fail(function (jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
      });
    }

    // search for address
    $('#mapSearch').on('submit', function (e) {
      $("#mapSearch :input").prop("disabled", true);
      searchByAddress($('#mapSearch input').val());
      e.preventDefault();
    });

    // close on any button
    $('#mapChooseContainer button').click(function (e) {
      $('#mapChooseContainer').remove();
      destroyOverlay();
    });

    // handle OK button
    $('#mapChooseContainer #ok').click(function (e) {
      let center = map.getCenter();
      onOk({lat: center.lat, lng: center.lng});
    });

  }

  static popupShow(coord) {

    function nearlyEqual(a, b) {
      return Math.abs(a-b) < 0.000001;
    }

    function isMatchingMarker(marker) {
      return nearlyEqual(marker.getLatLng().lat, Number(coord.lat)) 
        && nearlyEqual(marker.getLatLng().lng, Number(coord.lng));
    }

    // find marker with matching coordinates
    let markers = mapShowMarkers.filter(isMatchingMarker);
    if (markers.length == 0) {
      console.warn("No map markers found for given coordinate");
    }
    else {
      mapShow._onResize();
      mapShow.setZoom(16);
      mapShow.panTo(markers[0].getLatLng());
    }

  }

}

CoordBlot.blotName = 'coord';
CoordBlot.tagName = 'span';
