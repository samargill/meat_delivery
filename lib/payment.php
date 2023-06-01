<?php
	function GetServiceCharges($Type)
	{
		if ($Type == "Cons")
		{
			$ConfigID = 11;
		}
		elseif ($Type == "Pres")
		{
			$ConfigID = 12;
		}
		elseif ($Type == "MedCert")
		{
			$ConfigID = 13;
		}
		elseif ($Type == "MedCertLong")
		{
			$ConfigID = 60;
		}
		elseif ($Type == "CarCert")
		{
			$ConfigID = 14;
		}
		elseif ($Type == "CovCert")
		{
			$ConfigID = 64;
		}
		elseif ($Type == "NewRefe")
		{
			$ConfigID = 15;
		}
		elseif ($Type == "ChaRefe")
		{
			$ConfigID = 16;
		}
		elseif ($Type == "RepRefe")
		{
			$ConfigID = 17;
		}
		elseif ($Type == "Tele")
		{
			$ConfigID = 46;
		}
		elseif ($Type == "CovMed")
		{
			$ConfigID = 67;
		}
		elseif ($Type == "PsychoCons")
		{
			$ConfigID = 68;
		}
		else
		{
			$ConfigID = 0;
		}
		$Amount = "0.00";
		$Query = "SELECT config_value FROM websettings WHERE config_id = ".$ConfigID;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Amount = sprintf("%0.2f",$objRow->config_value);
		}
		return($Amount);
	}

	function SaveExtraCharges($Type,$CartID,$OptionalFee="0")
	{
		$ServiceFee = GetServiceCharges($Type);
		if ($Type == "Cons" || $Type == "Tele" || $Type == "CovMed")
		{
			if ($Type == "Cons")
				$ServiceID = 1;
			elseif ($Type == "CovMed")
				$ServiceID = 13;
			else
				$ServiceID = 8;
			$FeeTable  = "consultation";
			$FeeColum  = "consid";
		}
		elseif ($Type == "Pres")
		{
			$ServiceID = 2;
			$FeeTable  = "prescription";
			$FeeColum  = "presid";
		}
		elseif ($Type == "MedCert" || $Type == "MedCertLong" || $Type == "CarCert")
		{
			$ServiceID = ($Type == "MedCert" || $Type == "MedCertLong") ? 3 : 4;
			$FeeTable  = "certificate";
			$FeeColum  = "certid";
		}
		elseif ($Type == "CovCert")
		{
			$ServiceID = 12;
			$FeeTable  = "certificate";
			$FeeColum  = "certid";
		}
		elseif ($Type == "NewRefe" || $Type == "ChaRefe" || $Type == "RepRefe")
		{
			if ($Type == "NewRefe")
				$ServiceID = 5;
			elseif ($Type == "ChaRefe")
				$ServiceID = 6;
			else
				$ServiceID = 7;
			$FeeTable = "referral";
			$FeeColum = "refeid";
		}
		elseif ($Type == "PsychoCons")
		{
			$ServiceID = 15;
			$FeeTable  = "telepsychology";
			$FeeColum  = "psycid";
		}
		$Amount = 0.00;
		if ($FeeTable != "" && $FeeColum != "")
		{
			$Query = "DELETE FROM ".$FeeTable."cartfee WHERE ".$FeeColum." = ".$CartID;
			mysqli_query($GLOBALS["Conn"],$Query);
			$Query = "SELECT feeid, amount, sorting FROM servicefees".
				" WHERE serviceid = ".$ServiceID." AND status = 1".
				" AND ((servetype = 1) OR (servetype = 2 AND feeid IN (".$OptionalFee.") AND amount > 0))".
				" ORDER BY sorting";
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			while ($objRow = mysqli_fetch_object($rstRow))
			{
				if ($objRow->sorting == 0)
					$FeeAmount = $ServiceFee;
				else
					$FeeAmount = $objRow->amount;
				$Query = "INSERT INTO ".$FeeTable."cartfee".
					" (".$FeeColum.", feeid, amount)".
					" VALUES (".$CartID.", ".$objRow->feeid.", ".$FeeAmount.")";
				mysqli_query($GLOBALS["Conn"],$Query);
				$Amount += $FeeAmount;
			}
		}
		return($Amount);
	}

	function StartWallet()
	{
		$PayWallet = $PaySubscr = 0.00;
		if (isset($_REQUEST['TotalPayable']))
			$TotalPayable = $_REQUEST['TotalPayable'];
		else
			$TotalPayable = 0.00;
		if (isset($_REQUEST['ChkPayWallet']))
		{
			if ($_REQUEST['txtPayWallet'] >= $TotalPayable)
				$PayWallet = $TotalPayable;
			else
				$PayWallet = $_REQUEST['txtPayWallet'];
		}
		$TotalPayable = $TotalPayable - $PayWallet;
		if (isset($_REQUEST['ChkPaySubscr']))
		{
			if ($_REQUEST['txtPaySubscrAmt'] >= $TotalPayable)
				$PaySubscr = $TotalPayable;
			else
				$PaySubscr = $_REQUEST['txtPaySubscrAmt'];
		}
		return(array($PayWallet,$PaySubscr));
		
		/*$PayWallet = 0.00;
		if (isset($_REQUEST['ChkPayWallet']))
		{
			if ($_REQUEST['txtPayWallet'] >= $_REQUEST['TotalPayable'])
				$PayWallet = $_REQUEST['TotalPayable'];
			else
				$PayWallet = $_REQUEST['txtPayWallet'];
		}
		return($PayWallet);*/
	}

	function DebitWallet($ActBookID,$PatientID,$Refund)
	{
		$Query = "UPDATE patient".
			" SET walletamount = walletamount + ".$Refund." WHERE patientid = ".$PatientID;
		mysqli_query($GLOBALS['Conn'],$Query);
		$Query = "INSERT INTO patient_ledger".
			" (transdate, transtype, paytype, patientid, bookid, amount)".
			" VALUES (NOW(), 1, 1, ".$PatientID.", '".$ActBookID."', ".$Refund.")";
		mysqli_query($GLOBALS['Conn'],$Query);
	}

	function CreditWallet($ActBookID,$PatientID,$PayWallet,$PaySubscr)
	{
		if ($PayWallet > 0)
		{
			$Query = "UPDATE patient".
				" SET walletamount = walletamount - ".$PayWallet." WHERE patientid = ".$PatientID;
			mysqli_query($GLOBALS['Conn'],$Query);
			$Query = "INSERT INTO patient_ledger".
				" (transdate, transtype, paytype, patientid, bookid, amount)".
				" VALUES (NOW(), -1, 2, ".$PatientID.", '".$ActBookID."', ".$PayWallet.")";
			mysqli_query($GLOBALS['Conn'],$Query);
		}
		if ($PaySubscr > 0)
		{
			$Query = "UPDATE patient_subscribed".
				" SET remaining = remaining - ".$PaySubscr." WHERE patientid = ".$PatientID;
			mysqli_query($GLOBALS['Conn'],$Query);
			$Query = "INSERT INTO patient_ledger".
				" (transdate, transtype, paytype, patientid, bookid, amount)".
				" VALUES (NOW(), -1, 3, ".$PatientID.", '".$ActBookID."', ".$PaySubscr.")";
			mysqli_query($GLOBALS['Conn'],$Query);
		}
	}

	function SavePayment($CartID,$PayRef,$CardNo,$CardExpiry,$CardCVC,$Amount)
	{
		$CardExpiry = str_replace(" ","",$CardExpiry);
		$CardExpiry = explode("/",$CardExpiry);
		$CardExpiry = $CardExpiry[1].$CardExpiry[0];
		if (DBUserName == "myitvh" && true)
		{
			if ($CardNo == "4005 5500 0000 0001" && $CardExpiry == "2505" && $CardCVC == "123")
			{
				$Response = "{".
					"\"PayTransID\": \"".date("YmdHis")."\", ".
					"\"PayAuthID\": \"".date("is")."\", ".
					"\"PayAmount\": \"".sprintf("%0.2f",$Amount)."\", ".
					"\"PayStatus\": \"1\"".
					"}";
			}
			else
			{
				$Response = "{".
					" \"PayError\": \"Invalid Card Details\",".
					" \"PayStatus\": \"0\"".
					" }";
			}
		}
		else
		{
			$PostData = "CartID=".$CartID."&PayRef=".$PayRef."&CardNo=".str_replace(" ","",$CardNo).
				"&CardExpiry=".$CardExpiry."&CardCVC=".$CardCVC."&Amount=".sprintf("%0.2f",$Amount);
			$PayUrl = WebsiteUrl."/payment-process";
			$Curl = curl_init();
			curl_setopt($Curl, CURLOPT_URL,$PayUrl);
			curl_setopt($Curl, CURLOPT_TIMEOUT, 0);
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($Curl, CURLOPT_POST, 1);
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $PostData);
			$Response = curl_exec($Curl);
			if (curl_errno($Curl) > 0)
			{
				$Error = curl_error($Curl);
				curl_close($Curl);
				echo("Curl Error = ".$Error);
				die;
			}
			else
			{
				curl_close($Curl);
			}
		}
		return($Response);
	}

	function SaveRefund($PayRef,$PayTransID,$Amount)
	{
		if (DBUserName == "root" && true)
		{
			$Response = "{".
				"\"PayTransID\": \"".date("YmdHis")."\", ".
				"\"PayAuthID\": \"".date("is")."\", ".
				"\"PayAmount\": \"".sprintf("%0.2f",$Amount)."\", ".
				"\"PayStatus\": \"1\"".
				"}";
		}
		else
		{
			$PostData = "PayRef=".$PayRef."&PayTransID=".$PayTransID."&Amount=".sprintf("%0.2f",$Amount);
			$PayUrl = WebsiteUrl."/payment-refund";
			$Curl = curl_init();
			curl_setopt($Curl, CURLOPT_URL,$PayUrl);
			curl_setopt($Curl, CURLOPT_TIMEOUT, 0);
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($Curl, CURLOPT_POST, 1);
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $PostData);
			$Response = curl_exec($Curl);
			if (curl_errno($Curl) > 0)
			{
				$Error = curl_error($Curl);
				curl_close($Curl);
				echo("Error = ".$Error);
				die;
			}
			else
			{
				curl_close($Curl);
			}
		}
		return($Response);
	}

	function GetPayStatusID($PayStatus)
	{
		$PayID = 2;
		$PayStatus = str_replace("+"," ",$PayStatus);
		$Query = "SELECT paystatusid FROM payment_status WHERE paystatusname = '".addslashes($PayStatus)."'";
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$PayID  = $objRow->paystatusid;
		}
		return($PayID);
	}
?>