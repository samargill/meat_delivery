<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['CampaignID']))
	{
		$Query = "SELECT SQ.status".
			" FROM smsque SQ".
			" INNER JOIN clientmobile CM ON SQ.mobileid = CM.mobileid".
			" WHERE SQ.clientid = ".$_SESSION[SessionID."ClientID"]." AND SQ.smsqueid = ".$_REQUEST['CampaignID'];
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Campaign Detail";
			goto Response;
		}
		$Query = "DELETE FROM smsque WHERE smsqueid = ".$_REQUEST['CampaignID'];
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Delete Campaign<br><br>".mysqli_error($Conn)."<br><br>".$Query;
			goto Response;
		}
		$Query = "DELETE FROM smsquelist WHERE smsqueid = ".$_REQUEST['CampaignID'];
		@mysqli_query($Conn,$Query);
		$RespHead = "Done";
		$RespText = "Campaign Deleted Successfully.";
	}
Response:
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation ...";
	}
	$Response = str_replace("[Status]",$RespHead,$Response);
	$Response = str_replace("[Message]",$RespText,$Response);
	echo($Response);
?>