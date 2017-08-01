'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Inline = Quill.import('blots/inline');

var BoldBlot = function (_Inline) {
  _inherits(BoldBlot, _Inline);

  function BoldBlot() {
    _classCallCheck(this, BoldBlot);

    return _possibleConstructorReturn(this, (BoldBlot.__proto__ || Object.getPrototypeOf(BoldBlot)).apply(this, arguments));
  }

  return BoldBlot;
}(Inline);

BoldBlot.blotName = 'bold';
BoldBlot.tagName = 'strong';

var ItalicBlot = function (_Inline2) {
  _inherits(ItalicBlot, _Inline2);

  function ItalicBlot() {
    _classCallCheck(this, ItalicBlot);

    return _possibleConstructorReturn(this, (ItalicBlot.__proto__ || Object.getPrototypeOf(ItalicBlot)).apply(this, arguments));
  }

  return ItalicBlot;
}(Inline);

ItalicBlot.blotName = 'italic';
ItalicBlot.tagName = 'em';

/**
 * COORD
 */

var CoordBlot = function (_Inline3) {
  _inherits(CoordBlot, _Inline3);

  function CoordBlot() {
    _classCallCheck(this, CoordBlot);

    return _possibleConstructorReturn(this, (CoordBlot.__proto__ || Object.getPrototypeOf(CoordBlot)).apply(this, arguments));
  }

  _createClass(CoordBlot, null, [{
    key: 'create',
    value: function create(coord) {
      var node = _get(CoordBlot.__proto__ || Object.getPrototypeOf(CoordBlot), 'create', this).call(this);
      node.setAttribute('data-tagtype', 'coord');
      node.setAttribute('data-lat', coord.lat());
      node.setAttribute('data-lng', coord.lng());
      return node;
    }
  }, {
    key: 'formats',
    value: function formats(node) {
      var lat = Number(node.getAttribute('data-lat'));
      var lng = Number(node.getAttribute('data-lng'));
      return { lat: lat, lng: lng };
    }
  }, {
    key: 'popupUI',
    value: function popupUI(onCoordOk, onCoordCancel) {

      var mapDiv = $('\n    <div id="mapContainer">\n      <div id="map"></div>\n      <div class="buttonRow">\n        <button id="ok">OK</button>\n        <button id="cancel">Cancel</button>\n        </div>\n    </div>\n    ');

      // create map
      $('body').append(mapDiv);
      var map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -37.397, lng: 143.644 },
        zoom: 8
      });

      // close on any button
      $('#mapContainer button').click(function (e) {
        $('#mapContainer').remove();
      });

      // handle OK button
      $('#mapContainer #ok').click(function (e) {
        onCoordOk(map.getCenter());
      });
    }
  }]);

  return CoordBlot;
}(Inline);

CoordBlot.blotName = 'coord';
CoordBlot.tagName = 'span';

Quill.register(BoldBlot);
Quill.register(ItalicBlot);
Quill.register(CoordBlot);

var quill = new Quill('#editor-container');

// button listeners
$('#bold-button').click(function () {
  quill.format('bold', true);
});
$('#italic-button').click(function () {
  quill.format('italic', true);
});
$('#coord-button').click(function () {

  CoordBlot.popupUI(function (gLatLng) {
    quill.format('coord', gLatLng);
  });
});

function initMap() {
  console.log("initMap()");
}

