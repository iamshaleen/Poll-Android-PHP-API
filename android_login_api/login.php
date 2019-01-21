<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['emp_id']) && isset($_POST['password'])) {

    // receiving the post params
    $emp_id = $_POST['emp_id'];
    $password = $_POST['password'];

	//echo "Something";
    // get the user by email and password
    $user = $db->getUserByEmailAndPassword($emp_id, $password);

    if ($user != false) {
        // user is found
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["fullname"] = $user["fullname"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["emp_id"] = $user["emp_id"];
        //$response["user"]["facebookid"] = $user["facebookid"];
	    $response["user"]["created_at"] = $user["created_at"];
        $response["user"]["updated_at"] = $user["updated_at"];
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>