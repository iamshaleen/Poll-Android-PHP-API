<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['emp_id'])) {

    // receiving the post params
    $emp_id = $_POST['emp_id'];
    $res = $db->getuserpolls($emp_id);
    if($res != NULL){
    $response["error"] = FALSE;
    $response["vals"] = $res;
    echo json_encode($response);
    
    }else {

        $response["error"] = FALSE;
        $response["vals"] = NULL;
        echo json_encode($response);
    
     }
    
    }  else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter (emp_id) is missing!";
    echo json_encode($response);
}
?>		
