<?php include_once '_top.php'; ?>

  <div id="map-large"></div>

  <ul id="entry_previews"></ul>

  <script async src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4&callback=initMap"></script>

  <script>

  function initMap() {

    var lastApiCallTimeStamp = 0;

    // create map
    map = new google.maps.Map(document.getElementById('map-large'), {
      center: { lat: -37.397, lng: 143.644 },
      zoom: 8
    });

    // function createMarker(dataRow) {
     
    //   var marker = new google.maps.Marker({
    //     position: new google.maps.LatLng(dataRow.coord.lat, dataRow.coord.lng),
    //     map: map,
    //     title: dataRow.title
    //   });

    //   markers.push(marker);
    //   bounds.extend(marker.getPosition());

    //   marker.addListener('click', function() {
    //     document.location.href = `/alpha/entry.php?entry_id=${dataRow.entry_id}`;
    //   });
    // }

    function onEntryReceived(result) {
      console.log(result);
      let item = $(`
          <li data-entry-id="${result.data.entry_id}">
            <a href="/alpha/entry.php?entry_id=${result.data.entry_id}">
            <img src="${result.data.image_url}"></img>
            <span>${result.data.title}</span>          
            </a>
            </li>
            `);
      $('#entry_previews').append(item);
    }

    function fetchEntryPreview(entryId) {
      let url = `/api/v1/entryPreview.php?id=${entryId}`;
      $.getJSON(url, onEntryReceived);
    }

    function onEntryIdsReceived(result) {
      $('#entry_previews').empty();
      result.data.entry_ids.map(fetchEntryPreview);
    }

    function onBoundsChange() {

      // avoid thrashing the server
      let threeSeconds = 2000;
      if ((Date.now() - lastApiCallTimeStamp) < threeSeconds) {
        return;
      }
      lastApiCallTimeStamp = Date.now();

      let bounds = map.getBounds();
      let north = bounds.getNorthEast().lat();
      let east = bounds.getNorthEast().lng();
      let south = bounds.getSouthWest().lat();
      let west = bounds.getSouthWest().lng();

      let url = `/api/v1/search/findEntriesByLatLng.php?north=${north}&east=${east}&south=${south}&west=${west}`;
      $.getJSON(url, onEntryIdsReceived);
    }

     google.maps.event.addListener(map, "bounds_changed", onBoundsChange);
  }

  </script>

<?php include_once('_bottom.php'); ?>