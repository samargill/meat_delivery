<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['UserID']))
	{
		$RespHead = "";
		$RespText = "";
		$UserPackage = GetValue("packageid","clientuser","clientid = ".$_SESSION[SessionID."ClientID"]);
		if ($UserPackage == 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Save Device. Please Subscribe Package To Add Devices ...";
			goto Response;
		}
		// Get Total Devices
		$TotalDevices = GetValue("COUNT(*) As Total","clientmob","clientid = ".$_SESSION[SessionID."ClientID"]);
		//Get Allowed Devices
		$AllowedDevices = GetValue("totaldevices","packages","packageid = ".$UserPackage." AND packageduration  = 1");

		if ($TotalDevices >= $AllowedDevices)
		{
			$RespHead = "Error";
			$RespText = "Maximum Limit Exceeded To Add Devices. Please Upgrade Your Package To Add Devices ...";
		}
Response:
		$Response = str_replace("[Status]",$RespHead,$Response);
		$Response = str_replace("[Message]",$RespText,$Response);
		echo($Response);
		die;
	}
	if (strpos($Response,"[Status]") > 0)
	{
		$Response = str_replace("[Status]","Error",$Response);
		$Response = str_replace("[Message]","Undefined Operation ...",$Response);
		echo($Response);
		die;
	}
?>