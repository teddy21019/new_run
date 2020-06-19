<?php

/*******************************************
 * Model Layer -- Connect Database
 * 
 * 這個程式碼採 MVC 設計模式
 * DB這個class只進行「連接資料庫」
 * 而「連結資料庫」也只有DB這個class可以做
 * 
 * 這樣一方面可以區分好工作，另一方面之後這塊
 * 程式碼還可以搬到其他後台運作
 * 
 *******************************************/
class DB
{


    private $_pdo,                  //php預設用來連接資料庫的套件
        $_query,                //最後要拿來執行的query
        $_error = false,        //錯誤。預設 false
        $_results,              //最後結果
        $_count = 0;            //結果數量  //42:31

    // 設定為private 是為了只讓class自己創立instance
    private function __construct()
    {
        try {
            $this->_pdo = new PDO(  //PDO( 'mysql:host=127.0.0.1;dbname=runner' , username, password )
                'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'),
                Config::get('mysql/username'),
                Config::get('mysql/password')
            );
            $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }


    private static $_instance = null;   //singleton instance
    /**
     * Singleton 
     * 
     * 建立一個單型（一種設計模式）
     * 這個class只會有一個物件，就是他自己。
     * 這樣做是為了避免重複有多個物件連接到資料庫
     * 
     */
    public static function singleton()
    {

        if (!isset(self::$_instance)) {       //如果還沒建立singleton，建立他
            self::$_instance = new DB();
        }                                   //如果已經建立了，回傳那個singleton
        return self::$_instance;
    }

    /**
     * Query function
     * 
     * 所有向資料庫傳送sql指令的函數
     * 就連db class 裡面其他public method
     * 都要透過這個函數來傳資料。
     * 
     * 這樣好處是，如果未來要除錯，在確定這一步沒有問題之下
     * 可以減少要debug的步驟。
     * 
     * 用法：query("SELECT * FROM table WHERE col1 = ?", array(10))
     * 
     */
    public function query(string $sql, $params = array())
    {
        // echo "<br>", $sql, "<br>";
        // print_r($params);
        $this->_error = false;
        $this->_results = array();  //reset all data from previous query
        $this->_count = 0;


        // $sql = "SELECT * FROM `runner` WHERE `id` = ? ";
        // $params = array('id'=>1);
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $i = 1;
            if (count($params)) {
                foreach ($params as $param) {   //only sends in value, doesn't care about key
                    $this->_query->bindValue($i++, $param);
                }
            }

            if ($this->_query->execute()) {                     //執行並成功
                // echo "execute succeess";
                $this->_results = $this->_query->fetchAll();    //結果取回
                $this->_count = $this->_query->rowCount();      //結果數量
    
            //    print_r($this->_results);
            } else {
                $this->_error = true;

                echo "execute fail";
                print_r($this->_query->errorInfo());

            }

            return $this;
        }

    }

    public function firstResult()
    {
        return isset($this->_results[0])?$this->_results[0]:false ;
    }

    public function getResults(){
        return $this->_results;
    }

    public function count(){
        return $this->_count;

    }

    /**
     * Syntax candy - select
     * 
     * 把要向資料庫擷取資料的sql語法
     * query("SELECT * FROM table WHERE XXX", param)
     * 簡化成
     * select($table, where)
     * 
     * 目前只有一個搜尋條件，要很多個你自己寫
     */
    public function select($table, $condition = array())
    {    //condition = array('id','=','10')
        // get '=', '>', '<'

        if (empty($condition)) {
            return $this->query("SELECT * FROM `${table}`");

        } else {

            $conditionString = self::conditionString($condition);   //conditionString already includes "WHERE"
            if (empty($conditionString)) {  //error
                $this->_error = true;
                return false;
            } 
            $param = $condition[2];
            return $this->query("SELECT * FROM `${table}` WHERE ${conditionString}", array($param));

        }



    }

    /**
     * Get with Limit
     * 
     * 如果要取16~25的資料 (from 16 get 10)
     * 則Query 為
     * "SELECT *  FROM table LIMIT 15, 10"
     */
    public function selectSeveral($table, $from = 1, $get = 30, $condition = array(), $order = array())
    {  //get from 16~25 (10) is
        $offset = $from - 1;

        if(empty($order)){
            if(empty($condition)){
                return $this->query("SELECT * FROM `${table}` LIMIT ${offset}, ${get}");
            }else{
                $conditionString = self::conditionString($condition);
                if (empty($conditionString)) {
                    $this->_error = true;
                    return false;
                }else{
                    $param = $condition[2];
                }
                return $this->query("SELECT * FROM `${table}` WHERE ${conditionString} LIMIT ${offset}, ${get}", array($param) );
            }
        }else{
            /**
             * 有排序 $order = ['type'=>A, 'number'=>D]<=依序由type, order 來排序...
             */

             $orderString = "";
             $i = 1;
             $len = count($order);
             foreach($order as $col=>$dir){
                $orderString .= "`${col}` ";
                switch($dir){
                    case 'A':
                    $orderString .= "ASC";
                    break;
                    case 'D':
                    $orderString .= "DESC";
                    break;
                }
                if($i++<$len){
                    $orderString.=", ";
                }
             }
    
            if(empty($condition)){
                return $this->query("SELECT * FROM `${table}` ORDER BY ${orderString} LIMIT ${offset}, ${get}");
            }else{
                $conditionString = self::conditionString($condition);
                if (empty($conditionString)) {
                    $this->_error = true;
                    return false;
                }else{
                    $param = $condition[2];
                }
                return $this->query("SELECT * FROM `${table}` WHERE ${conditionString} ORDER BY ${orderString} LIMIT ${offset}, ${get}", array($param) );
            }
            
        }



    }


    public function insert($table, $params = array())
    {

        //params = array("first"=>1, "second"=>3, "third"=>5)
        //INSERT INTO "table"("first", "second", "third") VALUES(?,?,?)
        $columnString = ""; // want "first" , "second" , "third" , "forth"
        $valueString = "";  // want ?,?,?,?

        $i = 1;
        $len = count($params);
        foreach ($params as $index => $param) {
            $columnString .= " `${index}` ";
            $valueString .= "?";
            if ($i++ < $len) {
                $columnString .= ",";
                $valueString .= ",";
            }
        }

        //INSERT `runner`( "first" , "second" , "third" , "forth" ) VALUES(?,?,?,?)
        $sql = "INSERT `${table}`(${columnString}) VALUES(${valueString})";
        return $this->query($sql, $params);
    }

    /**
     * Syntax candy - update
     * 
     * 把要向資料庫擷取資料的sql語法
     * query("UPDATE table SET col1=?, col2=? WHERE col3 > ? ",$param)
     * 簡化成
     * update($table, $condition=array("col3","=","true"), $params=array("col1"=>"", "col2"=>"", "col3"=>"true") )
     * 
     */
    public function update($table, $params, $condition=array())
    {


        $paramToQuery = array();    //the array to send to the query function
                                    // {condition value}, :)

        //between SET and WHERE
        $alterString = "";
        $i = 1;
        $len = count($params);
        foreach ($params as $index => $param) {
            $alterString .= " `${index}`=? ";
            if ($i++ < $len) {
                $alterString .= ",";
            }
            $paramToQuery[$index] = $param; //append
        }

        if (empty($condition)) {
            $sql = "UPDATE `${table}` SET ${alterString}";

        } else {
            $conditionString = self::conditionString($condition);
            if (empty($conditionString)) {
                $this->_error = true;
                return false;
            }
            $column = $condition[0];
            $param = $condition[2];
            $paramToQuery[$column] = $param;
            $sql = "UPDATE `${table}` SET ${alterString} WHERE $conditionString";

        }
    // echo $sql;
    // echo "<br>";
    // print_r($paramToQuery);
        return $this->query($sql, $paramToQuery);
    }

    public function delete($table, $condition = array()){

        $conditionString = $this->conditionString($condition);
        $this->query(" DELETE FROM `${table}` WHERE ${conditionString}", array($condition[2]));

    }

    public static function conditionString($condition)
    {
        if (!empty($condition)) {
            $column = $condition[0];
            $param = $condition[2];
            $compareSymble = $condition[1];
            $operator = "";
            switch ($compareSymble) {
                case "=":
                    $operator = "=";
                    break;
                case ">":
                    $operator = ">";
                    break;
                case "<":
                    $operator = "<";
                    break;
                default:
                    return false;       //error condition -> return false
            }
        return "`${column}` ${operator} ?";
        }else{
            return " ";      //condition not set -> return space
        }
    }
}