$(document).ready(function(){
    $("#record_button").click(function(){
        let number = $("#record_num").val();

        $.post("php/functions/record.php", {
            'action':'record',
            'number': number
        }, function(result){
            result = JSON.parse(result);

            let msg = result['msg'];
            if(msg === "NO_USER"){
                showMessageInGreyBox("無此跑者！");
            }
            else if(msg==="NOT_START")
            {
                showMessageInGreyBox(`${result['type']}尚未開始登記！`)
            }
            else if(msg==="SUCCESS"){
                showMessageInGreyBox(`恭喜${result['name']}以${result['run_time']}完成${result['run_type']}比賽！`);
            }
            else if(msg==="REPEAT"){
                showMessageInGreyBox(`${result['name']}已登記！以${result['run_time']}完成${result['run_type']}比賽！`);

            }
        });

    });

    $('#record_num').keypress(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            $("#record_button").trigger("click");
        }
    });

});

function showMessageInGreyBox(msg){
    $("#record_msg").text(msg);
    $("#record_msg_box").animate({opacity:'100%'},500).delay(3000).animate({opacity:0},500);
    $("#record_num").val('');

}