
var googlemap_geocoder;

window.addEvent( "domready" , googlemap_initialize );

function googlemap_initialize() {

    //Get maps
    var maps     = document.getElements('.ym-form-map');
    var maps_len = maps.length;

    for( var i = 0 ; i < maps_len ; i++ ){
      googlemap_prepareMaps( maps[ i ] );
    }

    googlemap_geocoder = new google.maps.Geocoder();
}

function googlemap_prepareMaps( mapDiv ){

    //Get the nessted name
    var prefix       = mapDiv.get('id');
    var lat_el       = $(prefix + '_lat');
    var lng_el       = $(prefix + '_lng');
    var zoom_el      = $(prefix + '_zoom');    
    var lat          = (!lat_el.get('value')  ? mapDiv.get('lat')  : lat_el.get('value')) - 0;
    var lng          = (!lng_el.get('value')  ? mapDiv.get('lng')  : lng_el.get('value')) - 0;
    var zoom         = (!zoom_el.get('value') ? mapDiv.get('zoom') : zoom_el.get('value')) - 0;
    
    var myOptions = {
        zoom:      zoom ,
        center:    new google.maps.LatLng( lat, lng ),
        mapTypeId: google.maps.MapTypeId.HYBRID
    }
    var map = new google.maps.Map(mapDiv, myOptions);

    if (lat && lng) {
        googlemap_addMarker(map, new google.maps.LatLng(lat, lng) );
        map.setZoom(18);
    }
    google.maps.event.addListener(map, 'click', function(event) {
        googlemap_addMarker(map, event.latLng);
    });

    mapDiv.store('googlemap', map);
    mapDiv.getParent('.ym-form-box').addEvent( 'dependshow' , function( ){
        google.maps.event.trigger(map, 'resize');
    });

}

  function googlemap_addMarker(map, location) {

      // Get elements
      var mapDiv  = map.getDiv();
      var prefix  = mapDiv.get('id');
      var lat_el  = $(prefix + '_lat');
      var lng_el  = $(prefix + '_lng');
      var zoom_el = $(prefix + '_zoom');      
      var lat     = location.lat();
      var lng     = location.lng();
      var zoom    = map.getZoom();
      
      // Update the coordinates
      lat_el.value = lat;
      lng_el.value = lng;
      zoom_el.value = zoom;

      // Get and Create Marker
      var googlemap_marker = mapDiv.retrieve('googlemap_marker');
      
      if (googlemap_marker)
          googlemap_marker.setMap(null)

      googlemap_marker = new google.maps.Marker({
          position: location,
          map: map ,
          draggable: true
      });

      mapDiv.store('googlemap_marker', googlemap_marker);
      googlemap_setCenter(map, location);
  }

  function googlemap_setCenter(map, location) {
      map.setCenter(location);
  }

  function googlemap_setZoom(map, zoom) {
      map.setZoom(zoom);
  }

  function googlemap_hasMarker() {
      return (googlemap_marker);
  }

  function googlemap_boundingCoordinates(location, distance) {

  	  var radLat = location.lat() * (Math.PI / 180);
  	  var radLon = location.lng() * (Math.PI / 180);;

      var earthRadius = 6371.3929;
      var radius      = earthRadius;
      distance        = distance * 1.609344; // Convert to Miles

	  var MIN_LAT = -1 * Math.PI / 2; // -PI/2
	  var MAX_LAT = Math.PI / 2;      //  PI/2
	  var MIN_LON = -1 * Math.PI;     // -PI
	  var MAX_LON = Math.PI;          //  PI

      // angular distance in radians on a great circle
	  var radDist = distance / radius;
      var minLat = radLat - radDist;
	  var maxLat = radLat + radDist;

      if (minLat > MIN_LAT && maxLat < MAX_LAT) {
          var deltaLon = Math.asin(Math.sin(radDist) / Math.cos(radLat));

		  var minLon = radLon - deltaLon;
          if (minLon < MIN_LON)
              minLon += 2 * Math.PI;

		  var maxLon = radLon + deltaLon;
		  if (maxLon > MAX_LON)
		      maxLon -= 2 * Math.PI;

		}
		else {
			// a pole is within the distance
            minLat = Math.max(minLat, MIN_LAT);
			maxLat = Math.min(maxLat, MAX_LAT);

			var minLon = MIN_LON;
			var maxLon = MAX_LON;
		}

        minLon = minLon * (180 / Math.PI);
        maxLon = maxLon * (180 / Math.PI);
        minLat = minLat * (180 / Math.PI);
        maxLat = maxLat * (180 / Math.PI);

        var minLatLng = new google.maps.LatLng(minLat, minLon);
        var maxLatLng = new google.maps.LatLng(maxLat, maxLon);

        return new google.maps.LatLngBounds(minLatLng, maxLatLng);
  }

  //http://code.google.com/apis/maps/documentation/geocoding/
  function googlemap_codeAddress(map, road, unit, address, lat, lon, coordinatesType, addCoordinate) {

      var search_address = '';
      var geocode_data   = {};

      // Address Format
      if (address)
          search_address = road + ' ' + unit + ' ' + address;
      
      // Bounds only neighborhood
      if ( coordinatesType == 'neighborhood' ) {

          if ( addCoordinate )
              search_address = search_address + ' ' + lat + ' ' + lon;

          var location           = new google.maps.LatLng(lat, lon);
          geocode_data['bounds'] = googlemap_boundingCoordinates(location, 1);
      }

      geocode_data['address'] = search_address.trim();

      googlemap_geocoder.geocode( geocode_data, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            
              var location      = results[0].geometry.location;
              var location_type = results[0].geometry.location_type;

              // If we have the neighborhood coords and the result coords distance to the neighborhood is more than one mile, then we use the neighborhood coords as result
              if ( coordinatesType == 'neighborhood' ) {

                  var location_distance = ((Math.acos(Math.sin(lat * Math.PI / 180) * Math.sin(location.lat() * Math.PI / 180) + Math.cos(lat * Math.PI / 180) * Math.cos(location.lat() * Math.PI / 180) * Math.cos((lon - location.lng()) * Math.PI / 180)) * 180 / Math.PI) * 60 * 1.1515);
                  if ( location_distance > 1 ) {
                      location      = new google.maps.LatLng(lat, lon);
                      location_type = google.maps.GeocoderLocationType.GEOMETRIC_CENTER;
                  }
              }

              map.setCenter( location );

              switch( location_type ){
                  case google.maps.GeocoderLocationType.ROOFTOP:
                    map.setZoom(19);
                    break;
                  case google.maps.GeocoderLocationType.RANGE_INTERPOLATED:
                    map.setZoom(18);
                    break;
                  case google.maps.GeocoderLocationType.GEOMETRIC_CENTER:
                    map.setZoom(17);
                    break;
                  default:
                    map.setZoom(16);
                    break;
              }

          } else {

              if ( address && addCoordinate)
                  googlemap_codeAddress(map, road, unit, address, lat, lon, coordinatesType, false);
              if ( road && unit )
                  googlemap_codeAddress(map, road, '', address, lat, lon, coordinatesType, false);
              else if ( road && coordinatesType == 'neighborhood' )
                  googlemap_codeAddress(map, '', '', '', lat, lon, coordinatesType, true);
              else if ( road )
                  googlemap_codeAddress(map, '', '', address, lat, lon, coordinatesType, false);
              else
                  return;
          }
      });
  }
