<?php include_once '_top.php'; ?>

  <div id="map-large">
  </div>

  <script async src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4&callback=initMap"></script>

  <script>

  function initMap() {

    let bounds = new google.maps.LatLngBounds();
    let markers = [];

    // create map
    map = new google.maps.Map(document.getElementById('map-large'), {
      center: { lat: -37.397, lng: 143.644 },
      zoom: 8
    });

    function createMarker(dataRow) {
     
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(dataRow.coord.lat, dataRow.coord.lng),
        map: map,
        title: dataRow.title
      });

      markers.push(marker);
      bounds.extend(marker.getPosition());
    }

    function onDataReceived(dataRows) {
      dataRows.map(createMarker);
      map.fitBounds(bounds);      
    }

    $.getJSON('/cache/coords.json', onDataReceived);
  }

  </script>

<?php include_once('_bottom.php'); ?>