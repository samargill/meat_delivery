<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Output = "";
	if (isset($_POST['Mobile']))
	{
		if (isset($_POST['ChatTime']))
			$ChatTime = $_POST['ChatTime'];
		else
			$ChatTime = "2000-01-01 00:00:00";
		$Mobile = trim($_POST['Mobile']);
		// $Query  = "(SELECT SmsL.smsid As SmsID, 'Out' As SmsType, SmsL.smstext As SmsText,".
		// 	" SmsL.smsaddtime As SmsDate, SmsL.smssent As SmsSentTime".
		// 	" FROM smsque SmsQ".
		// 	" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
		// 	" INNER JOIN clientmobile CM ON SmsQ.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" AND CM.clientmobid = ".$_POST['ClientMobID']." AND mobile = ".$Mobile.
		// 	" AND SmsL.smsaddtime > '".$ChatTime."')".
		// 	" UNION".
		// 	" (SELECT SmsR.smsid As SmsID, 'In' As SmsType, SmsR.smstext As SmsText,".
		// 	" SmsR.smsdate As SmsDate, SmsR.smsdate As SmsSentTime".
		// 	" FROM smsreclist SmsR".
		// 	" INNER JOIN clientmobile CM ON SmsR.clientmobid = CM.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" AND CM.clientmobid = ".$_POST['ClientMobID']." AND mobile = ".$Mobile.
		// 	" AND SmsR.smsdate > '".$ChatTime."')".
		// 	" ORDER By SmsDate";


		$Query  = "(SELECT SmsL.smsid As SmsID, 'Out' As SmsType, SmsL.smstext As SmsText,".
			" SmsL.smsaddtime As SmsDate, SmsL.smssent As SmsSentTime".
			" FROM smsque SmsQ".
			" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
			" INNER JOIN clientmobile CM ON SmsQ.mobileid = CM.mobileid".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]."".
			" AND CM.mobileid = ".$_POST['ClientMobID']." AND SmsL.mobile = ".$Mobile."".
			" AND SmsL.smsaddtime > '".$ChatTime."')".
			" UNION".
			" (SELECT SmsR.smsid As SmsID, 'In' As SmsType, SmsR.smstext As SmsText,".
			" SmsR.smsdate As SmsDate, SmsR.smsdate As SmsSentTime".
			" FROM smsreclist SmsR".
			" INNER JOIN clientmobile CM ON SmsR.mobileid = CM.mobileid".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]."".
			" AND CM.mobileid = ".$_POST['ClientMobID']." AND SmsR.mobile = ".$Mobile."".
			" AND SmsR.smsdate > '".$ChatTime."')".
			" ORDER By SmsDate";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			while($objRow = mysqli_fetch_object($rstRow))
			{
				$SmsText = str_replace("\n","<br>",$objRow->SmsText);
				if ($objRow->SmsSentTime == null)
					$SmsDate = date("D d-M-Y g:i A", strtotime($objRow->SmsDate));
				else
					$SmsDate = date("D d-M-Y g:i A", strtotime($objRow->SmsSentTime));
				if ($objRow->SmsType == "Out")
				{
					if ($objRow->SmsSentTime == null)
					{
						$FailedMessage = "".
						"<div class=\"direct-chat-info clearfix\">".
							"<span class=\"direct-chat-timestamp pull-right\" style=\"color:red;\">Sending Failed!</span>".
						"</div>";
					}
					else
					{
						$FailedMessage = "";
					}
					$Output .= 
<<<EOD
					<div class="direct-chat-msg">
						<img class="direct-chat-img" src="{$PagePath}dist/img/Logo-Icon.png" alt="message support image">
						<div class="direct-chat-text">
							{$SmsText}
						</div>
						<div class="direct-chat-info clearfix">
							<span class="direct-chat-timestamp pull-right" style="color:black;">{$SmsDate}</span>
						</div>
						{$FailedMessage}
					</div>
EOD;
				}
				else
				{	
					$Output .= 
<<<EOD
					<div class="direct-chat-msg right">
						<img class="direct-chat-img" src="{$PagePath}dist/img/avatar5.png" alt="message user image">
						<div class="direct-chat-text">
							{$objRow->SmsText}
						</div>
						<div class="direct-chat-info clearfix">
							<span class="direct-chat-timestamp pull-left" style="color:black;">{$SmsDate}</span>
						</div>
					</div>
EOD;
				}			
			}
			echo($Output);
		}
	}
?>