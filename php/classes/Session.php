<?php

class Session{
    public static function set($field, $value){
        $_SESSION[$field] = $value;
    }

    public static function get($field){
        if (isset($_SESSION[$field])){
            return $_SESSION[$field];
        }else{
            return false;
        }
    }

    public static function exist($field){
        return isset($_SESSION[$field]);
    }
}