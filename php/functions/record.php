<?php
require_once("../core/init.php");

$data = array();

if (Session::exist('user') && (Session::get('user') == 'admin' || Session::get('user') == 'recorder')) {
    if (Input::exist() && Input::get('action') == 'record') {
        $number = Input::get('number');
        $runner = Runner::singleton();

        //check runner exist
        if ($runner_data = $runner->getDataByNumber($number)) {

            //check whether start
            
            $run_type = new RunType($runner_data->run_type);
            $run_type_name = $run_type->getName();

            if ($run_type->isStart()) {

                //not yet altered
                if($runner->altered($number)){
                    $data = [
                        'msg' => 'REPEAT',
                        'name'=>$runner_data->name,
                        'number'=>$runner_data->number,
                        'run_time'=>$runner_data->run_time,
                        'run_type'=>$run_type_name
                    ];
                }else{
                    $runner->finish($runner_data->number);
                    //refresh runner information
                    $runner_data = $runner->getDataByNumber($number);
    
                    $data = [
                        'msg' => 'SUCCESS',
                        'name'=>$runner_data->name,
                        'number'=>$runner_data->number,
                        'run_time'=>$runner_data->run_time,
                        'run_type'=>$run_type_name
                    ];
                }

                    
            } else {
                $data = ['msg' => "NOT_START", 'type' => $run_type_name];
            }
        } else {
            $data = ['msg' => 'NO_USER'];
        }


    } else {
        $data = ['msg' => '404'];
    }

} else {
    $data = ['msg' => '404'];
}
echo json_encode($data, JSON_UNESCAPED_UNICODE);