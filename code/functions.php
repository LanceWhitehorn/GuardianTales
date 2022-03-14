<?php

function checkCSRF() {
	// Check the CSRF token.
	// Compare the token from the ajax header $_SERVER['HTTP_TOKEN'] and the 'csrf_token' cookie
	if (isset($_SERVER['HTTP_TOKEN'])) {
		// https://stackoverflow.com/questions/38577338/get-ajax-header-information-in-php
		$token = $_SERVER['HTTP_TOKEN'];
	} else {
		include ('403.php');
		die();
	}
	if (!isset($_COOKIE['csrf_token']) || $token !== $_COOKIE['csrf_token']) {
		echo "<p><b>Error:</b> Please <a href='#' onClick='window.location.reload();return false;'>reload</a> the page.</p>";
		die();
	}
}


function userIP($access) {
	require_once ('mysql_connect.php');

    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
    	//ip from share internet
    	$ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Getting rough location
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"));
    $country = $geo["geoplugin_countryName"];
    $city = $geo["geoplugin_city"];

    if (strpos($ip, "192.168") === false) {
    	$sql = "INSERT INTO sessions(date, ip, country, city, access) VALUES (NOW(), '$ip', '$country', '$city', $access)";
    	$result = mysqli_query($dbc, $sql);
    }
}

?>