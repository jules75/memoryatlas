<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/map.css">
  <link rel="stylesheet" href="/css/preview.css">

  <ul id="entry_previews"></ul>

  <div id="map-large"></div>

  <script>

  var mapLastMovedTimestamp = 0;
  var shouldFetchPreviews = true;

  var markers = [];    


  function initMap() {

    let fetchWaitPeriod = 1000;

    // create map
    var map = L.map('map-large').setView([-37.56, 143.85], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    function createMarker(dataRow) {

      var marker = L.marker([dataRow.lat, dataRow.lng]).addTo(map);
      marker.entry_id = dataRow.entry_id;

      unhighlight(marker);
      markers.push(marker);

      marker.on('mouseover', onMarkerHover);
      marker.on('mouseout', onMarkerUnhover);
    }

    function highlight(marker) {
       marker.setOpacity(1.0);
    }

    function unhighlight(marker) {
       marker.setOpacity(0.7);
    }

    function findMarkerWithLatLng(lat, lng) {

      function f(m) {
        return (m.getLatLng().lat==lat && m.getLatLng().lng==lng);
      }

      return markers.filter(f)[0];
    }

    function onMarkerHover(e) {

      lat = e.target.getLatLng().lat;
      lng = e.target.getLatLng().lng;

      marker = findMarkerWithLatLng(lat, lng);
      highlight(marker);
      $(`#entry_previews [data-entry-id="${marker.entry_id}"]`).addClass('highlight');
    }

    function onMarkerUnhover(e) {
      markers.map(unhighlight);
      $('#entry_previews li').removeClass('highlight');
    }

    function onPreviewHover(e) {
      let li = e.target.parentElement.parentElement;
      let id = li.dataset.entryId;
      $(li).addClass('highlight');
      markers.filter((m) => m.entry_id==id).map(highlight);
    }

    function onPreviewUnhover(e) {
      let li = e.target.parentElement.parentElement;
      let id = li.dataset.entryId;
      $(li).removeClass('highlight');
      markers.map(unhighlight);
    }

    function onEntryReceived(result) {
      let item = $(`
          <li data-entry-id="${result.data.entry_id}" style="display:none;">
            <a href="/alpha/entry.php?entry_id=${result.data.entry_id}">
            <img src="${result.data.image_url}"></img>
            <span>${result.data.title}</span>          
            </a>
            </li>
            `);
      $('#entry_previews').append(item);
      $(item).hover(onPreviewHover, onPreviewUnhover);
      $(item).fadeIn();
      result.data.coords.map(createMarker);
    }

    function fetchEntryPreview(entryId) {
      let url = `/api/v1/entryPreview.php?id=${entryId}`;
      $.getJSON(url, onEntryReceived);
    }

    function onEntryIdsReceived(result) {
      $('#entry_previews').empty();
      markers.map(function(m) { map.removeLayer(m); });
      markers = [];
      result.data.entry_ids.map(fetchEntryPreview);
    }

    function onBoundsChange() {
      mapLastMovedTimestamp = Date.now();
      shouldFetchPreviews = true;
    }

    function checkMapStoppedMoving() {
      
      if (((Date.now() - mapLastMovedTimestamp) > fetchWaitPeriod) && shouldFetchPreviews) {

        console.log('fetching');

        shouldFetchPreviews = false;

        let b = map.getBounds();
        let url = `/api/v1/search/findEntriesByLatLng.php?north=${b.getNorth()}&east=${b.getEast()}&south=${b.getSouth()}&west=${b.getWest()}`;
        $.getJSON(url, onEntryIdsReceived);
      }

    }

    map.on('moveend', onBoundsChange);

    // periodically check if map has stopped moving for 2 seconds
    setInterval(checkMapStoppedMoving, 250);    
  
  }

  initMap();

  </script>

<?php include_once('_bottom.php'); ?>