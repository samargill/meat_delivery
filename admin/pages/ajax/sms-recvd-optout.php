<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['SmsID']))
	{
		// $Query = "SELECT SmsR.smsid, SmsR.clientmobid, SmsR.mobile".
		// 	" FROM smsreclist SmsR".
		// 	" INNER JOIN clientmobile CM ON SmsR.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" AND SmsR.smsid = ".$_POST['SmsID'];
		$Query = "SELECT SmsR.smsid, SmsR.mobileid, SmsR.mobile".
			" FROM smsreclist SmsR".
			" INNER JOIN clientmobile CM ON SmsR.mobileid = CM.mobileid".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
			" AND SmsR.smsid = ".$_POST['SmsID'];
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Sms Detail";
			goto Response;
		}
		$objRow = mysqli_fetch_object($rstRow);
		$Query = "INSERT INTO optout".
			" (mobileid, mobile, adddate)".
			" VALUES (".$objRow->mobileid.", ".$objRow->mobile.", NOW())";
		mysqli_query($Conn,$Query);
		$Query = "UPDATE smsreclist SET optout = 1 WHERE smsid = ".$_REQUEST['SmsID'];
		mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Opt-out The Sender<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			$RespHead = "Done";
			$RespText = "Sender Added To Opt-out List Successfully.";
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