<?php
	include("../lib/variables.php");
	include("../lib/opencon.php");
	include("../lib/session.php");
	include("../lib/functions.php");
	include("../lib/functionsdb.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\" }";
	if (isset($_REQUEST['Parent']))
	{
		if ($_REQUEST['Parent'] == "SaveSetting")
		{
			$Query = "UPDATE websettings SET".
				"  config_value = '".TrimText($_REQUEST['ConfigValue'],1)."'".
				", lastedit     = NOW()".
				"  WHERE config_id = ".$_REQUEST['ConfigID'];
			mysqli_query($Conn,$Query);
			$Response = str_replace("[Status]","Done",$Response);
			$Response = str_replace("[Message]","Setting saved successfully.",$Response);
			echo($Response);
			die;
		}
	}
	if (strpos($Response,"[Status]") > 0)
	{
		$Response = str_replace("[Status]","Error",$Response);
		$Response = str_replace("[Message]","Undefined Operation ...",$Response);
		echo($Response);
		die;
	}
?>