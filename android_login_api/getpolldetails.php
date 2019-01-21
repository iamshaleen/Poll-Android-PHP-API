<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['pollname'])) {

    // receiving the post params
    $pollname = $_POST['pollname'];
    $title = $db->gettitle($pollname);
    $result = $db->getpolldetails($pollname);
    $count = $db->gettotalvotes($title);

    if($result==0 || $result==1){

        $response["error"] = FALSE;
        $response["error_msg"] = "details obtained successfully";
        $response["status"] = $result;
        $response["count"] = $count;
        echo json_encode($response);
    
    }else {
        $response["error"] = FALSE;
        $response["error_msg"] = "details obtained successfully";
        $response["vals"] = NULL;
        echo json_encode($response);
    
    }
    
}  else {

    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter pollname is missing!";
    echo json_encode($response);
}
?>		