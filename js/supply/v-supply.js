let bus = new Vue();
let vSupply = new Vue({
    el: "#app",
    data: {
        items: [
            {
                id: 1,
                name: "水",
                count:0
            }, {
                id: 2,
                name: "舒跑",
                count:0
            }, {
                id: 3,
                name: "杯子",
                count:0
            }, {
                id: 4,
                name: "香蕉",
                count:0
            }, {
                id: 5,
                name: "海綿",
                count:0
            }
        ],
        position:{
            id:4,
            name:'小台灣'
        }
    },
    created() {
        bus.$on('countChange', (event) => {
            this.changeCount(event);
        })
    },
    methods: {
        changeCount: function (d) {
            let id = d.id;
            this.items.find(item => item.id === id).count = d.count;

        },
        send:function(){
            let itemToSend_array = this.items.filter(item=>item['count']!==0)

            let itemToSend_Obj={};
            itemToSend_array.forEach(item => {
                itemToSend_Obj[item['id']]=item['count'];
            });

            


            //send to server
            let sendData = {
                'action':'PUSH',
                'position':this.position.id,
                'message':JSON.stringify({
                    'SUPPLY':itemToSend_Obj
                }),
            }
            $.post('php/functions/notification.php',sendData,function(result){
                if (result == 'SUCCESS'){
                    vSupply.items.forEach(item => {
                        item.count = 0;
                    });
                }
            })

            //set all componet count to 0
            
                            
        }
    },
    components: {
        'item': {
            template: "#supply-template",
            props: ['item'],
            data: function () {
                return {
                }
            },
            computed: {
                name: function () {
                    return this.item.name;
                },
                count:function(){
                    return this.item.count;
                }

            },

            methods: {
                add: function () {
                    this.item.count++;
                    this.emitBus();
                },
                minus: function () {
                    if (this.item.count <= 0) {
                        return;
                    } else {
                        this.item.count--;
                        this.emitBus();
                    }
                },
                emitBus: function () {
                    bus.$emit('countChange', {
                        id: this.item.id,
                        count: this.item.count
                    });
                }
            }
        }
    }
})


