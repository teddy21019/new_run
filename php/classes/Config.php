<?php


/*******************************************
 * Configuration Sugar
 * 
 * 雖然所有資訊在core/init.php 裡面都有了，我也
 * 的確可以用$GLOBAL['config']['xxx']['xxx']
 * 來讀取資料。但上面寫法有點太醜，所以透過這個class
 * 來降低閱讀程式碼的困難。
 * 
 * 目標：可以只寫
 * Config::get('mysql/host')
 * 
 * 而非
 * $GLOBAL['config']['mysql']['host']
 *  
 *******************************************/

class Config
{
    public static function get($p = null)
    {
        if ($p) {
            $dir = $GLOBALS['config'];
            $path = explode('/', $p);   // 'a/b/c'會變成 [a,b,c]

            foreach ($path as $part) {

                try {

                    if (isset($dir[$part])) {
                        $dir = $dir[$part];  //if exist, set dir to the it
                    } else {
                        $dir = null;
                        throw new Exception("Config '{$p}' doesn't exist");
                    }

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
            return $dir;
        }
    }
}