<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/packages.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['DeviceID']))
	{
		if ($_POST['DeviceID'] == 0)
			$AddNew = true;
		else
			$AddNew = false;
		// $Query = "SELECT clientmobid FROM clientmobile".
		// 	" WHERE clientid = ".$_SESSION[SessionID."ClientID"].
		// 	" AND mobileno = '".addslashes($_POST['txtMobileNo'])."'";
		$Query = "SELECT CM.mobileid".
			" FROM clientmobile CM".
			" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
			" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
			" AND CM.mobileno = '".addslashes($_POST['txtMobileNo'])."'";
		if ($AddNew == false)
		{
			$Query .= " AND CM.mobileid <> ".$_POST['DeviceID'];
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$RespHead = "Error";
			$RespText = "Same Mobile # Already Added in Devices";
			goto Response;
		}
		$GetPkg = GetValue("pkgtype","client","clientid = ".$_SESSION[SessionID."ClientID"]);	
		// Show Max Slots input Field When Super Admin Session Is Set 
		if ($_SESSION[SessionID."ClientID"] == 1)
		{
			$MaximumSlots = $_POST['txtMaxSlot'];
		}
		elseif ($_SESSION[SessionID."ClientID"] != 1 && $GetPkg != 6 && $GetPkg != 7 && $GetPkg != 8 && $GetPkg != 9)
		{
			$MaximumSlots = 0;
		}	
		$MobileNo = TrimText(str_replace("+","",$_POST['MobileFull']),1);
		if ($AddNew == true)
		{
			if ($_SESSION[SessionID."ClientID"] != 1) 
			{
				// Check Max Devices
				list($RunDevice,$MaxDevice) = GetRightDevice();
				if ($RunDevice >= $MaxDevice)
				{
					$RespHead = "Error";
					$RespText = GetValue("config_value","websettings","config_id = 303");
					$RespText = str_replace("[Run]",$RunDevice,$RespText);
					$RespText = str_replace("[Max]",$MaxDevice,$RespText);
					goto Response;
				}
			}
			// $Query = "INSERT INTO clientmobile".
			// 	" (clientid, mobileno, mobilecode, mobilename, adddate, lastedit)".
			// 	" VALUES (".$_SESSION[SessionID."ClientID"].", '".$MobileNo."',".
			// 	" '".TrimText($_POST['txtMobileCode'],1)."', '".TrimText($_POST['txtMobileName'],1)."', NOW(), NOW())";
			// mysqli_query($Conn,$Query);
			// $ClientMobID = mysqli_insert_id($Conn);
			// $Query = "INSERT INTO smsque".
			// 	" (clientmobid, smsquename, adddate, status)".
			// 	" VALUES (".$ClientMobID.", 'SMS API', NOW(), 0)";
			// mysqli_query($Conn,$Query);
			$Query = "INSERT INTO clientmobile".
				" (mobileno, mobilecode, mobilename, maxslot, adddate, lastedit)".
				" VALUES ('".$MobileNo."', '".TrimText($_POST['txtMobileCode'],1)."', '".TrimText($_POST['txtMobileName'],1)."', ".$MaximumSlots.", NOW(), NOW())";
			mysqli_query($Conn,$Query);
			$ClientMobID = mysqli_insert_id($Conn);
			$Query = "INSERT INTO clienthavemob".
				" (clientid, mobileid) VALUES (".$_SESSION[SessionID."ClientID"].", ".$ClientMobID.")";
			mysqli_query($Conn,$Query);
			$Query = "INSERT INTO smsque".
				" (clientid, mobileid, smsquename, startdate, adddate, status)".
				" VALUES (".$_SESSION[SessionID."ClientID"].", ".$ClientMobID.", 'SMS API', NOW(), NOW(), 0)";
			mysqli_query($Conn,$Query);
		}
		else
		{
			// $Query = "UPDATE clientmobile SET".
			// 	"  mobileno   = '".$MobileNo."'".
			// 	", mobilecode = '".TrimText($_REQUEST['txtMobileCode'],1)."'".
			// 	", mobilename = '".TrimText($_REQUEST["txtMobileName"],1)."'".
			// 	", lastedit   =  NOW()".
			// 	"  WHERE clientid = ".$_SESSION[SessionID."ClientID"]." AND clientmobid = ".$_POST['DeviceID'];
			$Query = "UPDATE clientmobile CM".
				" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid SET".
				" CM.mobileno   = '".$MobileNo."',".
				" CM.mobilecode = '".TrimText($_REQUEST['txtMobileCode'],1)."',".
				" CM.mobilename = '".TrimText($_REQUEST["txtMobileName"],1)."',".
				" CM.maxslot 	= ".$MaximumSlots.",".
				" CM.lastedit   =  NOW()".
				" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]." AND CM.mobileid = ".$_POST['DeviceID'];
			mysqli_query($Conn,$Query);
		}
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			if ($AddNew == true)
				$RespText = "Unable To Add New Device";
			else
				$RespText = "Unable To Save Device";
			$RespText .= "<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			$RespHead = "Done";
			if ($AddNew == true)
				$RespText = "New Device Added Successfully.";
			else
				$RespText = "Device Detail Saved Successfully.";
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