<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
   
	$errors = array(); 
	$email = $_POST['email'];
	$content =  $_POST['content'];
    if (empty($content)) { array_push($errors, "content is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }	
  
	if($email != '' && $content != '') {
		
		//$user_check_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
		//echo $user_check_query;
		//$result = mysqli_query($db, $user_check_query);
		//if($result )
		//	$user = mysqli_fetch_assoc($result);
	  
		//if (!$user) { // login ok

		//	 array_push($errors, "Sorry the email does not exist");
		//}
		//if (count($errors) == 0) 
		{	
			//echo 'email:'.$email;
			//echo 'content:'.$content;
			$url = 'http://simed10-trieste.7e14.starter-us-west-2.openshiftapps.com/spring-mvc-angularjs/api/sendMail/';
			$params = array(
				'email' => $email,
				'content' => $content,
			);

			 
			//Initiate cURL.
			$ch = curl_init($url);
			 
			//Encode the array into JSON.
			$jsonDataEncoded = json_encode($params);
			 
			//Tell cURL that we want to send a POST request.
			curl_setopt($ch, CURLOPT_POST, 1);
			 
			//Attach our encoded JSON string to the POST fields.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
			 
			//Set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
			 
			//Execute the request
			$result = curl_exec($ch);
			if($result == 1) {
				header('location: chart.php?room='.$_SESSION['curRoom']);
			}
			print_r($result); 
		}
	}

?>