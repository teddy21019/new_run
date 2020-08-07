let vMap = new Vue({
  el: "#map-block",
  data: {
    map: null,
    centerLocation: {
      lat: 23.2055,
      lng: 119.4295
    },
    zoom: 14,
    stops: {},
    supplies: {},
    markers: [],
    infoWindow: null,
    infoWindowContent: {
      position: '',
      staff: '',
      staffTel: '',
      needTitle: '',
      need: [{
        name: '',
        quantity: '',
        unit: ''
      }],
    }
  },
  async mounted() {
    let stops = await this.getPosiions();
    this.stops = JSON.parse(stops);
    let supplies = await this.getSupplies()
    this.supplies = JSON.parse(supplies);
    this.initMap();
    // .then(positions => {
    //   this.stops = positions;
    // })
    console.log("runned");
  },
  methods: {
    initMap() {
      this.map = new google.maps.Map(
        document.getElementById("map"),
        {
          center: this.centerLocation,
          zoom: this.zoom,
          streetViewControl: false,
          styles: [
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
      );  //end of this.map

      //map marker Icon
      let restImg = (emt, run_type) => {
        let url;
        if (emt == 0) {
          if(run_type ==0){
            url = 'assets/icons/rest.png';
          }
          else if(run_type ==1){
            url = 'assets/icons/1_rest.png';
          }else if (run_type ==2){
            url = 'assets/icons/2_rest.png';
          }else if (run_type ==3){
            url = 'assets/icons/3_rest.png';
          }
          url = 'assets/icons/rest.png';
        } else if (emt == 1) {
          url = 'assets/icons/emt.png';
        }
        return (
          {
            url: url,
            ize: new google.maps.Size(30, 30),
            scaledSize: new google.maps.Size(30, 30)
          }
        );

      }

      //set markers from this.stop
      this.stops.forEach(stop => {
        let marker = new google.maps.Marker({
          position: {
            lat: parseFloat(stop.lat),
            lng: parseFloat(stop.lng)
          },
          map: this.map,
          animation: google.maps.Animation.DROP,
          title: this.name,
          icon: restImg(stop.emt, stop.run_type)
        })

        this.markers.push({ id: stop.id, marker: marker });

      });
    },
    getPosiions() {
      return ($.post('php/functions/get_staff_info.php',
        { action: 'all_position_info' }))
    },
    getSupplies() {
      return ($.post('php/functions/get_supply.php',
        { action: '1' }))
    },
    showInfo({
      position: {
        orig: posId
      },
      message: {
        orig: jsonMessage
      },
      staff: {
        show: staffName,
        tel: staffTel
      }
    }) {

      if (this.infoWindow != null) {
        this.infoWindow.close();
        google.maps.event.clearInstanceListeners(this.infoWindow);
        this.infoWindow = null;
      }

      let newNeeds = [];
      let needs = JSON.parse(jsonMessage);
      console.log(needs);
      let needTitle = '';

      let d = Object.keys(needs)[0];   //SUPPLY or MEDICAL
      if (d == 'SUPPLY') {
        needTitle = '物資';
        Object.keys(needs[d]).forEach(i => {  //for each 登記的物資
          // get name from item id
          let supplyItem = this.supplies.find(s => s.id == i)//某一項物資的名字
          newNeeds.push({
            name: supplyItem.name, quantity: needs[d][i], unit: supplyItem.unit
          })
        })
      } else if (d == 'MEDICAL') {
        needTitle = '傷患';
        newNeeds = null;
      }


      this.changeContentPromise({
        position: this.stops.find(s => s.id == posId).name,
        staffName,
        staffTel,
        needTitle,
        needs: newNeeds,
      }).then(() => {
        //補記站
        let anchor = this.markers.find(stop => stop.id == posId).marker;

        this.infoWindow = new google.maps.InfoWindow({
          content: $("#map-information").html()
        })

        this.infoWindow.open(this.map, anchor);

      });



      //get marker with that pos id



    },
    changeContentPromise(data) {
      return new Promise((resolve, reject) => {
        this.infoWindowContent = data;
        resolve();
      })
    }
  }
});