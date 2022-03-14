<?php

include ('functions.php');

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		$csrf = base64_encode(openssl_random_pseudo_bytes(32));
		setcookie("csrf_token", $csrf, time() + 1800, "/");
		echo $csrf;
	} else {
		include ('403.php');
		die();
	}

?>