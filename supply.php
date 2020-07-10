<?php
require_once 'php/core/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="screen" href="css/sidebar.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/supply.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <script src="js/functions/function.js"></script>

    <title>路跑補給系統</title>
    <style></style>
</head>



<script type="text/x-template" id="supply-template">
    <div class="grid-column-content">
        <div v-if="count!=0" class="quantity-bubble">
            <div style="color:white">{{count}}</div>
        </div>
        <div class="content-block">
            <div class="title"><div>{{name}}</div></div>
            <div @click="add" class="quant-change add" > <span>+</span></div>
            <div @click="minus" class="quant-change minus"><span>-</span></div>
        </div>
    </div>
</script>

<script type="text/x-template" id="timer-template">
    <div class="timer-content">
        <div id="type-name">
            <div>{{name}}</div>
        </div>
        <div id="start-time">
            <div><span>開始時間</span></div>
            <div>{{start_time}}</div>
        </div>
        <div id="pass-time">
            <div><span>經過時間</span></div>
            <div>{{pass_time}}</div>
        </div>
    </div>
</script>

<?php if (Session::get('user')=='supply'){?>
<body>
    <div class="sidebar">
        <img src="logo.png" id="sidebar-logo">
        <div><span>路跑補給系統</span></div>
        <ul>
            <li> <a href="#">主畫面</a>  </li>
            <li> <a href="#">設定</a> </li>
            <li> <a href="php/logout.php">登出</a> </li>
        </ul>
    </div>
    <div class="main" id="app">
        <div>
            <div class="supply-title">
                <div>
                    <h2>物資需求</h2>
                </div>
                <div id="send-supply"> <button @click="send">送出</button></div>
            </div>
            <div class="grid-column" >
                <!-- 從伺服器抓資料，主會場可編輯名單 -->
                <item v-for="item in items" :item="item"></item>
            </div>
        </div>
    </div>
    <div class="overall" id="overall">
        <h2>路跑總覽</h2>
        <div id="position">
            <div>
                <h4>所屬補給站：<span>{{position.name}}</span></h4>
                
            </div>
        </div>
        <div id="timers">
            <timer v-for="type in run_types" :run_type="type" :now="now"></timer>
        </div>
    </div>

</body>

<?php }else{
    header("Location: login.php");

} ?>
<script src="js/supply/v-overall.js"></script>
<script src="js/supply/v-supply.js"></script>


</html>