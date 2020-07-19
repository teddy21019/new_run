let vOverall = new Vue({
    el: "#overall",
    data: {
        position:{
            'id':1,
            'name':''
        },
        now: null,
        run_types: [],
        positionText : "所屬補給站"
    },
    async mounted() {
        this.run_types = await this.getRunInfo();
        this.timer();
        this.getPosition();
    },
    methods: {
        getRunInfo(){
            return new Promise(function(resolve){
                $.post('php/functions/get_run_type.php',{action:'started'},function(result){
                    result = JSON.parse(result)
                    result.forEach(rt => {
                        rt.start_time = rt.start_time.split(" ")[1];
                    });
                    resolve(result);
                })

            })

        },
        timer() {
            // const self=this;
            setInterval(function () {
                let now = new Date();
                this.now = [now.getHours(), now.getMinutes(), now.getSeconds()];
            }.bind(this), 30);
        },
        getPosition(){
            $.post('php/functions/get_position.php',{'action':'get'},function(result){
                result = JSON.parse(result);
                vOverall.position = result;
            })
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