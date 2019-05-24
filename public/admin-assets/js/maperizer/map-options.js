(function(window, google, maperizer) {

    maperizer.MAP_OPTIONS = {
        geolocation: true,
        center: {
            lat: 0,
            lng: 0
        },
        zoom: 7,
        searchbox: true,
        cluster: true,
        geocoder: true
    }


}(window, google, window.Maperizer || (window.Maperizer = {})));