/**
 * Create and render the tabbed panel that displays the images, videos, places etc. from the entry document.
 */


function isMap(quillOp) {
    if (quillOp.hasOwnProperty('attributes')) {
        if (quillOp.attributes.hasOwnProperty('coord')) {
            return true;
        }
    }
    return false;
}

function nearlyEqual(a, b) {
    return Math.abs(a-b) < 0.000001;
}

function createMap() {

    let mapOps = quill.getContents().ops.filter(isMap);

    if (mapOps.length == 0) {
        return;
    }

    let mapDiv = $(`<div id="mapShow"></div>`);
    // let bounds = new google.maps.LatLngBounds();

    // create map
    $('#mapShowContainer').append(mapDiv);
    mapShow = L.map('mapShow').setView([-37.56, 143.85], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mapShow);


    function createMarker(quillOp) {

        var marker = L.marker([quillOp.attributes.coord.lat, quillOp.attributes.coord.lng]).addTo(mapShow);

        unhighlight(marker);
        mapShowMarkers.push(marker);

        marker.on('mouseover', onCoordOrMarkerHover);
        marker.on('mouseout', onCoordOrMarkerUnhover);        

        marker.bindTooltip(quillOp.insert);

        // bounds.extend(marker.getPosition());
    }

    // create markers, fit to bounds
    mapOps.map(createMarker);
    // mapShow.fitBounds(bounds);
}


function createImagePreview(i, el) {

    let url = el.dataset.url;
    let img = $(`<img src="${url}" class="preview"></img>`);

    $(img).css('height', '100px');
    $(img).click(function (e) {
        window.open(url, '_blank');
        // $.featherlight($(this)); // can't get featherlight working here??
    });

    $('#media-panel div.images').append(img);
}

function childNumber(element) {
    var i = 0;
    while ((element = element.previousElementSibling) != null) {
        i++;
    }
    return i;
}

function onHeadingClick(e) {
    
    // activate tab heading
    $('#media-panel li').removeClass("selected");
    $(e.target).addClass("selected");
    
    // hide all divs but one
    let n = childNumber(e.target);
    $(`#media-panel > div.wrapper > div`).hide();
    $(`#media-panel > div.wrapper > div:nth-of-type(${n+1})`).show();
}

function onImageHover(e) {
    let fn = (e.type=='mouseenter') ? 'addClass' : 'removeClass';
    let url = e.target.src || e.target.dataset.url;
     $(`#media-panel div.images img[src="${url}"], span[data-url="${url}"]`)[fn]('highlight');
}

function highlight(marker) {
    marker.setOpacity(1.0);
}

function unhighlight(marker) {
    marker.setOpacity(0.7);
}

function unhighlightAllMarkers() {
    mapShowMarkers.map(unhighlight);
}

function highlightMarker(lat, lng) {
    let markers = mapShowMarkers.filter(function (marker) {
        return (nearlyEqual(marker.getLatLng().lat, lat) && nearlyEqual(marker.getLatLng().lng, lng));
    });
    highlight(markers[0]);
}

function highlightCoordText(lat, lng) {
    let spans = $('span[data-tagtype="coord"]').filter(function (i, el) {
        return (nearlyEqual(el.dataset.lat, lat) && nearlyEqual(el.dataset.lng, lng));
    });
    $(spans[0]).addClass('highlight');    
}

function onCoordOrMarkerHover(e) {

    let lat, lng;

    if (e.hasOwnProperty('latlng')) {
        lat = Number(e.latlng.lat);
        lng = Number(e.latlng.lng);
    }
    else {
        lat = Number(e.target.dataset.lat);
        lng = Number(e.target.dataset.lng);
    }

    unhighlightAllMarkers();
    highlightMarker(lat, lng);
    highlightCoordText(lat, lng);
}

function onCoordOrMarkerUnhover(e) {
    unhighlightAllMarkers();
    $('span[data-tagtype="coord"]').removeClass('highlight');
}

function deleteEntry(e) {
    
    
    if (confirm("This will delete the entire entry. Continue?")) {
    
        let url = `/api/v1/entry.php?id=${urlPageId()}`;

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(result) {
                alert("Entry has been deleted");
                window.location.replace('/home.php');
            },
            error: function(result) {
                alert(result.responseText);
            }
        });

    }
}

function loadComments() {

    let userUrl = new URL(location.href);
    let entryId = userUrl.searchParams.get('entry_id');
    let apiUrl = `/api/v1/find/commentsByEntry.php?id=${entryId}`;

    $.getJSON(apiUrl, function (data) {
        console.log('ok', data);
      }).fail(function(data) {
        console.log('fail');
      }).always(function() {
        console.log('always');
      });
}

function renderMediaPanel() {

    $("#media-panel div.images").empty();
    $("#media-panel #mapShowContainer").empty();

    // add images
    $("span[data-tagtype='image']").each(createImagePreview);

    // add map
    createMap();

    loadComments();    

    // listen for heading clicks
    $('#media-panel li').click(onHeadingClick);

    // click first tab heading
    $('#media-panel li:first-child').click();

    // highlight img + text together
    $("#media-panel div.images img, span[data-tagtype='image']").hover(onImageHover);

    // coord text hover
    $("span[data-tagtype='coord']").hover(onCoordOrMarkerHover, onCoordOrMarkerUnhover);

    // listen for delete button
    $("button#delete").click(deleteEntry);

    // counters
    $("#image-count").text($("span[data-tagtype='image']").length);
    $("#coord-count").text($("span[data-tagtype='coord']").length);

}

