<?php

class Input
{
    /**
     * Input Class
     * 
     * 為避免一直使用 $_POST 或是 $_GET
     * 設計一個Input 類別來取得這些資訊
     * 會好看一點
     */

    public static function exist($type = 'post')
    {
        /**
         * 是否有輸入
         */
        $type = strtolower($type);
        switch ($type) {
            case 'post':
                return (empty($_POST)) ? false : true;
                break;
                
            case 'get':
                return (empty($_GET)) ? false : true;
                break;
        }
    }

    public static function get($field)
    {
        if (isset($_POST[$field])) {
            return $_POST[$field];
        } else if (isset($_GET[$field])) {
            return $_GET[$field];
        }else{
            return false;
        }
    }

    public static function isset($field){
        return isset($_POST[$field]);
    }
}