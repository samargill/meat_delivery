<?php
	define("DBUserName","root");
	$Conn = mysqli_connect("localhost",DBUserName,"rootroot");
	if (!$Conn)
	{
		echo("Unable To Connect To Server. Please Refresh The Page To Reload Website");
		exit;
	}
	if (!mysqli_select_db($Conn,DBName))
	{
		echo("Unable To Connect To Data Server. Please Refresh The Page To Reload Website");
		exit;
	}
	/*$Query = "SET time_zone = 'Australia/Sydney'";
	mysqli_query($Conn,$Query);*/
	if (isset($_SERVER['HTTP_USER_AGENT']))
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'],"{AppView/MeatDlv/Apple}") > 0)
		{
			define("AppView","True");
			define("AppType","Apple");
		}
		elseif (strpos($_SERVER['HTTP_USER_AGENT'],"{AppView/MeatDlv}") > 0 || strpos($_SERVER['HTTP_USER_AGENT'],"{AppView/MeatDlv/Android}") > 0)
		{
			define("AppView","True");
			define("AppType","Android");
		}
	}
?>