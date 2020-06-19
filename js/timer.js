$(document).ready(function () {
    for (let i = 1; i <= 2; i++) {
        $.post("php/functions/time.php", { 'type': i }, function (result) {
            if (result != "") {

                $("#" + i + "_start_btn").attr('disabled', true);
                $("#" + i + "_reset_btn").attr('disabled', false);

                $("#" + i + "_reset_btn").click(function () {

                    let confirmResult = confirm("確定要重製？");
                    if (confirmResult) {

                        $.post("php/functions/start.php",
                            {
                                'action': 'reset',
                                'type': i
                            },
                            function (result) {
                                console.log(result);

                                if (result == "SUCCESS") {
                                    location.reload();
                                }
                            }
                        )

                    }
                });


                result = JSON.parse(result);
                // console.log(result);
                $("#" + i + "_start_time").text(result[0].split(" ")[1]);
                var timer = setInterval(function () {
                    var serverTime = result[1];
                    var now = Date.now() / 1000;
                    var distance = now - serverTime;
                    $("#" + i + "_passed_time").text(timeToString(distance));
                }, 1000);
            } else {
                $("#" + i + "_reset_btn").attr('disabled', true);

                $("#" + i + "_start_btn").click(function () {

                    let confirmResult = confirm("確定要開始？");
                    if (confirmResult) {


                        $.post("php/functions/start.php",
                            {
                                'action': 'start',
                                'type': i
                            },
                            function (result) {
                                console.log(result);
                                if (result == "404_STARTED") {
                                    alert("已開始！")
                                } else if (result == "SUCCESS") {
                                    location.reload();
                                }
                            });
                    }
                })
            }
        });






    }



});

function timeToString(timeTxt){
    var hours = Math.floor((timeTxt % (60 * 60 * 24)) / (60 * 60));
    var minutes = Math.floor((timeTxt % (60 * 60)) / (60));
    var seconds = Math.floor((timeTxt % 60));
    var timeString = "";
    timeString += timeString += hours + ":" + minutes + ":" + seconds ;
    return timeString;
}