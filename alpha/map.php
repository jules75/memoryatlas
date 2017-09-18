<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/map.css">
  <link rel="stylesheet" href="/css/preview.css">

  <ul id="entry_previews"></ul>

  <div id="map-large"></div>

  <script async src="//maps.googleapis.com/maps/api/js?key=AIzaSyD4vbKcoEyAUOT9Ql4ydk-L8OlEEq5dJW4&callback=initMap"></script>

  <script>

  var mapLastMovedTimestamp = 0;
  var shouldFetchPreviews = true;

  var markers = [];    


  function initMap() {

    let fetchWaitPeriod = 1000;

    // create map
    map = new google.maps.Map(document.getElementById('map-large'), {
      center: { lat: -37.56, lng: 143.85 },
      zoom: 14
    });

    function createMarker(dataRow) {

      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(dataRow.lat, dataRow.lng),
        map: map,
        opacity: 0.65,
        entry_id: dataRow.entry_id,
        title: ''
      });

      markers.push(marker);
      marker.addListener('mouseover', onMarkerHover);
      marker.addListener('mouseout', onMarkerUnhover);
    }

    function highlight(marker) {
       marker.setOptions({opacity: 1.0});
    }

    function unhighlight(marker) {
       marker.setOptions({opacity: 0.65});
    }

    function findMarkerWithLatLng(lat, lng) {

      function f(m) {
        return (m.position.lat()==lat && m.position.lng()==lng);
      }

      return markers.filter(f)[0];
    }

    function onMarkerHover(e) {
      
      lat = e.latLng.lat();
      lng = e.latLng.lng();
      
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
      markers.map(function(m) { m.setMap(null); });
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

        let bounds = map.getBounds();
        let north = bounds.getNorthEast().lat();
        let east = bounds.getNorthEast().lng();
        let south = bounds.getSouthWest().lat();
        let west = bounds.getSouthWest().lng();

        let url = `/api/v1/search/findEntriesByLatLng.php?north=${north}&east=${east}&south=${south}&west=${west}`;
        $.getJSON(url, onEntryIdsReceived);
      }

    }

    google.maps.event.addListener(map, "bounds_changed", onBoundsChange);

    // periodically check if map has stopped moving for 2 seconds
    setInterval(checkMapStoppedMoving, 250);
  }

  </script>

<?php include_once('_bottom.php'); ?>