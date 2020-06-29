Vue.component('timer',
    {
        template: "#timer_template",
        props: ['type'],
        data:function(){
            return {
                name:this.type.name,
                now:null
            }
        },
        mounted: function(){
            this.timer();
        },
        methods: {
            start() {
                let id = this.type.id;
                let confirmResult = confirm("確定要開始？");
                    if (confirmResult) {
                        $.post("php/functions/start.php",
                            {
                                'action': 'start',
                                'type': id
                            },
                            function (result) {
                                console.log(result);
                                if (result == "404_STARTED") {
                                    alert("已開始！")
                                } else if (result == "SUCCESS") {
                                    location.reload();
                                }
                            });
                    }
             },
            reset() { 
                let id = this.type.id;
                let confirmResult = confirm("確定要重製？");
                    if (confirmResult) {

                        $.post("php/functions/start.php",
                            {
                                'action': 'reset',
                                'type': id
                            },
                            function (result) {
                                console.log(result);

                                if (result == "SUCCESS") {
                                    location.reload();
                                }
                            }
                        )

                    }
            },
            timer(){
                // const self=this;
                setInterval(function(){
                    let now = new Date();
                    this.now = [now.getHours(), now.getMinutes(), now.getSeconds()];
                }.bind(this),30);
            }
        },
        computed:{
            start_time:function(){
                return this.type.start_time;
            },
            pass_time: function(){

                if(this.now==null){
                    return
                }
                if(this.type.started==true){
                    let time = timeArrayToStr(
                        timeInterval(
                            timeStrToArray(this.start_time), 
                                this.now)
                    )
                    return time;
                }else{
                    return "";
                }
            }
        }


    });


var app = new Vue({
    el: "#app",
    data: {
        run_types: [
            {
                id: 1,
                name: "挑戰組",
                start_time: "12:12:12",
                pass_time: "01:00:00",
                started: true

            },
            {
                id: 2,
                name: "樂活組",
                start_time: "12:12:12",
                pass_time: "01:00:00",
                started: false
            }
        ],
    },
    created: function () {
        //get data from server: 
        this.run_types = [];    //clear data for testing
        $.post("php/functions/get_run_type.php", { 'action': 1 }, function (result) {
            result = JSON.parse(result);
            
            //get time now



            result.forEach(type => {
                let newType = {};
                newType.id = type['id'];
                newType.name = type['name'];
                if(type['started']!=0){     //boolean in database is "0", string
                    newType.started = true;
                    newType.start_time = type['start_time'].split(" ")[1];

                    
                }else{
                    newType.started = false;
                    newType.start_time = "";
                    newType.pass_time = "";
                }
                console.log(newType);   
                app.run_types.push(newType);

                //also change option in runner table
                run_types[type['id']]=type['name'];
                $("#r_run_type").append(`<option value="${type['id']}">${type['name']}</option>`)
            });
        })

    },

    computed:{
        
    }

    
});


function timeInterval(time1, time2) {
    //time = [12, 34, 56]

    //time 1 to second
    let time1Second = 60 * 60 * time1[0] +
    60 * time1[1] +
    time1[2];
    let time2Second = 60 * 60 * time2[0] +
    60 * time2[1] +
    time2[2];
    
    timeDiff = (time1Second - time2Second)
    timeDiff *= (timeDiff < 0) ? -1 : 1;
    
    //back to array
    
    let sec = timeDiff%60 ;
    let min = Math.floor(timeDiff/60)%60;
    let hour = Math.floor(timeDiff/(60*60))%60;
    return [hour, min, sec];
}

function timeStrToArray(str) {
    //str = "12:34:56"
    //to
    // [12, 34, 56]
    return str.split(':').map(val=>parseInt(val));
}

function timeArrayToStr(arr){
    arr =  arr.map(val=>val.toString().padStart(2, "0"));
    return `${arr[0]}:${arr[1]}:${arr[2]}`;
}

