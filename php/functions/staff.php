<?php 
require_once("../core/init.php");

$returnData=[];

if(Session::exist('user')){
    if(Input::exist()){
        /****************************
         * 
         *          UPDATE
         * 
         ***************************/
        if(Input::get('action')=='update'){
            $s = Staff::singleton();
            //field has 
            // id, action, name, uid, position, staff_type, tel, new_pwd
            unset($_POST['action']);    //don't need anymore
            $v = new Validate();
            
            $conditions = array(
                'id'=>array(
                    Validate::REQUIRED  => true,
                    Validate::EXIST     => 'staff/id',
                    Validate::TYPE      =>  Validate::NUMBER
                ),
                'name'=>array(
                    Validate::REQUIRED  => true,
                    Validate::MAX       => 20
                ),
                'position'=>array(
                    Validate::REQUIRED  => true,
                    Validate::EXIST     => 'position/id'
                ),
                'staff_type'=>array(
                    Validate::REQUIRED  => true,
                    Validate::EXIST     => 'staff_type/id'
                )
               
            );
            /** CHANGE PASSWORD
             * if is admin, doesnt have to validate origin password
             * but if not admin, verify original password first
             * 
             */


             //if doesn't want to change password or not in form
            if(Input::isset('new_pwd') && Input::get('new_pwd')!=""){
                if(Session::get('user')=='admin'){
                    $conditions['new_pwd'] = array(
                        Validate::MIN       => 4
                    );
                }else{
                    $conditions['pwd'] = array(
                        Validate::REQUIRED  => true,
                        Validate::PWDMATCH  => 'staff/password'
                    );
                    $conditions['new_pwd'] = array(
                        Validate::MIN       => 4
                    );
                    $conditions['new_pwd_2nd'] = array(
                        Validate::MATCH     =>'new_pwd'
                    );
                }


            }

            //check uid change
            //if the uid searched by id match, then don't check
            if(Staff::singleton()->getFieldById('uid', Input::get('id'))==Input::get('uid')){
                unset($_POST['uid']);
            }else{
                $conditions['uid'] = array(
                    Validate::REQUIRED      => true,
                    Validate::MAX           => 20,
                    Validate::UNIQUE        => 'staff/uid'
                );
            }

            //check tel change
            if(Staff::singleton()->getFieldById('tel', Input::get('id'))==Input::get('tel')){
                unset($_POST['tel']);
            }else{
                $conditions['tel'] = array(
                    Validate::REQUIRED  => true,
                    Validate::TYPE      => Validate::TEL,
                    Validate::UNIQUE    => 'staff/tel'
                );
            }




            
            $v->check($_POST,$conditions);
            if(count($v->getError())==0){
                //hash password
                $toUpdate = $_POST;
                if(!empty($_POST['new_pwd'])){
                    $toUpdate['password'] = password_hash(Input::get('new_pwd'), PASSWORD_DEFAULT);
                }
                $id = Input::get('id');
                
                //don't want
                unset($toUpdate['new_pwd']);
                unset($toUpdate['new_pwd_2nd']);
                unset($toUpdate['pwd']);
                unset($toUpdate['id']);

                $s->update($id,$toUpdate);
                $returnData['msg']="SUCCESS";
            }else{
                $returnData['msg']="VAL_ERR";
                $returnData['field']=$v->getError();
            }

        }


        /****************************
         * 
         *          INSERT
         * 
         ***************************/
        elseif(Input::get('action')=='insert'){
            unset($_POST['action']);
            unset($_POST['id']);

            $v = new Validate();
            $conditions = array(
                'name'=>array(
                    Validate::REQUIRED  => true,
                    Validate::MAX       => 20
                ),
                'uid'=>array(
                    Validate::REQUIRED  => true,
                    Validate::MAX       => 20,
                    Validate::UNIQUE    => 'staff/uid'
                ),
                'position'=>array(
                    Validate::REQUIRED  => true,
                    Validate::EXIST     => 'position/id'
                ),
                'staff_type'=>array(
                    Validate::REQUIRED  => true,
                    Validate::EXIST     => 'staff_type/id'
                ),
                'tel'=>array(
                    Validate::REQUIRED  => true,
                    Validate::TYPE      => Validate::TEL,
                    Validate::UNIQUE    => 'staff/tel'
                ),
                'new_pwd'=>array(
                    Validate::REQUIRED  => true,
                    Validate::MIN       => 4
                )
                );
            $v->check($_POST, $conditions);

            if(count($v->getError())==0){
                $s = Staff::singleton();
                $toInsert = $_POST;
                //hash password 
                // echo ("PWD=".Input::get('new_pwd'));
                $toInsert['password'] = password_hash(Input::get('new_pwd'), PASSWORD_DEFAULT);
                unset($toInsert['new_pwd']);

                $s->add($toInsert);
                $returnData['msg']="SUCCESS";
            }else{
                $returnData['msg']= "VAL_ERR";
                $returnData['field'] = $v->getError();
            }

        }
        /****************************
         * 
         *          DELETE
         * 
         ***************************/
        elseif(Input::get('action')=='delete'){
            $id = Input::get('id');
            $s = Staff::singleton();
            $s->remove($id);

            if($s->getDataById($id)==false){
                $returnData['msg']= "SUCCESS";
            }else{
                $returnData['msg']= "RMV_ERR";

            }
        }

        else{
            $returnData['msg']="ERROR";
        }
    }

    echo json_encode($returnData);

}