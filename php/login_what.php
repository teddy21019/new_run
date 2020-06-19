<?php
require_once 'core/init.php';

if(Input::exist('post')){

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'uid'=>[Validate::REQUIRED=>true],
        'pwd'=>[Validate::REQUIRED=>true]
    ));

    if ($validate->passed()) {
        $uid = Input::get('uid');
        $password = Input::get('pwd');

        $staff = new Staff($uid);

        if ($staff->exist()) {
            if ($staff->auth(Staff::ADMIN)) {
                if ($staff->login($password)) {
                    Session::set('user', 'admin');
                    // Session::set('username', $->name);
                    echo "SUCCESS";
                } else {
                    echo "WRONG_PWD";
                }
            } else {
                echo "NOT_ADMIN";
            }
        } else {
            echo "NO_USER";
        }
    }

}
?>
