<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['CampaignID']))
	{
		if ($_POST["Status"] == 0)
		{
			$StatusText = "Stop";
			$Status = 1;
		}
		else
		{
			$StatusText = "Start";
			$Status = 0;
		}
		// Check Campaign
		// $Query = "SELECT SQ.smsqueid, CM.token".
		// 	" FROM smsque SQ".
		// 	" INNER JOIN clientmobile CM ON SQ.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" AND SQ.smsqueid = ".$_POST['CampaignID'];
		$Query = "SELECT SQ.smsqueid, CM.token".
			" FROM smsque SQ".
			" INNER JOIN clientmobile CM ON SQ.mobileid = CM.mobileid".
			" WHERE SQ.clientid = ".$_SESSION[SessionID."ClientID"].
			" AND SQ.smsqueid = ".$_POST['CampaignID'];
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$RespHead = "Error";
			$RespText = "Invalid Campaign Detail";
			goto Response;
		}
		$objRow = mysqli_fetch_object($rstRow);
		$Token  = $objRow->token;
		// Send Notification
		if (DBUserName != "myitvh")
		{
			$FCMResponse = 1;
		}
		else
		{
			$ApiUrl = WebsiteUrl."/api/sendFCM.php?id=".$Token;
			$JsonResp = SendXML($ApiUrl,"");
			if (substr($JsonResp,0,10) == "CURL Error")
			{
				$RespHead = "Error";
				$RespText = "Failed To Send Notification To Bullky SMS Device. Check Device & Try Again ...";
				goto Response;
			}
			$Json = json_decode($JsonResp,true);
			$FCMResponse = $Json["success"];
		}
		if ($FCMResponse == 1)
		{
			// Change Status
			$Query = "UPDATE smsque SET status = ".$Status." WHERE smsqueid = ".$_REQUEST['CampaignID'];
			@mysqli_query($Conn,$Query);
			if (mysqli_errno($Conn) > 0)
			{
				$RespHead = "Error";
				$RespText = "Unable To ".$StatusText." Campaign<br><br>".mysqli_error($Conn)."<br><br>".$Query;
				goto Response;
			}
			else
			{
				// Start checks for package ID 6,7,8 (Topup System)
				$ClientID = $_SESSION[SessionID."ClientID"];
				$PkgID    = GetValue("pkgtype","client","clientid= ".$ClientID);
				$Query 	  = "SELECT * FROM smsquelist WHERE smsqueid = ".$_REQUEST['CampaignID']." AND smssent IS NULL";
				$rstRow   = mysqli_query($Conn,$Query);
				if (mysqli_num_rows($rstRow) > 0)
				{
					while (@$objRow = mysqli_fetch_object($rstRow))
					{
						$Message   = $objRow->smstext;
						$SmsID 	   = $objRow->smsid;
						$SmsQueID  = $objRow->smsqueid;
						if ($PkgID == 6 || $PkgID == 7 || $PkgID == 8)
						{
							// Total message count based on sms characrter
							$Maxsmschar	  = GetValue("maxsmschar", "client", "clientid = ".$ClientID);
							$CountMsgChar = strlen($Message);
							$CountTtlMsg  =	ceil($CountMsgChar / $Maxsmschar);
							// Get Client Details Based on Pacakage Expiry
							$QueUserBal = "SELECT COD.orderid, COD.smssendrate, COD.balance, COD.smssendlimit".
								" FROM clientorderdetail COD".
								" INNER JOIN clientorder CO ON COD.orderid = CO.orderid".
								" WHERE COD.pkgexpiry > NOW() AND CO.clientid =".$ClientID;
							$rstUserBal = mysqli_query($Conn,$QueUserBal);
							while($objUserBal = mysqli_fetch_object($rstUserBal))
							{		
								$UserOrderID  = $objUserBal->orderid;
								$SmsSendRate  = $objUserBal->smssendrate;
								$SmsSendLimit = $objUserBal->smssendlimit;
								$RemainingSms = $SmsSendLimit - $CountTtlMsg;
								// Get Client Total Sms Send limit
								$QueTtlSms = "SELECT SUM(COD.smssendlimit) As TtlMsgLimit".
									" FROM clientorder CO".
									" INNER JOIN clientorderdetail COD ON CO.orderid = COD.orderid".
									" WHERE CO.clientid =".$ClientID." AND COD.pkgexpiry > NOW()";
								$rstTtlSms = mysqli_query($Conn,$QueTtlSms);
								$objTtlSms = mysqli_fetch_object($rstTtlSms);
								if ($objTtlSms->TtlMsgLimit >= $CountTtlMsg)
								{
									if ($RemainingSms >= 0)
									{
										$TtlMsgAmount = $CountTtlMsg * $SmsSendRate;
										// Deduct Client Balance and Decrease Smssndlimit based on sending sms
										$QueDeductBal = "UPDATE clientorderdetail SET".
											" balance = balance - $TtlMsgAmount,".
											" smssendlimit = smssendlimit - $CountTtlMsg".
											" WHERE orderid=".$UserOrderID;
										mysqli_query($Conn,$QueDeductBal);
										break;				
									}
									elseif ($RemainingSms < 0)
									{
										$RemainingMsg 	  = $CountTtlMsg - $SmsSendLimit;
										$CountTtlSendSms  = $CountTtlMsg - $RemainingMsg;
										$TtlSendSmsAmount = $CountTtlSendSms * $SmsSendRate;
										$QueDeductBal 	  = "UPDATE clientorderdetail SET".
											" balance = balance - $TtlSendSmsAmount,".
											" smssendlimit = smssendlimit - ".$CountTtlSendSms."".
											" WHERE orderid = ".$UserOrderID;
										mysqli_query($Conn,$QueDeductBal);
										$CountTtlMsg = $RemainingMsg;
										if($CountTtlMsg < 0)
										{
											break;
										}
									}
								}
								else
								{
									$RespHead = "Error";
									$RespText = "Please Recharge your balance<br> Your currnet Balance is not suffciet for sending this Whole Campaign";
									goto Response;
								}
							}
						}
						$Querystatus  = "UPDATE smsquelist SET".
							" status = 1".
							" WHERE smsid =".$SmsID." AND smsqueid=".$SmsQueID;
						mysqli_query($Conn,$Querystatus);
					}
				}
				// End checks for package ID 6,7,8 (Topup System)
			}
		}
		// Return Response
		$RespHead = "Done";
		if ($_POST["Status"] == 0)
			$RespText = "Campaign Stopped Successfully.";
		else
			$RespText = "Campaign Started Successfully.";
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