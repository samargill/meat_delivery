<?php
	$PagePath = "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "".
		"{".
			"\"Status\": \"[Status]\",".
			"\"Message\": \"[Message]\",".
			"\"ReturnID\": \"[ReturnID]\"".
		"}";
	if (isset($_POST['Validate']))
	{
		$Editable = "";
		$Query = "SELECT CU.verifydate, C.status,".
			" ROUND((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(C.adddate)) / (24 * 60 * 60),2) As AddDays".
			" FROM client C INNER JOIN clientuser CU ON C.clientid = CU.clientid AND CU.usertype = 1".
			" WHERE C.clientid = ".$_REQUEST['ClientID'];
		$rstCli = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstCli) == 0)
		{
			$Editable = "Unable To Find Client Data ...";
			goto Response;
		}
		$objCli = mysqli_fetch_object($rstCli);
		if ($objCli->status == 2)
		{
			goto Response;
		}
		if ($objCli->verifydate == null)
		{
			if ($objCli->AddDays <= 30)
			{
				$Query = "SELECT logid FROM clientemaillog WHERE clientid = ".$_REQUEST['ClientID']." AND emailtype = 1";
				$rstRow = mysqli_query($Conn,$Query);
				if (mysqli_num_rows($rstRow) == 0)
				{
					$Editable = "Client Created ".$objCli->AddDays." Days Ago<br><br><b>Re-Send Him Verify Email</b>";
					goto Response;
				}
				else
				{
					$Editable = "Client Created ".$objCli->AddDays." Days Ago".
						"<br><br>Verify Email is Already Resent<br><br>Wait For ".(7 - floor($objCli->AddDays))." More Days May User Verify If Not Then Delete";
					goto Response;
				}
			}
			goto Response;
		}
		$HasDevs = 0;
		$Query = "SELECT CM.mobileid, CM.token".
			" FROM clientmobile CM".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.clientid".
			" WHERE CHM.clientid = ".$_REQUEST['ClientID'];
		$HasDevs = mysqli_num_rows(mysqli_query($Conn,$Query));
		$HasQues = 0;
		$Query = "SELECT smsqueid".
			" FROM smsque WHERE mobileid IN".
			" (SELECT CM.mobileid FROM clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.clientid WHERE CHM.clientid = ".$_REQUEST['ClientID'].")";
		$HasQues = mysqli_num_rows(mysqli_query($Conn,$Query));
		$HasSms = 0;
		$Query = "SELECT smsid, smsaddtime, smssent FROM smsquelist".
			" WHERE smsqueid IN (SELECT smsqueid FROM smsque WHERE mobileid".
			" IN (SELECT CM.mobileid FROM clientmobile CM INNER JOIN  clienthavemob CHM ON CM.mobileid = CHM.clientid WHERE CHM.clientid = ".$_REQUEST['ClientID']."))".
			" ORDER BY smsaddtime DESC LIMIT 0,1";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objSms = mysqli_fetch_object($rstRow);
			$HasSms = 1;
		}
		if ($objCli->AddDays <= 30)
		{
			$Editable = "Client Email is Verified ".$objCli->AddDays." Days Ago ...<br><br>Delete This Client After 30 Days<br><br>";
			goto Response;
		}
Response:
		if ($Editable == "")
		{
			$RespHead = "Delete";
			$RespText = "Are You Sure You Want To Delete This Client & All Related Data ?";
			$ReturnID = "Yes";
		}
		else
		{
			$RespHead = "Unable To Delete";
			$RespText = "This Client Cannot Be Deleted.".
				"<br><br>".$Editable.".";
			$ReturnID = "No";
		}
	}
	elseif (isset($_POST['Apply']))
	{
		$Query = "DELETE FROM smsquelist".
			" WHERE smsqueid IN (SELECT smsqueid FROM smsque WHERE clientmobid IN".
			" (SELECT clientmobid FROM clientmobile WHERE clientid = ".$_REQUEST['ClientID']."))";
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM smsque WHERE clientmobid IN".
			" (SELECT clientmobid FROM clientmobile WHERE clientid = ".$_REQUEST['ClientID'].")";
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM smsreclist WHERE clientmobid IN".
			" (SELECT clientmobid FROM clientmobile WHERE clientid = ".$_REQUEST['ClientID'].")";
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientcartdetail WHERE cartid IN".
			" (SELECT cartid FROM clientcart WHERE clientid = ".$_REQUEST['ClientID'].")";
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientcontact WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientcart WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientemaillog WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientmobile WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientmenurights WHERE adminid IN (SELECT userid FROM clientuser WHERE clientid = ".$_REQUEST['ClientID'].")";
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM clientuser WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		$Query = "DELETE FROM client WHERE clientid = ".$_REQUEST['ClientID'];
		mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) == 0)
		{
			$RespHead = "Done";
			$RespText = "Client Deleted Successfully.";
		}
		else
		{
			$RespHead = "Error";
			$RespText = "An Unexpected Error Occured. ".mysqli_error($Conn);
		}
	}
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation ...";
	}
	$Response = str_replace("[Status]",$RespHead,$Response);
	$Response = str_replace("[Message]",$RespText,$Response);
	if (isset($ReturnID))
	{
	$Response = str_replace("[ReturnID]",$ReturnID,$Response);
	}
	echo($Response);
?>