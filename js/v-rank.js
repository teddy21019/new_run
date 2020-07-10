let vRank = new Vue({
    el:"#rank-container",
    data:{},
    mounted(){
        this.getRankData();
    },
    methods:{
        getRankData(){
            $.post('php/functions/get_rank.php',{'action':'get'},function(result){
                /**
                 * result like:
                 */
                let obj = {
                    "0":{
                        'typeName':'半馬組',
                        'gender':'1',
                        'data':{
                            "0":{
                                'rank':1,
                                'name':'陳家威',
                                'number':'0284',
                                'time':'03:24:53'
                            },
                            "1":{
                                'rank':2,
                                'name':'陳家威',
                                'number':'0284',
                                'time':'03:24:53'
                            }

                        }
                    },
                    "1":{
                        'typeName':'半馬組',
                        'gemder':'0',
                        'data':{

                        }
                    }
                }
            })
        },
        recalculateRank(){
            $.post('php/functions/get_rank.php',{'action':'get', 'recalc':true},function(result){
            })  
        }
    },
    components:{
        'rank':{
            template:'#rank-template',
            props:['typeName', 'datas']
        }
    }
});