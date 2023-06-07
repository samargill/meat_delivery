<?php
	define("DBUserName","root");
	// Connect To Mysql
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
	// Set User TimeZone
	if (isset($_SESSION[SessionID."Zone"]))
	{
		$TimeZone = $_SESSION[SessionID."Zone"];
	}
	else
	{
		$TimeZone = "Australia/Sydney";
	}
	$Query = "SET time_zone = '".$TimeZone."'";
	mysqli_query($Conn,$Query);
	// Load Settings
	LoadSettings();
	function LoadSettings()
	{
		if ($GLOBALS["Conn"])
		{
			$Query = "SELECT config_id, config_name, config_value".
				" FROM websettings WHERE config_id IN (1, 2, 4, 8, 9, 10)".
				" ORDER BY config_id";
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			while ($objRow = mysqli_fetch_object($rstRow))
			{
				switch ($objRow->config_id)
				{
					case 1:
						define("WebsiteTitle",$objRow->config_value);
						break;
					case 2:
						define("WebsitePhone",$objRow->config_value);
						break;
					case 4:
						define("WebsiteEmail",$objRow->config_value);
						break;
					case 8:
						define("Website",$objRow->config_value);
						break;
					case 9:
						define("WebsiteAddress1",$objRow->config_value);
						break;
					case 10:
						define("WebsiteAddress2",$objRow->config_value);
						break;
				}
			}
		}
	}