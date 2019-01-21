<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['title']) && isset($_POST['createst']) && isset($_POST['emp_id']) && isset($_POST['pollname'])) {

    // receiving the post params
    $createst = $_POST['createst'];
    $title = $_POST['title'];
    $emp_id = $_POST['emp_id'];
    $pollname = $_POST['pollname'];
    $result = $db->cretable($createst);
    //echo $result;
    if($result != FALSE)
    {
        $db->statusupdate($title,$emp_id,$pollname);
        $response["error"] = FALSE;
        $response["error_msg"] = "Poll Created Successfully";
    }
    else{
        $response["error"] = TRUE;
        $response["error_msg"] = "Poll Not Created";
    }
    echo json_encode($response);
    
}   else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (title, createst) is missing!";
    echo json_encode($response);
}
?>		