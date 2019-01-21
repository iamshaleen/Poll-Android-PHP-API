<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['pollname'])) {

    // receiving the post params
    $pollname = $_POST['pollname'];
    
    // check status of poll active or not
    $status = $db->endpoll($pollname);

    if ($status) {	
	
        $response["error"] = FALSE;
        $response["status"] = 0; 
        echo json_encode($response);
        
        
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "status not changed";
        echo json_encode($response);
    }
} else {
    // required post param is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter pollname is missing!";
    echo json_encode($response);
}
?>