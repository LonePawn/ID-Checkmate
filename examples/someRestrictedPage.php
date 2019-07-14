<?php
	//Add the following line at the beggining of any php page to implement ID-Checkmate Access Control feature on that page.
	include_once 'access_control.php';
?>

<html>
<head>
	<title>Some Restricted Page</title>
	
	<!-- Add the following javascript reference to your html head tag!-->
	<script type = "text/javascript" src = "http://id-checkmate.tech/access_control.js"></script>

</head>

<body>
	<h1>Some Restricted Page</h1>

	<h2>Welcome, you are logged in as <?= $email ?>  This was written from a HTML block</h2>
	<!-- Notice how we used the $email php variable to access the logged in user's email. You can access this variable anywhere within a restricted page to access the email of the logged in user. Also, remember the reason we used the less than - question mark - and equal sign (before the variable) and question mark - greater than sign (after the variable) is to let HTML know that the $email variable is a php variable. Hence, if you are using it in a php block, no need for the signs. An example is shown further below. !-->
	


	<!-- Use the following button to allow a logged in user to log out from a restricted page. Notice that the logOutDestination.html is just a sample page. You can design it however you want and the name can be anything you like. However, It should be an unrestricted page that gets loaded after the user is logged out. Although a html page is used here as an example, the logout destination could also be a php page or any other page type in fact. !-->
	<button type = "button" onclick = "logOut('<?=$current_url?>', 'logOutDestination.html');"> Log Out </button>
	<!-- Notice how we passed the path to the log out destination page as the second agrument to the javascript logOut function. You can change this parameter to whatever page url you want to be loaded when the user logs out.
	The first argument of the function on the other hand, is the full url of the current page. You don't have to replace the dynamic variable. It will automatically grab the url and place it for you. !-->
</body>

</html>

<?php
	// Notice this is a php block. So to access the logged in user's email, we simply use the $email variable without any signs.
	
	echo 'Bye bye '.$email.' This was written from a php block.'
?>