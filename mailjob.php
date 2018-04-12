 <?php include('server.php') ?>
<?php 
 
	$keypass = $_GET['keypass'];
	//echo 'keypass:'.$keypass;
	if($keypass =='ntR@simed')
	{
		 
		$user_check_query = "select * from energy  ORDER BY month DESC LIMIT 1";
		//echo $user_check_query;
		$result = mysqli_query($db, $user_check_query);
		if (!$result) {
			echo "Could not successfully run query ($sql) from DB: " . mysql_error();
			exit;
		}

		if (mysqli_num_rows($result) == 0) {
			echo "No rows found, nothing to print so am exiting";
			exit;
		}
		$rowcount=mysqli_num_rows($result);
		if ($rowcount == 0) {
			echo "No rows found, nothing to print so am exiting";
			exit;
		}		
		//printf("Result set has %d rows.\n",$rowcount);
  
		$row = mysqli_fetch_assoc($result);
		$savedEnergy	= $row['savedEnergy'];
		if($savedEnergy == '' || $savedEnergy < 0) {
			exit;
		}
		//echo 'savedEnergy: '.$savedEnergy;
		// Free result set
		mysqli_free_result($result);

		$content = "Respectful Ms. Silvia Ussai, please find your weekly report. Summary energy compliance: ".$savedEnergy."%. Best regards, Atisan";
		$url = 'http://simed10-trieste.7e14.starter-us-west-2.openshiftapps.com/spring-mvc-angularjs/api/sendMail/';
		$email = 'vunguyenthe1976@gmail.com';
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
		if($result) {
			echo "send mail success".'<br>';
		}
		
		//send next 
		
		$email = 'robert.raufer@gmail.com';
		$params = array(
			'email' => $email,
			'content' => $content,
		);
		 

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
		if($result) {
			echo "send mail success".'<br>';;
		}		
		
	}

?>