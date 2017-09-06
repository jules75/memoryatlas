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


function createImagePreview(i, el) {

    let url = el.dataset.url;
    let img = $(`<img src="${url}" class="preview"></img>`);

    $(img).css('height', '200px');
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
    $(`#media-panel > div`).hide();
    $(`#media-panel > div:nth-of-type(${n+1})`).show();

    // force map redraw
    google.maps.event.trigger(mapShow, 'resize')
}

function renderMediaPanel() {

    // create tab headings
    var headings = `<ul>
        <li>Images</li>
        <li>Map</li>
        <li>YouTubes</li>        
        <li>Dates</li>
        </ul>`;
    $('#media-panel').append(headings);

    // create image container, add images
    var container = $('<div class="images"></div>');
    $('#media-panel').append(container);
    $("span[data-tagtype='image']").each(createImagePreview);

    // create map container, add map
    var container = $('<div id="map-container"></div>');
    $('#media-panel').append(container);
    createMap();

    // listen for heading clicks
    $('#media-panel li').click(onHeadingClick);

    // click first tab heading
    $('#media-panel li:first-child').click();

}

