<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['title']) && isset($_POST['emp_id'])) {

    // receiving the post params
    $title = $_POST['title'];
    $emp_id = $_POST['emp_id'];

    // check status of poll active or not
    $status = $db->checkpollstatus($title);

    if ($status == 1) {	
	
        $response["error"] = FALSE;
        $response["status"] = $status; 
        $response["voted"] = $db->hasvoted($title,$emp_id);
        $colnames = $db->checkcolumns($title);
        $response["vals"] = $colnames;
        echo json_encode($response);
        //echo json_encode($colnames);
        
        
    } else {
        // user is not found with the credentials
        $response["error"] = FALSE;
        $response["status"] = 0;
        $response["voted"] = $db->hasvoted($title,$emp_id);
        $response["vals"] = FALSE;
        echo json_encode($response);
    }
} else {
    // required post param is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter title or emp_id is missing!";
    echo json_encode($response);
}
?>