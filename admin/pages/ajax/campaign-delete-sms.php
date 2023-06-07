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
		// 	" FROM smsque SQ".
		// 	" INNER JOIN clientmobile CM ON SQ.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SQ.smsqueid = ".$SmsQueID;
		$Query = "SELECT SQ.status".
			" FROM smsque SQ".
			" INNER JOIN clienthavemob CHM ON SQ.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SQ.smsqueid = ".$SmsQueID;
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Campaign Detail";
			goto Response;
		}
		$Query = "DELETE FROM smsquelist WHERE smsqueid = ".$SmsQueID." AND smsid = ".$_POST['SmsID'];
		@mysqli_query($Conn,$Query); 
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Delete SMS From Campaign<br><br>".mysqli_error($Conn)."<br><br>".$Query;
			goto Response;
		}
		$RespHead = "Done";
		$RespText = "SMS Removed From Campaign Successfully.";
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