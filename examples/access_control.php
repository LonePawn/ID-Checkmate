<?php
	// Don't cache the page. Page will always be requested to ensure authentication variables wont become obsolete.
	header("Cache-Control", "no-cache, no-store, must-revalidate");

	// initialise session
	session_start();

	// set a global path to the ID-Checkmate server
	$idc_root = 'http://id-checkmate.tech/';

	// get the full url of this page (i.e. the page you want to be loaded by ID-Checkmate if authentication is successful)
	$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

	// check to see if user has just logged out
	$logOutLandingPage = isset($_GET['logOutLanding']) ? $_GET['logOutLanding'] : '';
	if($logOutLandingPage != '')
	{
		// user has just logged out therefore unset all session variables and redirect to the log out landing page
		session_unset();
		header("Location: ".$logOutLandingPage);
	}

	// Check to see if user is already logged in
	$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
	if ($email == '')
	{
		// user is not logged in. So check to see if user has just logged in from ID-Checkmate. 
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$cachedKey = isset($_SESSION['cachedKey']) ? $_SESSION['cachedKey'] : '';
		$responseKey = isset($_POST['responseKey']) ? $_POST['responseKey'] : '';

		if($responseKey != '' && $email != '')
		{
			//User has just attempted logging in
			//Use the security key to ensure the login response is coming from ID-Checkmate
			if($cachedKey != $responseKey)
			{
				// The response is not from ID-Checkmate.
				// Deny access to the page as user is attempting to hijack another person's account.

				// You could build more advanced custom code here if you like. E.g track IP address of impersonator, Keep a log of attempted attacks, Display a custom security page letting the attacker know you are aware of his/her attack (That could sometimes scare the attacker away) etc.

				//For now, the exit function just ends code execution here and the impersonator is presented with a blank screen
				exit();
			}
			else
			{
				// user has just successfully logged in. Therefore maintain his session.
				$_SESSION['email'] = $email;
			}

		}
		else
		{
			// user has just innocently launched your page prior to login attempt.

			/*
			Redirect the user to ID-Checkmate for login attempt. When redirecting, we'll need to create a secret key, keep a copy of the key and send a copy of the secret key together with the url of your page to ID-Checkmate. After the user is authenticated by ID-Checkmate, the result of the authentication process is sent back to your page (the reason we send your page url to ID-Checkmate) together with a copy of the secret key. Now, if the response secret key matches your saved secret key, we know the response is from ID-Checkmate.
			*/

			$secretKey = uniqId();	// we'll use this for security purpose to ensure the authentication response we get is indeed from ID-Checkmate.

			// store the secretKey in a session variable.
			$_SESSION['cachedKey'] = $secretKey;

			
			// use javascript to create a form and post the authentication request parameters to ID-Checkmate.
			// Since javascript runs on the client side, we'll have to spill out some HTML code here ...
			echo
			'	
				<html>
					<head>
						<title>Authorisation Request</title>
						<script type = "text/javascript">
							function sendAuthenticationRequest()
							{
								var authForm = document.getElementById("authForm");
								authForm.submit();
							}
							</script>
					</head>
					<body onload = "sendAuthenticationRequest();">
						<form action = "'.$idc_root.'idc_processAuthenticationRequest.php" method = "post" id = "authForm">
							<input type = "password" value = "'.$secretKey.'" id = "secretKey" name = "secretKey" style = "display: none;"/> 
							<input type = "text" value = "'.$current_url.'" id = "pageUrl" name = "pageUrl" style = "display: none;"/>
						</form>
						
					</body>
				</html>
			';
		}	
	}

?>