<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['DeviceID']))
	{
		mysqli_query($Conn,"BEGIN");
		$Query = "DELETE FROM clientmobile CM".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
			" AND CM.mobileid = ".$_REQUEST['DeviceID'];	
		@mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clienthavemob".
			" WHERE clientid = ".$_SESSION[SessionID."ClientID"].
			" AND mobileid = ".$_REQUEST['DeviceID'];
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			mysqli_query($Conn,"ROLLBACK");
			$RespHead = "Error";
			$RespText = "Unable To Delete Device<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			mysqli_query($Conn,"COMMIT");	
			$RespHead = "Done";
			$RespText = "Device Deleted Successfully.";
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