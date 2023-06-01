<?php
	//Include Google Client Library 
	include_once("src/Google_Client.php");
	include_once("src/contrib/Google_Oauth2Service.php");
	// Get Details Google Dev Console - OAuth Client
	$ClientID = "457824596613-a7cnstj86ult736ht37v4qo2gp8a2ehi.apps.googleusercontent.com";
	$ClientSecret = "4FVn5kyQi8lUMv6LBqO8NaVK";
	$CallBackUrl = "https://www.primemedic.com.au/login-google";
	//Call Google API
	$gClient = new Google_Client();
	$gClient->setApplicationName('Login to PrimeMedic.com');
	$gClient->setClientId($ClientID);
	$gClient->setClientSecret($ClientSecret);
	$gClient->setRedirectUri($CallBackUrl);
	$google_oauthV2 = new Google_Oauth2Service($gClient);
?>