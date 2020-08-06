<?php 
require_once 'php/core/init.php';
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>主控台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="css/panel.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/sidebar.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTjZ3TVlMlS1qet1-hCioKdBuInaY4IWc">
    </script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js"> -->
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <script src="js/functions/function.js"></script>
    <script src="js/tab.js"></script>
    <!-- <script src="js/timer.js"></script> -->
    <script src="js/runner.js"></script>
    <script src="js/staff.js"></script>
    <script src="js/recordButton.js"></script>





</head>

<script type="text/x-template" id="timer_template">
    <div class="timer-grid ">
        <div class="timer_left">
            <div class="timer_title">
                {{name}}
            </div>
            <div class="timer_buttons">
                <button v-if="!type.started" @click="start">開始</button>
                <button v-if="type.started" @click="reset">重置</button>
            </div>
        </div>
        <div class="timer_right">
            <div class="start_section">
                <div>開始時間</div>
                <div>{{start_time}}</div>
            </div>
            <div class="pass_section">
                <div>經過時間</div>
                <div>{{pass_time}}</div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-template" id="rank-template">
    <div class="rank-runtype">
        <h3>{{typeName}}{{gender}}</h3>
        <table>
            <thead>
                <tr>
                    <th>名次</th>
                    <th>姓名</th>
                    <th>背號</th>
                    <th>時間</th>
            </tr></thead>
            <tbody>
                <tr v-for="data in datas">
                    <td>{{data.rank}}</td>
                    <td>{{data.name}}</td>
                    <td>{{data.number}}</td>
                    <td>{{data.time}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</script>


    



<body>
<?php if(Session::exist('user')&& Session::get('user')=='admin') { ?>
    <div class="sidebar">
    <img src="logo.png" id="sidebar-logo">
    <span>路跑管理系統</span>
    <ul>
        <li class id="li_overall"><a href="#">總覽</a></li>
        <li class id="li_rank"><a href="#">名次</a></li>
        <li class id="li_staff"><a href="#">工人</a></li>
        <li class id="li_runner"><a href="#">跑者</a></li>
        <!-- <li class id="li_contact"><a href="#">通訊錄</a></li> -->
        <li class id="li_logout"><a href="php/logout.php">登出</a></li>

    </ul>
</div>
<div class="main">
    <div id="overall">
        <!-- 挑戰組vs樂活組主控區 -->
        <div id="record">
            <input id="record_num" type="number" placeholder="請輸入跑者ID">
            <button id="record_button">登錄</button>
            <div style="opacity:0" id="record_msg_box">
                <span id="record_msg">1039登錄成功</span>
            </div>
        </div>
        <div id="run_panel">
            <div id="app">
                <timer v-for="type in run_types" :type="type">
                </timer>
            </div>
        </div>

        <div id="notification-section">
            <table>
                <thead>
                    <tr>
                        <th>時間</th>
                        <th>地點</th>
                        <th>訊息</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="data in notifications"
                        :class="{
                            'not-read':data.is_read==false,
                        }" 
                        @click={notificationClicked(data)}   
                    >
                        <td>{{data.time.show}}</td>
                        <td>{{data.position.show}}</td>
                        <td>{{data.message.show}}</td>
                        </th>
                </tbody>
            </table>
        </div>

        <!-- vue -->
        <div id="map-block">
            <div v-show="false" id="map-information">
                <div class="info-bubble">
                    <h3>{{infoWindowContent.position}}</h3>
                    <div class="staffInfo">
                        <h4>{{infoWindowContent.staffName}}</h4>
                        <p>{{infoWindowContent.staffTel}}</p>
                    </div>
                    <h4>{{infoWindowContent.needTitle}}</h4>
                    <div id="needs">
                        <div v-if="infoWindowContent.needs!=null" 
                        v-for="need in infoWindowContent.needs">
                            <p>{{need.name}}：{{need.quantity}}{{need.unit}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="map">
            </div>
        </div>
    </div>
    <div id="rank">
        <h1>名次總覽</h1>
        <button @click="recalculateRank()">重新整理</button>
        <div id="rank-container" >
            <rank v-for="type in ranks"
            :type-name="type.typeName"
            :gender="type.gender"
            :datas="type.data">
            </rank> 
        </div>
    </div>
    <div id="staff" class="grid">
        <div class="data">
            <h1>工人搜尋</h1>
            <input class="searchbox" type="text" id="staff_searchbox">
            <table class="result_table" id="staff_table">
                <thead>
                    <tr>
                        <th field="name">姓名</th>
                        <th field="uid">帳號</th>
                        <th field="position">地點</th>
                        <th field="staff_type">工作</th>
                        <th field="tel">手機</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="form">
            <input id="s_insert" class="form_insert" type="button" value="新增">
            <form autocomplete="off" id="staff_form">
                <fieldset>
                    <legend style="margin:10px">工人資訊</legend>
                    <input type="hidden" id="s_id" name="id" value="">
                    <input id="s_action" type="hidden" name="action" value="">
                    <div>
                        <label for="name">姓名</label>
                        <input autocomplete="off" type="text" name="name" id="s_name">
                    </div>
                    <div>
                        <label for="name">帳號</label>
                        <input autocomplete="off" type="text" name="uid" id="s_uid">
                    </div>
                    <div>
                        <label for="position">地點</label>
                        <select name="position" id="s_position">
                            <option value=""></option>
                        </select>
                    </div>
                    <div>
                        <label for="staff_type">工作</label>
                        <select name="staff_type" id="s_staff_type"></select>

                    </div>
                    <div>
                        <label for="tel">手機</label>
                        <input autocomplete="off" type="tel" name="tel" id="s_tel">
                    </div>
                    <div>
                        <label for="password">密碼</label>
                        <input autocomplete="off" type="text" style="text-security:disc; -webkit-text-security:disc;"
                            name="new_pwd" id="s_new_pwd">

                    </div>

                    <div id="s_buttons">
                        <!-- bad naming -->
                        <button id="s_add" type="submit" value="insert">新增</button>
                        <button id="s_update" type="submit" value="update">更新</button>
                        <button id="s_delete" type="submit" value="delete">刪除</button>

                    </div>






                </fieldset>
            </form>
        </div>
    </div>
    <div id="runner" class="grid">
        <div class="data">
            <h1>跑者搜尋</h1>
            <input class="searchbox" type="text" id="runner_searchbox">
            <table class="result_table" id="runner_table">
                <thead>
                    <tr>
                        <th field="number">背號</th>
                        <th field="run_type">組別</th>
                        <th field="name">姓名</th>
                        <th field="run_group">跑團</th>
                        <th field="tel">手機</th>
                        <th field="gender">性別</th>
                        <th field="end_time">成績</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div>
            <input id="r_insert" class="form_insert" type="button" value="新增">
            <form autocomplete="off" id="runner_form">
                <fieldset>
                    <legend style="margin:10px">跑者資訊</legend>
                    <input type="hidden" id="r_id" name="id" value="">
                    <input id="r_action" type="hidden" name="action" value="">
                    <div>
                        <label for="number">背號</label>
                        <input autocomplete="off" type="text" name="number" id="r_number">
                    </div>
                    <div>
                        <label for="run_type">組別</label>
                        <select name="run_type" id="r_run_type">

                        </select>
                    </div>
                    <div>
                        <label for="name">姓名</label>
                        <input autocomplete="off" type="text" name="name" id="r_name">
                    </div>
                    <div>
                        <label for="run_group">跑團</label>
                        <div id="run_group_holder">
                            <input type="text" name="run_group" id="r_run_group">
                            <div id="run_group_sug">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="tel">手機</label>
                        <input autocomplete="off" type="tel" name="tel" id="r_tel">
                    </div>
                    <div>
                        <label for="gender">姓別</label>
                        <select name="gender" id="r_gender">
                            <option value="1">男</option>
                            <option value="0">女</option>
                        </select>
                    </div>
                    <div id="r_buttons">
                        <!-- bad naming -->
                        <button id="r_add" type="submit" value="insert">新增</button>
                        <button id="r_update" type="submit" value="update">更新</button>
                        <button id="r_delete" type="submit" value="delete">刪除</button>

                    </div>






                </fieldset>
            </form>
        </div>




    </div>
    <div id="contact">
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>
        <h1>4</h1>
        <p>這邊放緊急聯絡資訊</p>

    </div>

</div>



<?php 
} else {
    header("Location: login.php");
}
?>

<script src="js/v-timer.js"></script>
<script src="js/v-map.js"></script>
<script src="js/v-notification.js"></script>
<script src="js/v-rank.js"></script>




    
</body>
</html>