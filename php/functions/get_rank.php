<?php
require_once("../core/init.php");

if(Session::get('user')=='admin'){
    if(Input::get('recalc')==true){
        
        //get all run type Id
        $results = DB::singleton()->select('run_type', $condition=['started','=',1], $fields=['id'])->getResults();
        if(!empty($results)){
            foreach($results as $result){
                $id = $result->id;
                $run_type = new RunType($id);
                $run_type->reRank(0);//female
                $run_type->reRank(1);//male
            }
        }
    }
}
//not only get data, also recalcuate??? 

