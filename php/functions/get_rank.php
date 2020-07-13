<?php
require_once("../core/init.php");

if(Session::get('user')=='admin'){
    $results = DB::singleton()->select('run_type', $condition=['started','=',1], $fields=['id'])->getResults();
    
    //get all run type Id
    if(!empty($results)){
        $returnObj = [];

        foreach($results as $result){
            $id = $result->id;
            $run_type = new RunType($id);


            //male / femal
            for($i=1;$i>=0;$i--){
                if(Input::get('recalc')==true){
                    $run_type->reRank($i);
                }

                $toAdd = [];

                $typeName = $run_type->getName();
                $gender = $i==1?"男":"女";
                $dataOrg = $run_type->getRankData($i);//select where gender & type match, order by rank
                $data = [];
                foreach($dataOrg as $d){
                    $data[]=[
                        'rank'=>$d->rank,
                        'name'=>$d->name,
                        'number'=>$d->number,
                        'time'=>$d->run_time
                    ];
                }
                $toAdd = [
                    'typeName'=>$typeName,
                    'gender'=>$gender,
                    'data'=>$data
                ];
                $returnObj[] = $toAdd;
            }
        }

        echo json_encode($returnObj);


    }else{
        echo "EMPTY";
    }



}
//not only get data, also recalcuate??? 

