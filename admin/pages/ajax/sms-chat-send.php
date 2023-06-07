<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."../lib/Phone-Validator/vendor/autoload.php");
	include($PagePath."../lib/sms.php");

	// Send Json Response
	$JsonError  = "".
		"{".
			"\"DocType\": \"[Status]\",".
			"\"DocDate\": \"".date("Y-m-d H:i:s")."\",".
			"\"Status\": \"[Status]\",".
			"\"Message\": \"[Message]\"".
		"}";
	if (!(isset($_POST['ClientMobID']) && isset($_POST['Mobile']) && isset($_POST['Message'])))
	{
		$RespHead = "Error";
		$RespText = "Error [1]";
		goto Response;
	}
	$ClientMobID = $_POST['ClientMobID'];
	// Start New Package System Checks. These Checks only Apply When package id in (6 || 7 || 8 || 9)
	$UserPkgID	= intval(GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]));
	if ($UserPkgID == 6 || $UserPkgID == 7 || $UserPkgID == 8 || $UserPkgID == 9)
	{
		// Get Current Timestamp
		$Today 	= time();
		// Check User Package Expiry
		$UserPkgExp	= strtotime(GetValue("pkgexpiry", "client", "clientid = ".$_SESSION[SessionID."ClientID"]));
		if ($Today > $UserPkgExp)
		{
			$RespHead = "Error";
			$RespText = "Please Renew Your Package <ul><li>Your Package is Expired</li></ul>";
			goto Response;
		}
		// Check User Sim Package Expiry
		if ($UserPkgID == 7)
		{
			$Query  = "SELECT COD.simexpiry".
				" FROM clientorder CO".
				" INNER JOIN clientorderdetail COD ON CO.orderid = COD.orderid".
				" WHERE CO.clientid = ".$_SESSION[SessionID."ClientID"]." AND COD.simexpiry IS NOT NULL". 
				" ORDER BY COD.orderid DESC".
				" LIMIT 1";
			$rstSimExp = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstSimExp) > 0)
			{
				$objSimExp  = mysqli_fetch_object($rstSimExp);
				$UserSimExp = $objSimExp->simexpiry;
				$UserSimExp = strtotime($UserSimExp);
				if ($UserSimExp < $Today)
				{
					$RespHead = "Error";
					$RespText = "Please Renew Your Sim Package <br>Your Sim Package is Expired";
					goto Response;
				}	
			}
		}
		// Get Total SMS Allowed to the user based on his balance
		$SmsQueID = GetValue("smsqueid","smsque","mobileid=".$ClientMobID." AND clientid = ".$_SESSION[SessionID."ClientID"]);
		$SmsSent  = $SmsSent = $SmsRecv = 0;
		if ($SmsQueID != "")
		{
			if ($UserPkgID == 9)
			{
				// Count total Send SMS from Client to his/him Specific Audience (Demo Package)
				$QueSend = "SELECT COUNT(*) As Sent".
					" FROM smsquelist".
					" WHERE smsqueid = ".$SmsQueID;
				// if (DBUserName != "myitvh")
				// {
				// 	$QueSend .= " AND smssent IS NOT NULL"; 
				// }
				$rstSend = mysqli_query($Conn,$QueSend);	
				$objSend = mysqli_fetch_object($rstSend);
				$SmsSent = $objSend->Sent;
				// Get Total Alowed Sms in Demo Package
				$DemoSmsLimit = GetValue("minsmsqty","package_rate","pkg_field_id = 8 AND pkgid = 9");
				if ($SmsSent >= $DemoSmsLimit)
				{
					$RespHead = "Message";
					$RespText = "<br>Free Sms Limit is Finished <br> Please Renew Your Package";
					goto Response;	
				} 
			}
		}
	}  // End New Package System Checks
	
	$CustomerMob = $_POST['Mobile'];
	$SentTo = preg_replace("/[^+\d]/", "", $CustomerMob);
	$PhoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
	try 
	{
		$ParsePhone = $PhoneUtil->parse($SentTo, "AU");
		
	}
	catch (\libphonenumber\NumberParseException $e) 
	{
		//var_dump($e);
		$RespHead = "Error";
		$RespText = "Error [2]".$e;
		goto Response;
	}
	if ($PhoneUtil->isValidNumber($ParsePhone))
	{
		$SentTo = $PhoneUtil->format($ParsePhone, \libphonenumber\PhoneNumberFormat::E164);
	}
	// Client Credentials
	$Query = "SELECT email, password FROM clientuser WHERE userid = ".$_SESSION[SessionID];
	$rstRow = mysqli_query($Conn,$Query);
	if (mysqli_num_rows($rstRow) == 0)
	{
		$RespHead = "Error";
		$RespText = "Error [2]";
		goto Response;
	}
	$objRow = mysqli_fetch_object($rstRow);
	$SmsQueID = 0;
	$Query = "SELECT smsqueid, status FROM smsque".
		" WHERE mobileid = ".$ClientMobID."".
		" AND clientid = ".$_SESSION[SessionID."ClientID"]."".
		" AND smsquename = 'SMS API'";
	$rstPro = mysqli_query($Conn,$Query);
	if (mysqli_num_rows($rstPro) == 0)
	{
		$Query = "INSERT INTO smsque".
			" (mobileid, smsquename, status)".
			" VALUES (".$ClientMobID.", 'SMS API', 1)";
		mysqli_query($Conn,$Query);
		$SmsQueID = mysqli_insert_id($Conn);
	}
	else
	{
		$objPro = mysqli_fetch_object($rstPro);
		$SmsQueID = $objPro->smsqueid;
	}
	if (trim($_REQUEST['Message']) == "")
	{
		$RespHead = "Error";
		$RespText = "Error [4]";
		goto Response;
	}
	$DeviceMobile = GetValue("mobileno","clientmobile","mobileid = ".$ClientMobID);
	$Json = "".
		"{".
			"\"DocType\": \"SendSMS\",".
			"\"DocDate\": \"".date("Y-m-d H:i:s")."\",".
			"\"UserName\": \"".$objRow->email."\",".
			"\"Password\": \"".$objRow->password."\",".
			"\"SMSQueID\": \"".$SmsQueID."\",".
			"\"DeviceID\": \"".$DeviceMobile."\",".
			"\"SendTo\": \"".$SentTo."\",".
			"\"Message\": \"".preg_replace("/\r\n/","\n", $_REQUEST['Message'])."\",".
			"\"SendTime\": \"\"".
		"}";
	$Url = WebsiteUrl."/api/sendsms";
	$Response = SendXML($Url,$Json,0);
	$Json = json_decode($Response,true);
	$Result = $Json["Message"];
	//var_dump($Response);
	//die();
	if ($Result == "Sent")
	{
		$RespHead = "Done";
		$RespText = "Sent";
		goto Response;
	}
	else
	{
		$RespHead = "Error";
		$RespText = "Error [5]-".$Result;
		goto Response;
	}
Response:
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation ...";
	}
	$JsonError = str_replace("[Status]",$RespHead,$JsonError);
	$JsonError = str_replace("[Message]",$RespText,$JsonError);
	echo($JsonError);
?>