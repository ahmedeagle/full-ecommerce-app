/*
 *  Maperizer.js - library for easy usage of Google Maps API
 *  Author: Simon Reinsperger
 *  URL: https://github.com/abisz
 *
 */

(function(window, google, List) {

  var Maperizer = (function() {
    function Maperizer(element, opts) {
      var self = this;

      this.gMap = new google.maps.Map(element, opts);
      this.markers = List.create();

      //Startposition from Map if geolocation is allowed and map options say so, start position will be geolocation of client. if one of the two requirements fails the location will fallback to center property in options
      if (navigator.geolocation && opts.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var lat = position.coords.latitude;
          var lng = position.coords.longitude;
          var newPosition = new google.maps.LatLng(lat, lng);
          self.gMap.setCenter(newPosition);
        });
      } else {
        var lat = opts.center.lat,
            lng = opts.center.lng;
        var startPosition = new google.maps.LatLng(lat, lng);
        self.gMap.setCenter(startPosition);
      }

      //SearchBox if set true in opts
        if(opts.searchbox) {
            var input = document.getElementById('pac-input');
            this.gMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            var searchBox = new google.maps.places.SearchBox(input);

            //EventListener
            google.maps.event.addListener(searchBox, 'places_changed', function () {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // For each place, get the icon, place name, and location.
                var searchMarkers = [];
                var bounds = new google.maps.LatLngBounds();

                for (var i = 0, place; place = places[i]; i++) {
                    var image = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    var searchMarker = new google.maps.Marker({
                        map: this.gMap,
                        icon: image,
                        title: place.name,
                        position: place.geometry.location
                    });

                    searchMarkers.push(searchMarker);

                    bounds.extend(place.geometry.location);
                }
                self.gMap.fitBounds(bounds);
            });

            google.maps.event.addListener(this.gMap, 'bounds_changed', function() {
                //console.log(this.getBounds());
                var bounds = this.getBounds();
                searchBox.setBounds(bounds);
            });
        }

        //check for cluster attribute in opts
      if (opts.cluster) {
        this.markerClusterer = new MarkerClusterer(this.gMap, [], opts.cluster.options);
      }

        //check for geocoder attribute in opts
      if (opts.geocoder) {
        this.geocoder = new google.maps.Geocoder();
      }
    }

      Maperizer.prototype = {
        // the zoom function serves to purposes: if there is a parameter, the map will zoom to the wanted level, if not it will return the current zoom level
      zoom: function(level) {
        if (level) {
          this.gMap.setZoom(level);
        } else {
          return this.gMap.getZoom();
        }
      },

        //geocode
      geocode: function(opts) {
        this.geocoder.geocode({
          address: opts.address
        }, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            opts.success.call(this, results, status);
          } else {
            opts.error.call(this, status);
          }
        });
      },

        //checks for geolocation and calls the callback with the position property from navigator.geolocation
      getCurrentPosition: function(callback) {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            callback.call(this, position);
          });
        }
      },

        //adds marker to the map need an option object with the following parts:
        //  position{ lat, lng}
        //  icon -> url to image (optional)
        //  content -> text for infowindow (optional)
        //  events -> additional events for the marker (requires events.name and events.callback)

      addMarker: function(opts) {
        var marker,
          self = this;

        opts.position = {
          lat: opts.lat,
          lng: opts.lng
        };

        //create Marker
        marker = this._createMarker(opts);

          //add marker to clusterer, if available
        if (this.markerClusterer) {
          this.markerClusterer.addMarker(marker);
        }

          //add marker to array
        this.markers.add(marker);

          //adds events (event object see _attachEvents())
        if (opts.events) {
          this.attachEvents(marker, opts.events);
        }
          //creates an infowindow with event to open it
        if (opts.content) {
          this._on({
            obj: marker,
            event: 'click',
            callback: function() {
              var infoWindow = new google.maps.InfoWindow({
                content: opts.content
              });
              infoWindow.open(this.gMap, marker);
            }
          });
        }

        return marker;
      },

        // function for adding evenlisteners
        // obj -> element for the event (e.g. marker, gMap, etc)
        // events -> object which must include:
        //      name -> name of trigger (click, dragend, etc.) for full list, visit: https://developers.google.com/maps/documentation/javascript/events
        //      callback -> what should be executed if triggered
      attachEvents: function(obj, events) {
        var self = this;
        events.forEach(function(event) {
          self._on({
            obj: obj,
            event: event.name,
            callback: event.callback
          });
        });
      },

        //private function to attach eventlistener to the map
        _on: function(opts) {
            var self = this;
            google.maps.event.addListener(opts.obj, opts.event, function(e) {
                opts.callback.call(self, e, opts.obj);
            });
        },


        // filter markers array by criteria
      findBy: function(callback) {
        return this.markers.find(callback);
      },

        //returns all markers
      getAllMarkers: function() {
        return this.markers.getAll();
      },

        // delete by criteria
        removeBy: function(callback) {
            var self = this;
            self.markers.find(callback, function(markers) {
                markers.forEach(function(marker) {

                    if (self.markerClusterer) {
                        self.markerClusterer.removeMarker(marker);
                    }
                    marker.setMap(null);
                    self.markers.remove(marker);

                });
            });
        },


        //private function to create a marker
      _createMarker: function(opts) {
        opts.map = this.gMap;
        return new google.maps.Marker(opts);
      },

        //changes position (viewpoint) of the map. Needs an object with lat and lng or location if geocoding is enabled
      changePosition: function(pos) {

        var newPos = new google.maps.LatLng(pos.lat, pos.lng);
        this.gMap.setCenter(newPos);
      }
    };

    return Maperizer;
  }());



    Maperizer.create = function(element, opts) {
        return new Maperizer(element, opts);
    };

  window.Maperizer = Maperizer;

}(window, google, List));