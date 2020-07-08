<?php
require_once 'core/init.php';


var_dump (DB::singleton()->selectSeveral('runner',$order=['id'=>'A'])->getResults());

// if(isset($_POST['action'])){
 
    
//     print_r(Runner::singleton()->search('測試')->getResults());

//     $start_time = DB::singleton()->select('run_type', ['id','=',1])->firstResult()->start_time;
//     $start_time = strtotime($start_time);
//     echo($start_time);
//     exit();
// }


// if (Input::exist()) {
//     $v = new Validate();
//     $v->check($_POST, array(
//         'number' => array(
//             Validate::REQUIRED => true,
//             Validate::UNIQUE => "runner/number",
//             Validate::MAX => 4,
//             Validate::TYPE=>Validate::NUMBER
//         ),
//         'name' => array(
//             Validate::REQUIRED => true,
//             Validate::MAX => 20,
//             Validate::MIN => 2


//         ),
//         'run_group' => array(
//             Validate::MAX => 10,
//         ),
//         'run_type'  => array(
//             Validate::EXIST=>"run_type/name"
//         )
//     ));

//     if (count($v->getError()) == 0) {
//         Runner::singleton()->add($_POST);
//         $_POST = [];

//     }else{
//         print_r ($v->getError());
//     }
// }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>測試用</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $.post("",{'action':'1'}, function(result){
                var serverTime = result;
                var timer = setInterval(function(){
                    console.log(serverTime);
                    var now = Date.now()/1000;
                    var distance = now-serverTime;

                    var hours = Math.floor((distance % ( 60 * 60 * 24)) / ( 60 * 60));
                    var minutes = Math.floor((distance % ( 60 * 60)) / (60));
                    var seconds = Math.floor((distance %  60 ));
                    var timeString = "";
                    // if((hours-8)!= 0){
                    //     timeString += (hours-8)+"時 ";
                    // }
                    timeString +=timeString += hours+"時 "+minutes+"分 "+seconds+"秒 "
                    $("#time").text(timeString);
                }, 1000)
            });
        });
    </script>
</head>
<body>
    <h1 id="time"></h1>
    <form action="" method="POST" id="t">
        <p>背號</p>
        <input type="text" name="number" value=<?php echo Input::get('number') ?>>
        <p>姓名</p>
        <input type="text" name="name" value=<?php echo Input::get('name') ?>>
        <p>跑團</p>
        <input type="text" name="run_group" value=<?php echo Input::get('run_group') ?>>
        <p>組別</p>
        <select name="run_type">
            <option value="挑戰組">挑戰組</option>
            <option value="樂活組">樂活組</option>
        </select>
        <br>      <br>
        <input type="submit" value="增加">
    </form>

    
</body>
</html>

