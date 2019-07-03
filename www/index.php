<?php
session_start();

require './OAuthSDK.php';

$credentials = yaml_parse_file('./credentials.yml');
//
$sdk = new OAuthSDK($credentials);

function getLinks($sdk)
{
	// display link connections to providers
	foreach ($sdk->getConnectionsLinks() as $providerName => $link) {
		echo "<a href=\"{$link}\"> connect with {$providerName} </a>";
	}
}

function confirm($sdk)
{
	$state = $_GET['state'];

	// state verification
	if (!($state === $_SESSION['state']))
		die("Your fucking state has been modified!");

	$provider = $_GET['provider'];
	// API Request
	$access_token_url = $sdk->getAccessTokenUrl($provider);
	var_dump($access_token_url); die();
	//
	$response = file_get_contents($access_token_url);

	$obj_response = json_decode($response);

	$access_token = $obj_response->access_token;

	$_SESSION['access_token'] = $access_token;

	$userInfos = $sdk->getUserInfos($provider);
	//
	header('Content-type: application/json');
	echo json_encode($userInfos, JSON_UNESCAPED_UNICODE);
}

// Kind of router
$path = strtok($_SERVER["REQUEST_URI"], '?');
switch($path) {
    case '/':
        getLinks($sdk);
        break;
    case '/confirm':
        confirm($sdk);
        break;
    // case '/auth-error':
    //     ();
    //     break;
    // case '/token':
    //     askToken();
    //     break;
}
