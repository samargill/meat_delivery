<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['ContactID']))
	{
		if ($_POST['ContactID'] == 0)
			$AddNew = true;
		else
			$AddNew = false;
		$Query = "SELECT contactid FROM clientcontact".
			" WHERE clientid = ".$_SESSION[SessionID."ClientID"].
			" AND mobile = ".$_POST['FullMobile'];
		if ($AddNew == false)
		{
			$Query .= " AND contactid <> ".$_POST['ContactID'];
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$RespHead = "Error";
			$RespText = "Same Mobile # Already Exitst";
			goto Response;
		}
		if ($AddNew == true)
		{
			$Query = "INSERT INTO clientcontact".
				" (clientid, fullname, mobile, adddate, lastedit)".
				" VALUES (".$_SESSION[SessionID.'ClientID'].", '".TrimText($_POST['txtName'],1)."',".
				" ".$_POST['FullMobile'].", NOW(), NOW())";
		}
		else
		{
			$Query = "UPDATE clientcontact SET".
				"  fullname  = '".TrimText($_POST['txtName'],1)."'".
				", mobile    =  ".$_POST['FullMobile'].
				", lastedit  =  NOW()".
				"  WHERE contactid = ".$_POST['ContactID'];
		}
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			if ($AddNew == true)
				$RespText = "Unable To Add New Contact";
			else
				$RespText = "Unable To Save Contact";
			$RespText .= "<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			$RespHead = "Done";
			if ($AddNew == true)
				$RespText = "New Contact Added Successfully.";
			else
				$RespText = "Contact Detail Saved Successfully.";
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