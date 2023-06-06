<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['CampaignID']))
	{
		$Query = "SELECT status FROM smsque".
			" WHERE clientid = ".$_SESSION[SessionID."ClientID"]." AND smsqueid = ".$_REQUEST['CampaignID'];
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Campaign Detail";
			goto Response;
		}
		$objRow = mysqli_fetch_object($rstRow);
		if ($objRow->status == 0)
		{
			$RespHead = "Error";
			$RespText = "This Campaign Cannot Restart. Campaign is Still Running";
			goto Response;
		}
		if ($_POST['Restart'] == "Check")
		{
			$RespHead = "Yes";
			$RespText = "";
			goto Response;
		}
		else
		{
			$Query = "UPDATE smsquelist SET smssent = NULL WHERE smsqueid = ".$_REQUEST['CampaignID'];
			@mysqli_query($Conn,$Query);
			$RespHead = "Done";
			$RespText = "Campaign Restarted Successfully.";
		}
	}
Response:
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation Restarted ...";
	}
	$Response = str_replace("[Status]",$RespHead,$Response);
	$Response = str_replace("[Message]",$RespText,$Response);
	echo($Response);
?>