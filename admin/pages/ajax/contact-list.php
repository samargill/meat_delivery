<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "";
	if (isset($_SESSION[SessionID."ClientID"]))
	{
		// $QuerySelect = "SELECT CC.mobile, CC.fullname, MAX(SmsDate) As SmsDate, MAX(Temp.SmsText) As SmsText";
		// $QueryJoin = " FROM clientcontact CC".
		// 	" LEFT OUTER JOIN".
		// 	" (".
		// 	" (SELECT SmsL.mobile, MAX(SmsL.smsaddtime) As SmsDate, MAX(CONCAT(SmsL.smsaddtime,'-',SmsL.smstext)) As SmsText".
		// 	" FROM clientmobile CM".
		// 	" INNER JOIN smsque SmsQ ON CM.clientmobid = SmsQ.clientmobid".
		// 	" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" GROUP BY SmsL.mobile)".
		// 	" UNION".
		// 	" (SELECT SmsR.mobile, MAX(SmsR.smsdate) As SmsDate, MAX(CONCAT(SmsR.smsdate,'-',SmsR.smstext)) As SmsText".
		// 	" FROM clientmobile CM".
		// 	" INNER JOIN smsreclist SmsR ON CM.clientmobid = SmsR.clientmobid".
		// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" GROUP BY SmsR.mobile)".
		// 	" ) As Temp ON CC.mobile = Temp.mobile";
		$QuerySelect = "SELECT CC.mobile, CC.fullname, MAX(SmsDate) As SmsDate, MAX(Temp.SmsText) As SmsText";
		$QueryJoin = " FROM clientcontact CC".
			" LEFT OUTER JOIN".
			" (".
			" (SELECT SmsL.mobile, MAX(SmsL.smsaddtime) As SmsDate, MAX(CONCAT(SmsL.smsaddtime,'-',SmsL.smstext)) As SmsText".
			" FROM clientmobile CM".
			" INNER JOIN smsque SmsQ ON CM.mobileid = SmsQ.mobileid".
			" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
			" WHERE SmsQ.clientid = ".$_SESSION[SessionID."ClientID"].
			" GROUP BY SmsL.mobile)".
			" UNION".
			" (SELECT SmsR.mobile, MAX(SmsR.smsdate) As SmsDate, MAX(CONCAT(SmsR.smsdate,'-',SmsR.smstext)) As SmsText".
			" FROM clientmobile CM".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" INNER JOIN smsreclist SmsR ON CM.mobileid = SmsR.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
			" GROUP BY SmsR.mobile)".
			" ) As Temp ON CC.mobile = Temp.mobile";
		$QueryWhere = " WHERE CC.clientid = ".$_SESSION[SessionID."ClientID"]."";
		$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere."".	
			" GROUP BY CC.mobile ORDER BY SmsDate DESC, fullname ASC";
		$rstRow = mysqli_query($Conn,$Query);
		$Index = 0;
		while ($objRow = mysqli_fetch_object($rstRow))
		{
			$Index++;
			if ($objRow->SmsDate == NULL)
				$LastMsgTime = "";
			else
				$LastMsgTime = date("d-M",strtotime($objRow->SmsDate));
			$Contact = $objRow->fullname;
			$Unseen  = 0;
			$SmsText = substr($objRow->SmsText,20);
			$Response .= <<<EOD
				<div class="bg-color direct-chat-msg pointer" id="Contact{$Index}" style="margin-bottom: 10px; padding: 4px; border-bottom: 1px solid #cdcdcd;" onclick="ContactClick({$Index},{$objRow->mobile});">
					<!-- Contact Img -->
					<img class="direct-chat-img" src="{$PagePath}/dist/img/avatar2.png" alt="Chat Contact">
					<!-- Contact Name -->
					<p id="Contact{$Index}Name" style="font-size: 1.4rem; margin-bottom: 0px !important; padding-left: 44px;">{$Contact}</p>
					<!-- Date & Time -->
					<span class="direct-chat-timestamp pull-right">{$LastMsgTime}</span>
					<!-- Last Message -->
					<span class="line-clamp text-primary" style=" padding-left: 4px;">{$SmsText}</span>
				</div>
EOD;
		}
	}
	echo($Response);
?>