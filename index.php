<?php
	$uri	= $_SERVER["REQUEST_URI"];
	$parts	= explode("/", $uri);

	if($parts[1] == 'api') {
		if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
			http_response_code(401);
			exit();
		}

		$method		= $_SERVER['REQUEST_METHOD'];
		$endpoint	= $parts[2]??null;
		$body		= json_decode(file_get_contents('php://input'), true);
		$token		= ltrim($_SERVER['HTTP_AUTHORIZATION'], "Basic ");

		//Note: no autoloader for simplicity
		include 'app/api.php';
		$api = new API($method, $endpoint, $token, $body);
		$api->processRequest();
	} else {
		http_response_code(404);
		exit();
	}
?>