<?php
	$PagePath = "../../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_REQUEST['Parent']))
	{
		if ($_REQUEST['Parent'] == "SaveSetting")
		{
			$Query = "UPDATE websettings SET".
				"  config_value = '".TrimText($_REQUEST['ConfigValue'],1)."'".
				", lastedit     = NOW()".
				"  WHERE config_id = ".$_REQUEST['ConfigID'];
			mysqli_query($Conn,$Query);
			if (mysqli_errno($Conn) > 0)
			{
				$RespHead = "Error";
				$RespText = "Failed To Save Settings. Try Again ...<br><br>".mysqli_error($Conn);
			}
			else
			{
				$RespHead = "Done";
				$RespText = "Setting Saved Successfully.";
			}
			$Response = str_replace("[Status]",$RespHead,$Response);
			$Response = str_replace("[Message]",$RespText,$Response);
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