let notificationURL = "php/functions/notification.php";

let vNotification = new Vue({
    el:"#notification-section",
    data:{
        notifications:[
        ]
    },
    mounted:function(){
        setInterval(function(){
            this.getNotifications();
        }.bind(this),1000)

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

                //get time only
                result = JSON.parse(result);
                vNotification.notifications = result.reverse();




            })
        },
        changeRead({id}){
            // this.notifications.find(n=>n.id==id)['is_read']= true;
            $.post(
                notificationURL,
                {
                    'action':'READ',
                    'id':id
                }).done(function(result){
                    // change isread property in items in notfications array
                    if(result == 'SUCCESS'){
                        vNotification.notifications.find(n=>n.id==id)['is_read']= true;
                    }else{
                        console.log(result);
                    }
                }).fail(function(error){
                    console.log(error)
                })
            
        },
        notificationClicked(data){
            vMap.showInfo(data);
            this.changeRead(data);

        }
    }
});