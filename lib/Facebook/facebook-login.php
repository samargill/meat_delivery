<?php
	require_once  'autoload.php';
	$fb = new \Facebook\Facebook(['app_id' => '1650071148456658','app_secret' => '2d248a82626e2f378838325cf66673c4','default_graph_version' => 'v4.0']);

	
	   //$helper = 
	//   $helper = $fb->getJavaScriptHelper();
	//   $helper = $fb->getCanvasHelper();
	//   $helper = $fb->getPageTabHelper();
	
	$helper = $fb->getJavaScriptHelper();
	echo "Access Token : ".$helper;
	try
	{
		
    	$accessToken = $helper->getAccessToken();
    	
    	
    }
    catch(\Facebook\Exceptions\FacebookResponseException $e)
    {
    	// When Graph returns an error
    	echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	}
	catch(\Facebook\Exceptions\FacebookSDKException $e)
	{
    	// When validation fails or other local issues
    	echo 'Facebook SDK returned an error: ' . $e->getMessage();
    	exit;
	}
	die;
	if (isset($accessToken))
	{
    	// Logged in!
    	$_SESSION['facebook_access_token'] = (string) $accessToken;
    	echo 'You are now logged in!';
    	echo '<br> the access token is: ' . $accessToken . '<br>';
    	// Now you can redirect to another page and use the
    	// access token from $_SESSION['facebook_access_token']
	}
	try
	{
	  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
	  // If you provided a 'default_access_token', the '{access-token}' is optional.
	  $response = $fb->get('/me', $accessToken);
	}
	catch(\Facebook\Exceptions\FacebookResponseException $e)
	{
	  // When Graph returns an error
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	}
	catch(\Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}

	$me = $response->getGraphUser();
	echo 'Logged in as ' . $me->getName();
?>