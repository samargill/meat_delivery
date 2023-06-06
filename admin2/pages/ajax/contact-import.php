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

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\"}";
	if (isset($_POST["btnSave"]))
	{
		// Upload XLSX File
		if ($_FILES["txtFile"]["name"] != "")
		{
			$UploadPath = $PagePath."../upload/";
			$UploadFile = $UploadPath.$_FILES["txtFile"]["name"];
			$FileType   = strtolower(pathinfo($UploadFile,PATHINFO_EXTENSION));
			$FileName   = "Name-".$_SESSION[SessionID."ClientID"]."-".date("YmdHis").".".$FileType;
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
			$XlReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
			$XlReader->setReadDataOnly(TRUE);
			$XlSheet = $XlReader->load($UploadPath.$FileName);
			$ActSheet = $XlSheet->getActiveSheet();
			$MaxRow = $ActSheet->getHighestRow();
			$MaxCol = $ActSheet->getHighestColumn();
			if ($MaxCol != "B")
			{
				$RespHead = "Error";
				$RespText = "Uploaded Excel File Does Not Have 2 Required Columns";
				unlink($UploadPath.$FileName);
				goto Response;
			}
		}
		if ($_FILES["txtFile"]["name"] != "")
		{
			$CountryCode = GetClientCountryCode();
			$MobUtil = \libphonenumber\PhoneNumberUtil::getInstance();
			for ($row = 1; $row <= $MaxRow; ++$row)
			{
				$ContactNum  = $ActSheet->getCell('A'.$row)->getValue();
				$Name = $ActSheet->getCell('B'.$row)->getValue();
				if ($ContactNum != '' && $Name != '')
				{
					$ContactNum = preg_replace("/[^+\d]/", "", $ContactNum);
					if (filter_var($ContactNum,FILTER_SANITIZE_NUMBER_INT))
					{
						$Mobile = $MobUtil->parse($ContactNum, $CountryCode);
						if ($MobUtil->isValidNumber($Mobile) == true)
						{
							$ContactNum = $MobUtil->format($Mobile, \libphonenumber\PhoneNumberFormat::E164);
							$Query = "SELECT clientid, mobile FROM clientcontact".
								" WHERE clientid = ".$_SESSION[SessionID.'ClientID']."".
								" And mobile =".$ContactNum;
							$rstRow = mysqli_query($Conn,$Query);
							if (mysqli_num_rows($rstRow) == 0)
							{
								$Query = "INSERT INTO clientcontact".
									" (clientid, fullname, mobile, adddate, lastedit)".
									" VALUES (".$_SESSION[SessionID.'ClientID'].",".
									" '".$Name."','".$ContactNum."', NOW(), NOW())";
								mysqli_query($Conn,$Query);
							}
							else
							{
								$objRow   = mysqli_fetch_object($rstRow);
								$ClientID = $objRow->clientid;
								$SavedNum = $objRow->mobile;
								if ($ClientID == $_SESSION[SessionID."ClientID"] && $SavedNum == $ContactNum)
								{
									$Query  = "UPDATE clientcontact SET".
										" deletedate = NULL".
										" WHERE clientid = ".$_SESSION[SessionID."ClientID"]."".
										" AND mobile = ".$SavedNum;
									mysqli_query($Conn,$Query);
								}
							}
						}
					}
				}
			}
			unlink($UploadPath.$FileName);
		}
		$RespHead = "Done";
		$RespText = "Contacts Imported Successfully ...";
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