(function(window, Maperizer){

    $.widget( "map.maperizer", {
        // default options
        options: {},
 
        // the constructor
        _create: function() {
        var element = this.element[0],
            options = this.options;

        this.map = Maperizer.create(element, options);
        },

        //set the zoom level of the map
        setZoom: function(lvl){
          this.map.zoom(lvl);
        },

        //get current zoom level of the map
        getZoom: function(){
            return this.map.zoom();
        },

        // sets center of the map. either pass an object with lat and lng or location
        setCenter: function(pos){
            var self = this;
            if(pos.location) {
                self.map.geocode({
                    address: pos.location,
                    success: function (results) {
                        //first result
                        var result = results[0];
                        var lat = result.geometry.location.lat();
                        var lng = result.geometry.location.lng();
                        self.map.changePosition({
                            lat: lat,
                            lng: lng
                        });
                    },
                    error: function (status) {
                        console.error(status);
                    }
                });
            }else{
                this.map.changePosition(pos);
            }

        },

        //adds marker to the map, if enabled it's possible to set a geocoder- location
        addMarker: function( opts ) {
        var self = this;
        if(opts.location){
          self.map.geocode({
            address: opts.location,
            success: function(results){
              //first result
              var result = results[0];
              opts.lat = result.geometry.location.lat();
              opts.lng = result.geometry.location.lng();
              self.map.addMarker(opts);
            },
            error: function(status){
              console.error(status);
            }
          });
        }else{
          this.map.addMarker(opts);
        }

        },

        //attach events to the map, opts needs:
        //  array[
        //      {
        //      name: 'trigger',
        //      callback: function(e){}
        //  },
        // ]
        attachEventsToMap: function( opts ){
            this.map.attachEvents(this.map.gMap, opts);
        },

        //add array with opts to create markers
        addArray: function(array){
        var self = this;
        array.forEach(function(marker){
          self.map.addMarker(marker);
        });
        },
      
        //Show only selected markers
        //!!! problem with markerclusterer - hidden markers still get clustered!!!
        showOnly: function(callback){
        var all = this.map.markers;
        all.items.forEach(function(marker){
          marker.setVisible(false);
        });
        var selected = this.map.findBy(callback);
        selected.forEach(function(marker){
          marker.setVisible(true);
        });

        },

        //creates Marker and centers it (for detail view)
        addFocusedMarker: function(opts){
        this.map.addMarker(opts);
        this.map.changePosition(opts);
        },

        findMarkers: function(callback){
        return this.map.findBy(callback);
        },

        //removes markers with a certain criteria
        removeMarkers: function(callback){
        this.map.removeBy(callback);
        },

        //removes all markers
        removeAllMarkers: function(){
            this.map.removeBy(function(){
                return true;
            });
        },

        //returns all markers
        markers: function(){
        return this.map.markers.items;
        },

        getCurrentPosition: function(callback){
        this.map.getCurrentPosition(callback);
        },

        //returns object with lat and lng of the first marker in the list
        getPosition: function(){
        var marker = this.map.getAllMarkers()[0];
        var position ={
          lat:marker.lat,
          lng:marker.lng
        };
        return position;
        }

    });
 
}(window, Maperizer));