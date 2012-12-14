$(function () {
    'use strict';
    var fields = $('#address_lat, #address_lng'),
        map, place;

    // MAP
    map = new google.maps.Map($('#map_canvas')[0], {
        center: new google.maps.LatLng(-30.03, -51.23), //poa
        zoom: 8,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    google.maps.event.addListener(map, 'click', function(location) {
        place.add(location.latLng);
    });

    // PLACE
    place = (function () {
        var marker = new google.maps.Marker({ map: map, draggable: true, visible: false }),
            latLng = false;

        google.maps.event.addListener(marker, 'dragend', function (location) {
             place.add(location.latLng);
        });

        return {
            center: function () {
                latLng && map.setCenter(latLng);
            },
            add: function (value, center) {
                latLng = value;
                marker.setPosition(latLng);
                center && place.center();
                fields[0].value = latLng.lat().toFixed(4);
                fields[1].value = latLng.lng().toFixed(4);
                marker.setVisible(true);
            },
            remove: function () {
                fields.val('');
                latLng = false;
                marker.setVisible(false);
            },
            fields: function () {
                var value = new google.maps.LatLng(fields[0].value, fields[1].value);
                console.log(value.toString());
                if (!value.toString().match(/NaN|\(0, 0\)/)) {
                    place.add(value);
                    place.center();
                } else {
                    place.remove();
                }
            }
        }
    })();
    place.fields();

    // INTERFACE
    $('#address-search').click(function () {
        var error = $('#address-error'),
            search = { address: $('#address').val() };

        new google.maps.Geocoder().geocode(search, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                place.add(results[0].geometry.location, true);
            } else {
                error.html('<span>Localização não encontrada.</span>');
                $(window).one('click', function () {
                    error.html('');
                })
            }
        });
    });
    $('#address-latlng').click(place.fields);
    $('#address-center').click(place.center);
    $('#address-remove').click(place.remove);
});