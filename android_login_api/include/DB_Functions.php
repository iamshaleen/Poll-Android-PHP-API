<?php

class DB_Functions
{
	private $conn;
	// constructor
	function __construct()
	{
		require_once 'DB_Connect.php';
		// connecting to database
		$db = new Db_Connect();
		$this->conn = $db->connect();
		

		if($this->conn){
			//echo "Connected to the database";
			//echo $this->conn;
		}
		else{
			echo "Not Connected";
		}
	}
	// destructor
	function __destruct()
	{

	}

/** * Storing new user * returns user details */

	public function storeUser($fullname, $email, $emp_id, $password)
	{
		
		$uuid = uniqid('', true);
		$hash=$this->hashSSHA($password);
		$encrypted_password=$hash["encrypted"];
		$salt=$hash["salt"];
		//echo $encrypted_password." ".$salt;
	
		$datestring = date('Y-m-d H:i:s');

		$stmt= sqlsrv_prepare($this->conn,"insert into appuser(unique_id,fullname, email, emp_id, encrypted_password, salt) VALUES(?, ?, ?, ?, ?, ?)",
		array(&$uuid,&$fullname,&$email,&$emp_id,&$encrypted_password,&$salt));
		if($stmt===false){
			$this->DisplayErrors();
		}
		//echo $stmt;
		
		$result = sqlsrv_execute($stmt);
		//echo $result;

	// check for successful store

		if ($result)
		{
			$stmt1 = sqlsrv_prepare($this->conn,"SELECT * FROM appuser WHERE emp_id = ?",array(&$emp_id));
			//$stmt->bind_param("s", $email);
			if(sqlsrv_execute($stmt1))
			{
				$stmt1result=sqlsrv_fetch_array($stmt1,SQLSRV_FETCH_ASSOC);
       				$user=array();
				if($stmt1result!=NULL)
				{
					$user=$stmt1result;
				}
            			return $user;
			}

			else
			{
				return NULL;
			}
		}
	}

	/** * Get user by email and password */

	public function getUserByEmailAndPassword($emp_id, $password)
	{
		
		if( $stmt = sqlsrv_prepare($this->conn,"SELECT * FROM appuser WHERE emp_id = ?",array(&$emp_id)))
		{
			sqlsrv_execute($stmt);
			
			$stmt1=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
			
       			$user=array();
			

			if($stmt1!=NULL)
			{
				$user=$stmt1;
				
			}
			
			$salt=$user['salt'];
			$encrypted_password = $user['encrypted_password'];
			$hash = $this->checkhashSSHA($salt,$password);
			if($hash==$encrypted_password){
				
				return $user;
			}
			else{
				return NULL;
			}
			
            //
        }

		else
		{
			return NULL;
		}
	}
 /** * Check user is existed or not */

	public function isUserExisted($emp_id)
	{
		$stmt = sqlsrv_prepare($this->conn,"SELECT * from appuser WHERE emp_id = ?",array(&$emp_id));
		sqlsrv_execute($stmt);
		$stmtresult=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
		//echo $stmtresult; 
		if ($stmtresult!=NULL)
		{
			//$stmt->close();
			return true;
		}
		else
		{
			//$stmt->close();
			return false;
		}
	}

/** * Check for forgot password credentials */

	public function isUserExistedforgot($email, $collid)
	{
		$stmt = $this->conn->prepare("SELECT email, emp_id from appusers WHERE email = ? AND emp_id = ?");
		$stmt->bind_param("ss", $email, $emp_id);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0 )
		{
			$stmt->close();
			return true;
		}
		else
		{
			$stmt->close();
			return false;
		}
	}





        public function forgot_password($email)
        {
                $length = 10;
                $password = str_shuffle("123AbEC0XY");

                $stmt = $this->conn->prepare("UPDATE appusers SET password = ? WHERE email = ?");
				$stmt->bind_param("ss", $password, $email);

                $stmt->execute();
			$stmt->close();
			return $password;


        }

        public function change_pass($email,$newpassword)
        {
        	$stmt = $this->conn->prepare("UPDATE appusers SET password = ? WHERE email = ?");
		$stmt->bind_param("ss", $newpassword, $email);

                $stmt->execute();
			$stmt->close();
        }


        public function cretable($createst)
        {
		$stmt = sqlsrv_prepare($this->conn,$createst);
		if($stmt===false){

			$this->DisplayErrors();
		}
		$result=sqlsrv_execute($stmt);
               if($result){
		       return TRUE;

	       }else{
		       return FALSE;
	       }



        }

        public function statusupdate($title,$emp_id,$pollname)
        {
				
		$var = 1;
                
		$stmt=sqlsrv_prepare($this->conn,"INSERT INTO pollstatus(title, active, emp_id, pollname) VALUES(?, ?, ?, ?)",array(&$title,&$var,&$emp_id,&$pollname));
		if($stmt){

			$result=sqlsrv_execute($stmt);
			if($result){
				
			}else{
				DisplayErrors();
			}
		}

        }


        public function checkpollstatus($title)
        {

                

		$stmt=sqlsrv_prepare($this->conn,"SELECT active FROM pollstatus WHERE title = ? ",array(&$title));
		if($stmt===false){
			$this->DisplayErrors();
		}else{
			$result=sqlsrv_execute($stmt);
			if($result){

				$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
				$active=$resultset['active'];
				//echo $active;
				return $active;
			}
		}
                

        }

        public function hasvoted($title,$emp_id)
        {


		$stmt = sqlsrv_prepare($this->conn,"SELECT count(*) as votecount FROM " .$title. "  WHERE emp_id = ? ",array(&$emp_id));
		if($stmt===false){
			$this->DisplayErrors();
		}else{
			$result=sqlsrv_execute($stmt);
			if($result){

				$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
				$votecount=$resultset['votecount'];
				//echo $votecount;
				if($votecount==0){
					return FALSE;
				}else{
					return TRUE;
				}
			}

		}


        }


        public function getuserpolls($emp_id)
        {
			$stmt=sqlsrv_prepare($this->conn,"SELECT DISTINCT(pollname) FROM pollstatus where emp_id = ?",array(&$emp_id));
			if($stmt===false){
				$this->DisplayErrors();
			}else{
	
				$result=sqlsrv_execute($stmt);
				$rows=array();
				$index=0;
				if($result){
					
					if(sqlsrv_has_rows($stmt)){
	
						while($obj=sqlsrv_fetch_object($stmt)){
	
							$rows[$index]=$obj->pollname;
							$index++;
						}
	
						return $rows;
					}else{
						return NULL;
					}
	
				}
				
			}
        }

        public function checkcolumns($title)
        {
			$stmt=sqlsrv_prepare($this->conn,"select column_name from INFORMATION_SCHEMA.COLUMNS Where TABLE_NAME = '".$title."' ");
			if($stmt===false){
			//echo "inside if";
				$this->DisplayErrors();
			}else{
			//echo "inside else";
				$result=sqlsrv_execute($stmt);
				$cols= array();
				$index=0;
			 	if($result){

					if(sqlsrv_has_rows($stmt)){
					
						while($obj=sqlsrv_fetch_object($stmt)){
	
							$cols[$index]=$obj->column_name;
							//echo ''.$obj->column_name.' ';
							$index++;
						}
					}
				
				

			 	}
				return $cols;
			
			}	
         
        }

        public function registervote($querystring)
        {
			$stmt = sqlsrv_prepare($this->conn,$querystring);
			if($stmt===FALSE){
				$this->DisplayErrors();
			}else{

				$result = sqlsrv_execute($stmt);
               if(!$result){
				   return FALSE;
				}else{
					return TRUE;
				}
               
			}
			  
        }

        public function getpolldetails($pollname)
        {
			
			$stmt=sqlsrv_prepare($this->conn,"SELECT active FROM pollstatus WHERE pollname = ?",array(&$pollname));
			if($stmt===false){
				$this->DisplayErrors();
			}else{
				$result=sqlsrv_execute($stmt);
				if($result){

					$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
					$active=$resultset['active'];
					//echo $active;
					return $active;

				}
			}

        }
 
        public function gettotalvotes($title)
        {

			$stmt = sqlsrv_prepare($this->conn,"SELECT count(*) as totalvotes FROM " .$title );
			if($stmt===false){
				$this->DisplayErrors();
			}else{
				$result=sqlsrv_execute($stmt);
				if($result){
					$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
					$totalvotes=$resultset['totalvotes'];
					return $totalvotes;

				}
			}
			


        }
 
        public function gettitle($pollname)
        {	
			$stmt=sqlsrv_prepare($this->conn,"SELECT title FROM pollstatus WHERE pollname = ?",array(&$pollname));
			if($stmt===false){
				$this->DisplayErrors();
			}else{
				$result=sqlsrv_execute($stmt);
				if($result){
					$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
					$title=$resultset['title'];
					return $title;
				}
			}

        }

        public function endpoll($pollname)
        {
			$active = 0;
			$stmt=sqlsrv_prepare($this->conn,"UPDATE pollstatus SET active = ? WHERE pollname = ?",array(&$active,&$pollname));
			if($stmt===false){
				$this->DisplayErrors();
			}else{
				$result=sqlsrv_execute($stmt);
				if($result){
					return TRUE;
				}else{
					return FALSE;
				}
			}

        }

        public function getpollresult($pollname)
        {
			//echo "inside poll result";
			$stmt = sqlsrv_prepare($this->conn,"select column_name from INFORMATION_SCHEMA.COLUMNS Where TABLE_NAME = '".$pollname."' ");
			$array_result = array();
			if($stmt===false){
				$this->DisplayErrors();
			}else{
				$result=sqlsrv_execute($stmt);
				$cols= array();
				$index=0;
				if($result){

					if(sqlsrv_has_rows($stmt)){
					
						while($obj=sqlsrv_fetch_object($stmt)){
							
							if($index>1){
								$cols[$index-2]=$obj->column_name;
							}
							
							$index++;
						}
					}

				}
				array_pop($cols);
				$array_result['columns']=$cols;
				$index=0;
				$col_values=array();
				while($index<sizeof($cols)){

					$stmt=sqlsrv_prepare($this->conn,"SELECT count(*) as totalcount FROM " .$pollname. " WHERE " .$cols[$index]. " = 1");
					if($stmt===false){
						$this->DisplayErrors();
					}else{
						$result=sqlsrv_execute($stmt);
						if($result){

							$resultset=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
							$totalcount=$resultset['totalcount'];
							array_push($col_values,$totalcount);
						
						}
						$index++;
					}
				}
				$array_result['votes']=$col_values;
				return $array_result;


			}
        

        }
		
	public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
		//echo $salt;
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
		//echo $encrypted;
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
	}
	

	public function DisplayErrors()  
{  
     $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);  
     foreach( $errors as $error )  
     {  
          echo "Error: ".$error['message']."\n";  
     }  
}


}

?>
		