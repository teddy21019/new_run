<?php


/**
 * Class : Validate
 * 
 * 在接收到使用者輸入的資訊並傳到資料庫之前，
 * 先用一個Validate類別檢查是否符合規定，
 * 例如不含特殊字元、字數太少...
 * 
 * 這邊寫的檢查內容包含：
 * 必填： required: true/false
 * 已經存在：unique: (col_name)
 * 最長、最短：max, min: num
 * 相符：match: (filed name)
 * 
 * 用法：
 * $validate->check($_POST,
 *      array(
 *          "field"=>array(
 *                      Validate::REQUIRED=>true, 
 *                      Validate::UNIQUE=>'runner/number'
 *                  )
 *      )
 * )
 */

class Validate{

    private $_errorMsg="", 
            $_errors=array(),
            $_db;

    //types of validaion
    const REQUIRED      =1;
    const MAX           =2;
    const MIN           =3;
    const UNIQUE        =4;
    const EXIST         =5;
    const MATCH         =6;
    const TYPE          =7;
    const PWDMATCH      =8;
    const validationMsg = array('','required', 'exceed max', 'below min','not unique','not exist','not match','wrong type');

    //types of TYPE
    const NUMBER        =1;
    const TEL           =2;
    const EMAIL         =3;


    public function __construct()
    {
        $this->_db = DB::singleton();    //refer to database to check uniqueness
    }

    public function check($source, $fields = array())
    {
        /**
         * source is the array of form to check
         * 
         * field array will be like:
         * array(
         *   'name'=>array(
         *        Validate::REQUIRED  =>true,
         *       Validate::MAX       =>10,
         *       Validate::MIN       =>2,
         *   ),
         *   'number'=>array(
         *       Validate::REQUIRED  =>true,
         *       Validate::MAX       =>10,
         *       Validate::MIN       =>3,
         *       Validate::UNIQUE    =>'runner'
         *   )
         * );
         * 
         */

        foreach ($fields as $field => $rules) {
            /**
             *  field       rules
             *   'name'=>array(
             *       Validate::REQUIRED  =>true,
             *       Validate::MAX       =>10,
             *       Validate::MIN       =>2,
             *   )
             *
             */

            foreach ($rules as $rule_name => $rule) {
                /**
                 * $rule is like [Validate::REQUIRED] => true
                 */
                
                 $value = escape($source[$field]);   // 取得在$source裡面要檢查的項目(field)的值

                //如果必填但沒填，其他都不用檢查了（尤其最小）
                if ($rule == true && empty($value)) {
                    //必填但沒填
                    $this->addError($field, $rule_name);
                    break;  //到下一個field
                } else {
                    switch ($rule_name) {
                        case self::MAX:
                        //過長
                            if (mb_strlen($value, 'utf-8') > intval($rule)) {      //mb_strlen回傳"字數"，適用utf-8
                                $this->addError($field, $rule_name);
                            }
                            break;

                        case self::MIN:
                        //過短
                            if (mb_strlen($value, 'utf-8') < intval($rule)) {
                                $this->addError($field, $rule_name);
                            }
                            break;

                        case self::UNIQUE:
                            //資料庫中不能有重複的
                            //這裡rule要長得像 "table/column"
                            $table = explode("/",$rule)[0];
                            $column = explode("/", $rule)[1];

                            if($this->_db->select($table, array($column,"=",$value))->count()){
                                $this->addError($field, $rule_name);
                            }
                            break;
                        
                        case self::EXIST:
                            //要出現在資料庫中
                            //這裡rule要長得像 "table/column"
                            $table = explode("/",$rule)[0];
                            $column = explode("/", $rule)[1];

                            if($this->_db->select($table, array($column,"=",$value))->count()==0){
                                $this->addError($field, $rule_name);
                            }
                        break;
                        
                        case self::MATCH:
                            //用在像是驗證「再次輸入密碼」
                            //這裡rule會是另一個field
                            if($source[$rule] != $value){
                                $this->addError($field, $rule_name);
                            }
                            break;

                        case self::TYPE:
                            switch ($rule){
                                case self::NUMBER:
                                
                                if(!is_numeric($value)){
                                    $this->addError($field, $rule_name);
                                }
                                break;

                                case self::TEL:
                                if(!preg_match("/^09\d{8}$/", $value)){
                                    $this->addError($field, $rule_name);
                                }
                                break;

                                case self::EMAIL:
                                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                                    $this->addError($field, $rule_name);
                                }
                                break;
                            }
                            break;
                        case self::PWDMATCH:
                            // 'staff/password'
                            $table = explode("/",$rule)[0];
                            $uid = explode("/", $rule)[1];
                            if($orgPwd=$this->_db->select($table, [$column,'=','uid'])->firstResult().password){
                                if(!password_verify($value, $orgPwd)){
                                    $this->addError('field', $rule_name);
                                }
                            }else{
                                $this->addError('field', $rule_name);

                            }


                    }
                }
            }
        }

    }

    public function passed()
    {
        return (count($this->_errors) == 0) ? true : false;
    }

    private function addError($field, $eCode){
        $this->_errors[$field][] = self::validationMsg[$eCode]; // human readable debug message
        // $this->_errors[$field][] = $eCode;

    }

    public function getError(){
        return $this->_errors;
    }
}