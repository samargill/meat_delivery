<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['SmsID']))
	{
		$SmsQueID = GetValue("smsqueid","smsquelist","smsid = ".$_POST['SmsID']);
		// $Query = "SELECT SQ.status".
		// 	" FROM smsque SQ INNER JOIN clientmobile CM ON SQ.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SQ.smsqueid = ".$SmsQueID;
		$Query = "SELECT SQ.status".
			" FROM smsque SQ".
			" INNER JOIN clientmobile CM ON SQ.mobileid = CM.mobileid".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SQ.smsqueid = ".$SmsQueID;
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Campaign Detail";
			goto Response;
		}
		$Query = "UPDATE smsquelist SET getapp = 0".
			" WHERE smsid = ".$_POST['SmsID']." AND getapp = 1 AND smssent IS NULL";
		mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Re-Send The SMS<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			$RespHead = "Done";
			$RespText = "SMS Marked To Be Re-Sent Successfully.";
		}
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