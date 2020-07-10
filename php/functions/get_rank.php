<?php
require_once("../core/init.php");

if(Session::get('user')=='admin'){
    if(Input::get('recalc')==true){
        
        //get all run type Id
        $ids = DB::singleton()->select('run_type', $condition=[], $fields=['id'])->getResults();
        foreach($ids as $id){
            $run_type = new RunType($id);
            
        }

        
    }

}
//not only get data, also recalcuate??? 

