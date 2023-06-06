<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	// include($PagePath."lib/libphonenumber/vendor/autoload.php");

	if (isset($_REQUEST['BtnPressed']))
	{
		if ($_REQUEST["BtnPressed"] == "SendSms")
		{
			$Query  = "SELECT clientmobid, mobile FROM smsrecvd WHERE mobile LIKE '%".$_REQUEST['Mobile']."%'";
			$rstRow = mysqli_query($Conn,$Query);
			$objRow = mysqli_fetch_object($rstRow);
			$CustomerMob = $objRow->mobile;
			$ClientMobID = $objRow->clientmobid;
			$SentTo = preg_replace("/[^+\d]/", "", $CustomerMob);
			$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
			try 
			{
				$parseNumber = $phoneUtil->parse($SentTo, "AU");
			}
			catch (\libphonenumber\NumberParseException $e) 
			{
				var_dump($e);
			}
			if ($phoneUtil->isValidNumber($parseNumber))
			{
				$SentTo = $phoneUtil->format($parseNumber, \libphonenumber\PhoneNumberFormat::E164);
			}
			// Client Credentials
			$Query = "SELECT email, password FROM clients WHERE clientid = ".$_SESSION[SessionID];
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				$SMSQueID   = 0;
				$Query = "SELECT smsqueid, status FROM smsque".
					" WHERE clientmobid = ".$ClientMobID." AND smsquename = 'SMS API'";
				$rstPro = mysqli_query($Conn,$Query);
				if (mysqli_num_rows($rstPro) > 0)
				{
					$objPro = mysqli_fetch_object($rstPro);
					$SMSQueID = $objPro->smsqueid;
					if ($_REQUEST['txtMessage'] != "")
					{
						$DeviceMobile = GetValue("mobileno","clientmobile","clientmobid = ".$ClientMobID);
						$Json = "{".
									"\"DocType\": \"SendSMS\",".
									"\"DocDate\": \"".date("Y-m-d H:i:s")."\",".
									"\"UserName\": \"".$objRow->email."\",".
									"\"Password\": \"".$objRow->password."\",".
									"\"SMSQueID\": \"".$SMSQueID."\",".
									"\"DeviceID\": \"".$DeviceMobile."\",".
									"\"SendTo\": \"".$SentTo."\",".
									"\"Message\": \"".preg_replace("/\r\n/","\n", $_REQUEST['txtMessage'])."\",".
									"\"SendTime\": \"".$txtStartDate."\"".
								"}";
						if (DBUserName == "root")
							$url = "http://dv-01/bullkysms/api/sendsms.php";
						else
							$url = "http://www.bullkysms.com/api/sendsms.php";
						$Curl = curl_init($url);
						curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
						curl_setopt($Curl, CURLOPT_POST, 1);
						curl_setopt($Curl, CURLOPT_POSTFIELDS, $Json);
						curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($Curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
						$Response = curl_exec($Curl);			
						curl_close($Curl);
						$XML = json_decode($Response,true);
						$Response = $XML["Message"];
						if ($Response = "Sent")
						{
							echo "Done";
							die();
						}
						else
						{
							echo "Error";
							die();
						}
					}	
				}
				echo "Error";
				die();
			}
		}
		


		if ($_REQUEST['BtnPressed'] == "BtnContacts")
		{
			$OutputContacts = "";
			$_SESSION[SessionID."ClientID"] = 5;
			$Query = "SELECT * FROM (SELECT MAX(SR.receivedate) As SmsDate,".
				" SR.smstext As SmsText, SUBSTR(SR.mobile,-9) As Mobile, C.contactname, SR.optout As OptOut". 
				" FROM smsrecvd SR". 
				" INNER JOIN clientmobile CM ON SR.clientmobid = CM.clientmobid". 
				" LEFT OUTER JOIN clientcontact C ON SUBSTR(SR.mobile,-9) = SUBSTR(C.contactmobile,-9)". 
				" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]."".
				" AND (SR.receivedate BETWEEN '2021-02-02 00:00:00' AND '2021-02-23 23:59:59')".  
				" GROUP BY Mobile". 

				" UNION". 

				" SELECT MAX(SmsQ.smssent) As SmsDate, SmsQ.smstext As SmsText,". 
				" SUBSTR(SmsQ.mobile,-9) As Mobile, C.contactname, 0 As OptOut". 
				" FROM smsquelist SmsQ". 
				" INNER JOIN smsque SQ ON SmsQ.smsqueid = SQ.smsqueid". 
				" INNER JOIN clientmobile CM ON SQ.clientmobid = CM.clientmobid". 
				" LEFT OUTER JOIN clientcontact C ON SUBSTR(SmsQ.mobile,-9) = SUBSTR(C.contactmobile,-9)". 
				" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]."". 
				" AND getapp = 1  AND (SmsQ.smssent BETWEEN '2021-02-02 00:00:00' AND '2021-02-23 23:59:59')".
				" GROUP BY Mobile) As Temp WHERE 1".
				" GROUP BY Mobile".
				" ORDER BY SmsDate DESC";	
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$Index = 1;
				while ($objRow = mysqli_fetch_object($rstRow))
				{	
					$BgColor = "";
					if ($objRow->OptOut == 1)
					{
						$BgColor = "style=\"background-color: #E8D5D6;\"";
					}
					$Contact = ($objRow->contactname != null) ? $objRow->contactname : $objRow->Mobile;
					$Unseen = GetValue("COUNT(smsrevid) As Total","smsrecvd","mobile LIKE '%".$objRow->Mobile."%' AND seen = 0");
					$Last_Msg_Date = date("d-M-Y H:i",strtotime($objRow->SmsDate));
				$OutputContacts .=	
<<<EOD
				<div class="bg-color direct-chat-msg text-center pointer" id="test{$Index}" style="margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #cdcdcd;" onclick="ShowSms({$objRow->Mobile},{$Unseen},{$Index})">
					<img class="direct-chat-img" src="{$PagePath}/dist/img/avatar2.png" alt="message user image"> <!-- Contact Img -->
					<span class="direct-chat-timestamp pull-right">{$Last_Msg_Date}</span> <!-- Date And Time -->
					<p style="font-size: 1.8rem; margin-bottom: 0px !important;">{$Contact}</p> <!-- Contact Name -->
EOD;
					if ($Unseen > 0)
					{
						$btnColor = "#8D021F";
				$OutputContacts .=			
<<<EOD
					<span class="pull-right badge" style="background-color:{$btnColor}">{$Unseen}</span> <!-- Notification Batch -->
EOD;
					}
				$OutputContacts .=		
<<<EOD
					<span class="line-clamp">{$objRow->SmsText}</span> <!-- Last Message Batch-->
				</div>
EOD;	
					$Index++;
				}
				echo($OutputContacts);
				die();
			}
		}


		if ($_REQUEST["BtnPressed"] == "ShowSms")
		{
			if (isset($_REQUEST['Mobile']))
			{
				$ContactPhone = $_REQUEST['Mobile'];
				$ContactName  = GetValue("contactname","clientcontact","contactmobile LIKE '%".$ContactPhone."%'");
			}
			if (isset($_REQUEST['Unseen']) && $_REQUEST['Unseen'] > 0)
			{
				$Query  = "UPDATE smsrecvd SET seen = 1 WHERE seen = 0 AND mobile LIKE '%".$ContactPhone."%'";
				die();
				$rstRow = mysqli_query($Conn,$Query);
			}


			$Output = "";
			$Mobile = $_REQUEST['Mobile'];
			$Mobile = ($Mobile == 'ssed Call') ? "Missed Call" : $Mobile;
			$Query  = "SELECT CONCAT('SendMsg',' ',smstext) AS SmsText, smssent As SmsDate, getapp".
				" FROM smsquelist".
				" WHERE mobile LIKE '%".$Mobile."%'".
				" UNION".
				" SELECT smstext AS SmsText, receivedate AS SmsDate, 1 As getapp".
				" FROM smsrecvd".
				" WHERE mobile LIKE '%".$Mobile."%' ".
				" ORDER By SmsDate";
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				while($objRow = mysqli_fetch_object($rstRow))
				{
					$SmsDate = date("l jS F g:i A", strtotime($objRow->SmsDate));
					$SmsText = $objRow->SmsText;
					if (substr($SmsText, 0, 7) == "SendMsg")
					{
						$SmsText = str_replace("SendMsg","",$SmsText);
						if ($objRow->getapp == 0)
						{
							$FailedMessage = "<div class=\"direct-chat-info clearfix\">
								<span class=\"direct-chat-timestamp pull-right\" style=\"color:red;\">Sending Failed!</span>
							</div>";
						}
						else
						{
							$FailedMessage = "";
						}
						$Output .= 
<<<EOD
						<div class="direct-chat-msg">
							<div class="direct-chat-info clearfix">
								<span class="direct-chat-timestamp pull-right" style="color:black;">{$SmsDate}</span>
							</div>
							{$FailedMessage}
							<img class="direct-chat-img" src="{$PagePath}dist/img/Logo-Icon.png" alt="message support image">
							<div class="direct-chat-text">
								{$SmsText}
							</div>
						</div>
EOD;
					}
					elseif ($objRow->SmsDate != NULL)
					{	
						$Output .= 
<<<EOD
						<div class="direct-chat-msg right">
							<div class="direct-chat-info clearfix">
								<span class="direct-chat-timestamp pull-left" style="color:black;">{$SmsDate}</span>
							</div>
							<img class="direct-chat-img" src="{$PagePath}dist/img/avatar5.png" alt="message user image">
							<div class="direct-chat-text">
								{$SmsText}
							</div>
						</div>
EOD;
					}			
		  		}
				echo($Output);
				die();
			}	
		}	
	}
?>
