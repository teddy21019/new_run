let vOverall = new Vue({
    el: "#overall",
    data: {
        supplySpot:'家裡',
        now: null,
        run_types: [
            {
                name: "挑戰組",
                start_time: "10:32:23"
            },
            {
                name: "樂活組",
                start_time: "00:01:23"
            }
        ]
    },
    mounted() {
        this.timer();
    },
    methods: {
        timer() {
            // const self=this;
            setInterval(function () {
                let now = new Date();
                this.now = [now.getHours(), now.getMinutes(), now.getSeconds()];
            }.bind(this), 30);
            console.log(this.now)
        }
    },
    components: {
        'timer': {
            template: '#timer-template',
            props: ['run_type','now'],
            computed: {
                name: function () {
                    return this.run_type.name;
                },
                start_time: function () {
                    //data is like 02:02:02
                    return this.run_type.start_time;
                },
                pass_time: function () {
                    //same as main panel
                    if(this.now==null){
                        return;
                    }
                    let time = timeArrayToStr(
                        timeInterval(
                            timeStrToArray(this.start_time),
                            this.now)
                    )
                    return time;
                }
            }
        }
    }
})