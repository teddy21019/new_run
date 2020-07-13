let vRank = new Vue({
    el:"#rank",
    data:{
        ranks:[]
    },
    mounted(){
        this.getRankData();
    },
    methods:{
        getRankData(){
            $.post('php/functions/get_rank.php',{'action':'get'},function(result){
                result = JSON.parse(result);
                vRank.ranks = result;
            })
        },
        recalculateRank(){
            $.post('php/functions/get_rank.php',{'action':'get', 'recalc':true},function(result){
                result = JSON.parse(result);
                vRank.ranks = result;

            })  
        }
    },
    components:{
        'rank':{
            template:'#rank-template',
            props:['type-name','gender', 'datas']
        }
    }
});