<?php 
require_once("../core/init.php");


$returnData=[];

if (Session::exist('user') && Session::get('user') == 'admin') {
    // print_r($_POST);
    if (Input::exist()) {

        /****************************
         * 
         *          UPDATE
         * 
         ***************************/
        if(Input::get('action')=="update"){
            
            unset($_POST['action']);
            //validate
            $v = new Validate();
            $v->check($_POST, array(
                'id' => array(
                    Validate::REQUIRED => true,
                    Validate::EXIST => 'runner/id',
                    Validate::TYPE => Validate::NUMBER
                ),
                'number' => array(
                    Validate::REQUIRED => true,
                    Validate::MAX => 5,
                    Validate::MIN => 3,
                    Validate::TYPE=>Validate::NUMBER
                ),
                'name' => array(
                    Validate::REQUIRED => true,
                    Validate::MAX => 20,
                    Validate::MIN => 2
                ),
                'run_type' => array(
                    Validate::EXIST => "run_type/id"
                ),
                'tel' => array(
                    Validate::TYPE => Validate::TEL
                )
    
            ));
    
    
            $newData = [];
            //everything except number is validated
            if (count($v->getError()) == 0) {
                $r = Runner::singleton();
                //if want to change number
                if (Input::get('number') != $r->getDataById(Input::get('id'))->number) {
    
                    //check if new number already exist
                    if ($r->getDataByNumber(Input::get('number'))) {
                        $returnData['msg']="VAL_ERR";
                        $returnData['field'] = ['number'=>['exist']];
                    }else{
                        $newData = $_POST;                    
                        Runner::singleton()->update(Input::get('id'), $newData);
                        // print_r($_POST);
                        $returnData['msg']= "SUCCESS";
                    }
    
    
                } else {
                //doesn't want to change number
                    $newData = $_POST;
                    unset($newData['number']);
                    Runner::singleton()->update(Input::get('id'), $newData);
                    // print_r($_POST);
                    $returnData['msg']= "SUCCESS";
                }
    
    
    
    
            } else {
                //validation error
                $returnData['msg']="VAL_ERR";
                $returnData['field'] = $v->getError();
    
            }




        /****************************
         * 
         *          INSERT
         * 
         ***************************/


        }else if (Input::get('action')=='insert'){
            unset($_POST['action']);
            unset($_POST['id']);    //in case accidently has id

            $v = new Validate();
            $v->check($_POST, array(
                'number'=>array(
                    Validate::REQUIRED =>true,
                    Validate::UNIQUE =>"runner/number",
                    Validate::MIN=>3,
                    Validate::MAX=>5
                ),                
                'name' => array(
                    Validate::REQUIRED => true,
                    Validate::MAX => 20,
                    Validate::MIN => 2
                ),
                'run_type' => array(
                    Validate::EXIST => "run_type/id"
                ),
                'tel' => array(
                    Validate::TYPE => Validate::TEL
                )

            ));

            if(count($v->getError())==0){
                $r = Runner::singleton();
                $r->add($_POST);
                $returnData['msg']= "SUCCESS";
            }else{
                $returnData['msg']= "VAL_ERR";
                $returnData['field'] = $v->getError();

            }






        /****************************
         * 
         *          Delete
         * 
         ***************************/

        }elseif(Input::get('action')=='delete'){
            $id=Input::get('id');
            $r = Runner::singleton();
            $r->remove($id);

            //檢查是否移除
            if($r->getDataById($id)==false){
                $returnData['msg']= "SUCCESS";
            }else{
                $returnData['msg']= "RMV_ERR";

            }
        }


    } else {
        //input error
        $returnData['msg']="INPUT_ERR";

    }
} else {
    //session error
    $returnData['msg']="ADMIN_ERR";
}

echo json_encode($returnData);