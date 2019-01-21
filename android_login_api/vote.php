<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['querystring'])) {

    // receiving the post params
    $querystring = $_POST['querystring'];
    

    // get the user by email and password
    $result = $db->registervote($querystring);

    if ($result != FALSE) {
		// user is found
		$response["error"] = FALSE;
        echo json_encode($response);
        
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Vote Not Registered";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter querystring is missing!";
    echo json_encode($response);
}
?>
