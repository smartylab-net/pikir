
/**
 * Created by Admin on 25.11.13.
 */


function initializeAdmin(id,value) {
    var loc = getLocation(value);


    var mapOptions = {
        center: loc,
        zoom: value==null?10:17,
        draggableCursor: 'crosshair',
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
    };

    var map = new google.maps.Map(document.getElementById("map-canvas-"+id), mapOptions);

    var marker = new google.maps.Marker({
        position: loc,
        map: map
    });

    google.maps.event.addListener(map, 'click', function (event) {
        marker.setPosition(event.latLng);
        map.setCenter(event.latLng);
        $("#" + id).val(event.latLng);
    });

    //Associate the styled map with the MapTypeId and set it to display.
    var styledMap = getStyledMap();
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
}