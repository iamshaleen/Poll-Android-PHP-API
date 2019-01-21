<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['emp_id']) && isset($_POST['password'])) {

    // receiving the post params
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $emp_id = $_POST['emp_id'];
    $password = $_POST['password'];
    

    // check if user is already existed with the same email or college id
    if ($db->isUserExisted($emp_id)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with ";
        echo json_encode($response);
    } else{
        // create a new user
		//echo("inside else");
        $user = $db->storeUser($fullname, $email, $emp_id, $password);
		//echo $user;
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["fullname"] = $user["fullname"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["emp_id"] = $user["emp_id"];
			$response["user"]["created_at"] = $user["created_at"];
            $response["user"]["updated_at"] = $user["updated_at"];
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
	}
    
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email, emp_id or password) is missing!";
    echo json_encode($response);
}
?>