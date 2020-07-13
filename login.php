<?php
require_once 'php/core/init.php';

if(Input::exist('post')){

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'uid'=>[Validate::REQUIRED=>true],
        'pwd'=>[Validate::REQUIRED=>true]
    ));

    if ($validate->passed()) {
        $uid = escape(Input::get('uid'));
        $password =escape(Input::get('pwd'));

        $user = new User($uid);
        $id = Staff::singleton()->getDataByUid($uid)->id;
        if ($user->exist()) {
            if ($user->login($password)) {
                Session::set('uid', $uid);
                Session::set('id', $id);
                if ($user->auth(User::ADMIN)) {           //1
                    Session::set('user', 'admin');
                    echo "ADMIN";
                    exit();
                } elseif ($user->auth(User::RECORDER)) {  //2
                    Session::set('user', 'recorder');
                    echo "RECORDER";
                    exit();
                } elseif($user->auth(User::SUPPLY)){
                    Session::set('user', 'supply');         //3
                    echo "SUPPLY";
                    exit();
                } elseif($user->auth(User::RIDE)){
                    Session::set('user', 'ride');         //4
                    echo "RIDE";
                    exit();
                }
                else {
                    Session::set('user', 'normal');         //xx
                    echo "NORMAL";
                    exit();
                }
            } else {
                echo "WRONG_PWD";
                exit();
            }
        }else{  //無使用者
            echo "NO_USER";
            exit();

        }
    }else{
        print_r($validate->getError());
        exit();
    }


}
?>


<html>
<head>
    <title>登入</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/login.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("form").submit(function (event) {
                event.preventDefault();
                // console.log($(this).serialize());
                $("input").removeClass('error');
                $.post("", $(this).serialize(), function (result) {
                    console.log(result);
                    if(result == "ADMIN"){
                        window.location.href = "panel.php";
                    }else if(result == "WRONG_PWD"){
                        $("#pwd").addClass("error");
                        alert("密碼錯誤")
                    }else if(result == "RECORDER"){
                        window.location.href = "record.php";
                    }else if(result == "SUPPLY" || result == "RIDE"){
                        window.location.href = "supply.php";
                    }else if(result == "NO_USER"){
                        $("#uid").addClass("error");
                        alert("無此帳號，請洽主辦單位");
                    }else{
                        $("#uid").addClass("error");
                        $("#pwd").addClass("error");
                        alert("尚未輸入完全");

                    }
                    $("#pwd").val("");
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <img class="nav-logo" src="logo.png">
        <span>路跑後台系統</span>
    </header>
    <div class="center-item login">
        <div class="mid">
            <img src="logo.png" class="logo">
            <h2>管理員登入</h2>
        </div>

        <form action="" method="post">
            <div class="inputBox">
                <label>帳戶</label>
                <input id="uid" type="text" name="uid">
            </div>
            <div class="inputBox">
                <label>密碼</label>
                <input id="pwd" type="password" name="pwd">
            </div>
            <input type="submit" name="submit" value="登入">
        </form>   

    </div>
</body>
</html>
