let notificationURL = "php/functions/notification.php";

let vNotification = new Vue({
    el:"#notification-section",
    data:{
        notifications:[
            {
                id:1,
                time:'12:00',
                pos:'小台灣',
                mes:'物資－水',
                is_read:false
            }
        ]
    },
    mounted:function(){
        this.getNotifications();

    },
    methods:{
        getNotifications(){
        /**
         * Get Data : notifications
         *    ----result-----
         * id           int         4  
         * time         string      "2020-03-26  12:34:56"
         * postion     id          3   (name/lonlat get from positions array)
         * message      string      {'SUPPLY':{     
         *                              3:4,
         *                              5:1
         *                           }}
         */

         //positions are obtained in 'staff.js', so is staffs
         $.post(
            notificationURL,
            {
                'action':'PULL'
            },
            function(result){
                result = JSON.parse(result);
                Object.keys(result).forEach(i=>{
                    //handle time array
                    let item  = result[i]
                    item.time = item.time.split(" ")[1];

                    let message = item.message
                    //handle message
                    Object.keys(message).forEach(j=>{
                        if (j =='SUPPLY'){
                            item.message = "物資";
                            Object.keys(message[j]).forEach(supplyId=>{
                                //get supply name from database
                            })

                            
                        }
                    })

                })
                //get time only
                console.log(result);

                //get position 



            })
        },
        changeRead(id){
            // this.notifications.find(n=>n.id==id)['is_read']= true;
            $.post(
                notificationURL,
                {
                    'action':'READ',
                    'id':id
                }).done(function(result){
                    // change isread property in items in notfications array
                    if(result == 'SUCCESS'){
                        this.notifications.find(n=>n.id==id)[is_read]= true;
                    }else{
                        console.log(result);
                    }
                }).fail(function(error){
                    console.log(error)
                })
            
        },
        notificationClicked(data){
            this.changeRead(data.id);

        }
    }
});