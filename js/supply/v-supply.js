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
        ]
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
            let itemToSend = this.items.filter(item=>item['count']!==0)
            itemToSend = itemToSend.map(item=>({
                id:item.id,
                count:item.count
            }));

            //send to server
            console.log(itemToSend);

            //set all componet count to 0
            this.items.forEach(item => {
                item.count = 0;
            });
                            
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


