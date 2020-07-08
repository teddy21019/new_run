<?php
require_once 'php/core/init.php';
?>

<!DOCTYPE html>
<html> 
    <head>
        <title>路跑手動登錄系統</title>
        <meta name="viewport" content="width=device-width, height=device-height initial-scale=1"/>
        <link rel="stylesheet" type="text/css" media="screen" href="css/record.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="js/recordButton.js"></script>
        <script>
            
        </script>
    </head>

    <?php
    if (Session::get('user') == 'recorder' || Session::get('user') == 'admin') { ?>
    <body>
    <div style="float:right">
        <a href="php/logout.php">登出</a>
    </div>        
    <div class="center">
        <div style="opacity:0" id="record_msg_box">
            <span id="record_msg">1039登錄成功</span>
        </div>
        <img class="logo" src="logo.png">
        <span class="finish-h">完賽記錄</span>
        <input id="record_num" type="number" placeholder="請輸入跑者ID">
        <button id="record_button">登錄</button>
        <span>製作 陳家威</span>
        </div>

    </body>
    <?php 
    }else{
        header("Location: login.php");
    }?>



</html>