<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/packages.php");
	require($PagePath."../lib/PHPSpreadSheet/vendor/autoload.php");
	require($PagePath."../lib/Phone-Validator/vendor/autoload.php");
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";

	if (isset($_POST["txtStartDate"]))
	{
		if (isset($_POST['SmsQueID']))
			$SmsQueID = $_POST['SmsQueID'];
		else
			$SmsQueID = 0;
		// Start Date
		$txtStartDate = explode(" ",$_POST['txtStartDate']);
		$Date = explode("-",$txtStartDate[0]);
		$txtStartDate = "'".$Date[2]."-".$Date[1]."-".$Date[0]." ".$txtStartDate[1].":00'";
		// Close Date
		if ($_POST['cboCampaignEnd'] == 1)
		{
			$txtCloseDate = explode(" ",$_POST['txtCloseDate']);
			$Date = explode("-",$txtCloseDate[0]);
			$txtCloseDate = "'".$Date[2]."-".$Date[1]."-".$Date[0]." ".$txtCloseDate[1].":00'";
		}
		else
		{
			$txtCloseDate = "NULL";
		}
		if ($_POST["MobNumType"] == 2)
		{
			// Upload XLSX File
			if ($_FILES["txtFile"]["name"] != "")
			{
				$UploadPath = $PagePath."../upload/";
				$UploadFile = $UploadPath.$_FILES["txtFile"]["name"];
				$FileType   = strtolower(pathinfo($UploadFile,PATHINFO_EXTENSION));
				$FileName   = "Sms-".$_SESSION[SessionID."ClientID"]."-".date("YmdHis").".".$FileType;
				if ($FileType != "xlsx")
				{
					$RespHead = "Error";
					$RespText = "Uploaded File is Not XLSX";
					goto Response;
				}
				if (move_uploaded_file($_FILES["txtFile"]["tmp_name"],$UploadPath.$FileName) == false)
				{
					$RespHead = "Error";
					$RespText = "Failed To Upload File";
					goto Response;
				}
				$XlReader 	= \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
				$XlReader->setReadDataOnly(TRUE);
				$XlSheet 	= $XlReader->load($UploadPath.$FileName);
				$ActSheet 	= $XlSheet->getActiveSheet();
				$MaxRow 	= $ActSheet->getHighestRow();
				$MaxCol 	= $ActSheet->getHighestColumn();
				if ($MaxCol != "B")
				{
					$RespHead = "Error";
					$RespText = "Uploaded Excel File Does Not Have 2 Required Columns";
					unlink($UploadPath.$FileName);
					goto Response;
				}
			}
		}	
		// Check Max Campaigns
		list($RunCamp,$MaxCamp) = GetRightCampaign();
		if ($RunCamp >= $MaxCamp)
		{
			$RespHead = "Error";
			$RespText = GetValue("config_value","websettings","config_id = 304");
			$RespText = str_replace("[Run]",$RunCamp,$RespText);
			$RespText = str_replace("[Max]",$MaxCamp,$RespText);
			if ($_POST["MobNumType"] == 2)
			{
				unlink($UploadPath.$FileName);
				goto Response;
			}	
		}
		$MaxSmsChar = GetRightSmsChar();
		$Query = "SELECT SQ.smsqueid".
			" FROM smsque SQ".
			" INNER JOIN clientmobile CM ON SQ.mobileid = CM.mobileid".
			" WHERE SQ.clientid = ".$_SESSION[SessionID."ClientID"].
			" AND SQ.smsquename = '".$_REQUEST['txtCampaignName']."'";	
		if ($SmsQueID > 0)
		{
			$Query .= " AND SQ.smsqueid <> ".$SmsQueID;
		}
		$rstPro = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstPro) > 0)
		{
			$RespHead = "Error";
			$RespText = "Same Campaign Name Alrady Exist ...";
			if ($_POST["MobNumType"] == 2)
			{
				unlink($UploadPath.$FileName);
				goto Response;
			}
		}
		if ($SmsQueID == 0)
		{
			$Query = "INSERT INTO smsque".
				" (clientid, mobileid, smsquename, intervalsec, startdate, closedate, adddate, status)".
				" VALUES (".$_SESSION[SessionID."ClientID"].", ".$_POST['cboDevice'].", '".addslashes($_POST['txtCampaignName'])."',".
				" ".sprintf("%d",$_POST['txtWait']).", ".$txtStartDate.", ".$txtCloseDate.", NOW(), 1)";
			@mysqli_query($Conn,$Query);
			if (mysqli_errno($Conn) > 0)
			{
				$RespHead = "Error";
				$RespText = "Failed To Create Campaign.<br><br>".mysqli_error($Conn);
				goto Response;
			}
			else
			{
				$SmsQueID = mysqli_insert_id($Conn);
			}
		}
		else
		{
			$Query = "UPDATE smsque SET".
				"  clientid    = ".$_SESSION[SessionID."ClientID"].
				", mobileid    = ".$_POST['cboDevice'].
				", smsquename  = '".addslashes($_POST['txtCampaignName'])."'".
				", intervalsec = ".sprintf("%d",$_POST['txtWait']).
				", startdate   = ".$txtStartDate.
				", closedate   = ".$txtCloseDate.
				"  WHERE smsqueid = ".$SmsQueID;
			mysqli_query($Conn,$Query);
		}
		if ($_POST["MobNumType"] == 1)
		{
			$CountryCode = GetClientCountryCode();
			$MobUtil= \libphonenumber\PhoneNumberUtil::getInstance();
			// Explode comma separated mobile #	
			$MobileTo = $_POST["txtMobileNo"];
			$MobileTo = str_replace(' ', '', $MobileTo);
			$MobileTo = explode(",",$MobileTo);
			foreach($MobileTo as $SendTo)
			{
				$Message = $_POST["txtMessage"];
				if ($SendTo != '' && $Message != '')
				{
					if (strlen($Message) > $MaxSmsChar)
					{
						$RespHead = "Error";
						$RespText = "Failed To Import All Campaign SMS.".
							"<br>SMS # ".$row." Has Exceeded Character Length of ".$MaxSmsChar.
							"<br>Reduce The Characters in SMS and Import Campaign Again".
							"<br><br>".mysqli_error($Conn);
						goto Response;
					}
					$SendTo = preg_replace("/[^+\d]/", "", $SendTo);
					if (filter_var($SendTo,FILTER_SANITIZE_NUMBER_INT))
					{
						$Mobile = $MobUtil->parse($SendTo, $CountryCode);
						if ($MobUtil->isValidNumber($Mobile) == true)
						{
							$SendTo = $MobUtil->format($Mobile, \libphonenumber\PhoneNumberFormat::E164);
							// Check Opt-Out List
							if (($_POST['cboFilter'] == 0) 
								|| ($_POST['cboFilter'] == 1 && $MobUtil->getNumberType($Mobile) == 1) 
								|| ($_POST['cboFilter'] == 2 && $MobUtil->getNumberType($Mobile) == 0))
							{
								$Query 	= "SELECT optid FROM optout".
									" WHERE mobileid = ".$_POST['cboDevice']." AND mobile = ".$SendTo;
								$rstRow = mysqli_query($Conn,$Query);
								if (mysqli_num_rows($rstRow) == 0)
								{
									$Query = "SELECT smsid FROM smsquelist".
										" WHERE smsqueid = ".$SmsQueID." AND mobile = ".$SendTo;
									$rstRow = mysqli_query($Conn,$Query);
									if (mysqli_num_rows($rstRow) == 0)
									{
										$Query = "INSERT INTO smsquelist".
											" (smsqueid, mobile, smstext, smsaddtime)".
											" VALUES (".$SmsQueID.", '".$SendTo."', '".addslashes($Message)."', NOW())";
										mysqli_query($Conn,$Query);
									}
								}
							}
						}
					}
				}	
			}
		}
		else
		{
			if ($_FILES["txtFile"]["name"] != "")
			{
				$CountryCode = GetClientCountryCode();
				$MobUtil = \libphonenumber\PhoneNumberUtil::getInstance();
				for ($row = 1; $row <= $MaxRow; ++$row)
				{
					$SendTo  = $ActSheet->getCell('A'.$row)->getValue();
					$Message = $ActSheet->getCell('B'.$row)->getValue();
					if ($SendTo != '' && $Message != '')
					{
						if (strlen($Message) > $MaxSmsChar)
						{
							$RespHead = "Error";
							$RespText = "Failed To Import All Campaign SMS.".
								"<br>SMS # ".$row." Has Exceeded Character Length of ".$MaxSmsChar.
								"<br>Reduce The Characters in SMS and Import Campaign Again".
								"<br><br>".mysqli_error($Conn);
							goto Response;
						}
						$SendTo = preg_replace("/[^+\d]/", "", $SendTo);
						if (filter_var($SendTo,FILTER_SANITIZE_NUMBER_INT))
						{
							$Mobile = $MobUtil->parse($SendTo, $CountryCode);
							if ($MobUtil->isValidNumber($Mobile) == true)
							{
								$SendTo = $MobUtil->format($Mobile, \libphonenumber\PhoneNumberFormat::E164);
								// Check Opt-Out List
								if (($_POST['cboFilter'] == 0) 
									|| ($_POST['cboFilter'] == 1 && $MobUtil->getNumberType($Mobile) == 1) 
									|| ($_POST['cboFilter'] == 2 && $MobUtil->getNumberType($Mobile) == 0))
								{
									$Query 	= "SELECT optid FROM optout".
										" WHERE mobileid = ".$_POST['cboDevice']." AND mobile = ".$SendTo;
									$rstRow = mysqli_query($Conn,$Query);
									if (mysqli_num_rows($rstRow) == 0)
									{
										$Query = "SELECT smsid FROM smsquelist".
											" WHERE smsqueid = ".$SmsQueID." AND mobile = ".$SendTo;
										$rstRow = mysqli_query($Conn,$Query);
										if (mysqli_num_rows($rstRow) == 0)
										{
											$Query = "INSERT INTO smsquelist".
												" (smsqueid, mobile, smstext, smsaddtime)".
												" VALUES (".$SmsQueID.", '".$SendTo."', '".addslashes($Message)."', NOW())";
											mysqli_query($Conn,$Query);
										}
									}
								}
							}
						}
					}
				}
				unlink($UploadPath.$FileName);
			}
		}	
		$RespHead = "Done";
		$RespText = "SMS Campaign Created & SMS List Imported Successfully ...";
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