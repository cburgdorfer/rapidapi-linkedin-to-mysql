<?php

include_once("./credentials.php");

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://linkedin-company-data.p.rapidapi.com/linkedInCompanyData",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => "{\n    \"liUrls\": [\n        \"".LINKEDIN_URL."\"    ]\n}",
	CURLOPT_HTTPHEADER => [
		"content-type: application/json",
		"x-rapidapi-host: linkedin-company-data.p.rapidapi.com",
		"x-rapidapi-key: " . RAPIDAPI_KEY
	],
]);

$response = json_decode(curl_exec($curl));
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	$followerCount = $response[0]->{'FollowerCount'};
	
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	if($mysqli->connect_error) {
		error_log("Connection failed: " . $mysqli->connect_error);
	  	die("Connection failed: " . $mysqli->connect_error);
	}
	
	$sql = "INSERT INTO ".TABLE_NAME." 
		(AreaName, LayerName, DataKind, PhysicalDeviceName, PhysicalDeviceId, DataId, Timestamp, Data)
	VALUES 
		('LinkedIn', 'FollowerCount', 'Integer', 'LinkedIn', 'RapidAPI', 'DataId', ?, ?)";
		
	$stmt = $mysqli->prepare($sql);
	
	$stmt->bind_param('ss', date("Y-m-d H:i:s"), 	// timestamp
							$followerCount);		// data

	if(!$stmt->execute()) {
		error_log("Error: " . $stmt->error);
	}

	$stmt->close();
}
