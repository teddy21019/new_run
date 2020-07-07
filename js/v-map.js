let vMap = new Vue({
    el:"#map-block",
    data:{
        map:null,
        centerLocation:{
            lat:23.2055,
            lng:119.4295
        },
        zoom:14
    },
    mounted(){
        // this.initMap();
    },
    methods:{
        initMap(){
            this.map = new google.maps.Map(
                document.getElementById("map"),
                {
                    center:this.centerLocation,
                    zoom:this.zoom,
                    streetViewControl: false,
                    styles:[
                        {
                          "elementType": "labels",
                          "stylers": [
                            {
                              "visibility": "off"
                            }
                          ]
                        },
                        {
                          "featureType": "administrative.land_parcel",
                          "stylers": [
                            {
                              "visibility": "off"
                            }
                          ]
                        },
                        {
                          "featureType": "administrative.neighborhood",
                          "stylers": [
                            {
                              "visibility": "off"
                            }
                          ]
                        },
                        {
                            "featureType": "transit.line",
                            "stylers": [
                              {
                                "visibility": "off"
                              }
                            ]
                          }
                      ]
                }
            );
        }
    }
});