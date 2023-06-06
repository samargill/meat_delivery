<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['ContactID']))
	{
		$Query = "DELETE FROM clientcontact".
			" WHERE clientid = ".$_SESSION[SessionID."ClientID"].
			" AND contactid = ".$_REQUEST['ContactID'];
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Unable To Delete Contact<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			$RespHead = "Done";
			$RespText = "Contact Deleted Successfully.";
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