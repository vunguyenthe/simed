<?php
	$url = 'http://simed10-trieste.7e14.starter-us-west-2.openshiftapps.com/spring-mvc-angularjs/api/sendMail/';
	$params = array(
		'email' => 'robert.raufer@gmail.com',
		'content' => 'hello world',
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

	print_r($result); 
		
?>		