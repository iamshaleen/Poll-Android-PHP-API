<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['email']) && isset($_POST['collid'])) {

    // receiving the post params
    
    $email = $_POST['email'];
    $collid = $_POST['collid'];
    
    
    // check if user is already existed with the same email and college id
    if ($db->isUserExistedforgot($email, $collid)) {
        // user already existed
        $response["error"] = FALSE;
        $response["error_msg"] = "User Exists";
        $password = $db->forgot_password($email);
        $response["password"] = $password;
        echo json_encode($response);
       $to = $email;

       $subject = "Pollit Password Recovery Email" ;
       $headers = "From: admin@pollitchitkara.tk" ;
       $txt = "Thank you for using the Pollit app.\nYour details are as follows:\n Email Id: $email \n Password: $password";
       mail($to,$subject,$txt,$headers);
        
    } else {
        
            // user failed to verify
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred. Try Again!";
            echo json_encode($response);
            
    		}
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (email or collid) is missing!";
    echo json_encode($response);
}
?>
	