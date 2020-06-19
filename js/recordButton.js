$(document).ready(function(){
    $("#record_button").click(function(){
        let number = $("#record_num").val();

        $.post("php/functions/record.php", {
            'action':'record',
            'number': number
        }, function(result){
            console.log(result);
            result = JSON.parse(result);

            let msg = result['msg'];
            if(msg === "NO_USER"){
                alert("無此跑者！");
            }
            else if(msg==="NOT_START")
            {
                alert(`${result['type']}尚未開始登記！`);
            }
            else if(msg==="SUCCESS"){
                alert(`恭喜${result['name']}以${result['run_time']}完成${result['run_type']}比賽！`);
            }
            else if(msg==="REPEAT"){
                alert(`${result['name']}已登記！以${result['run_time']}完成${result['run_type']}比賽！`);

            }
        });

    });

    $('#num').keypress(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            $("#but").trigger("click");
        }
    });

});