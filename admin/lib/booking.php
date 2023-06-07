<?php

	function SaveBookingLog($BookID,$ActType,$DoctorID = 0)
	{
		$SingleLog = false;
		if ($ActType == "Open")
			$ActID = 1;
		elseif ($ActType == "Start")
			$ActID = 2;
		elseif ($ActType == "Done")
			$ActID = 3;
		elseif ($ActType == "Reject")
			$ActID = 4;
		elseif ($ActType == "DeSelect")
			$ActID = 5;
		elseif ($ActType == "EditTime")
			$ActID = 6;
		elseif ($ActType == "Edit")
			$ActID = 7;
		elseif ($ActType == "SendFax")
			$ActID = 8;
		elseif ($ActType == "SendPost")
			$ActID = 9;
		elseif ($ActType == "SendGoogleReview")
			$ActID = 10;
		elseif ($ActType == "Refund")
			$ActID = 11;
		elseif ($ActType == "SendAvailabilitySms")
			$ActID = 12;
		elseif ($ActType == "VideoCall")
			$ActID = 13;
		elseif ($ActType == "PrescriptionFaxConfirmed")
		{
			$ActID = 14;
			$SingleLog = true;
		}
		elseif ($ActType == "PrescriptionEmailConfirmed")
		{
			$ActID = 15;
			$SingleLog = true;
		}
		elseif ($ActType == "PharmacyPostedEmail")
		{
			$ActID = 16;
			$SingleLog = true;
		}
		elseif ($ActType == "PrescriptionScanUpload")
		{
			$ActID = 17;
			$SingleLog = true;
		}
		elseif ($ActType == "Create")
		{
			$ActID = 18;
			$SingleLog = true;
		}
		elseif ($ActType == "RefundMedicare")
			$ActID = 19;
		elseif ($ActType == "EditMedItemNo")
			$ActID = 20;
		elseif ($ActType == "RefundToWallet")
			$ActID = 21;
		elseif ($ActType == "SendProductReview")
			$ActID = 23;
		elseif ($ActType == "Create-Prescription-Issue")
			$ActID = 24;
		elseif ($ActType == "Ask-Doc-To-Send-Pres")
			$ActID = 25;
		elseif ($ActType == "Call-Pharmacy-To-Confirm-Pres")
			$ActID = 26;
		elseif ($ActType == "Resolve-Prescription-Issue")
			$ActID = 27;
		elseif ($ActType == "SendPaymentSms")
			$ActID = 30;
		elseif ($ActType == "SendCertificatePDF")
			$ActID = 51;
		elseif ($ActType == "SendClaimBill")
			$ActID = 52;

		
		/*
		elseif ($ActType == "Create")
		{
			$ActID = 17;
			$SingleLog = true;
		}
		elseif ($ActType == "SendMedicareSms")
			$ActID = 18;
		elseif ($ActType == "PrescriptionPostingEmail")
			$ActID = 19;
		elseif ($ActType == "SendLabReportSms")
			$ActID = 20;
		elseif ($ActType == "Create-Prescription-Issue")
			$ActID = 22;
		elseif ($ActType == "Ask-Doc-To-Send-Pres")
			$ActID = 23;
		elseif ($ActType == "Call-Pharmacy-To-Confirm-Pres")
			$ActID = 24;
		elseif ($ActType == "Resolve-Prescription-Issue")
			$ActID = 25;
		elseif ($ActType == "BookingAssignedToDoc")
			$ActID = 27;*/
		/*elseif ($ActType == "BookTimeSmsToDoc")
			$ActID = 23;
		elseif ($ActType == "BookTimeSmsToPat")
			$ActID = 24;
		elseif ($ActType == "ConsultationReferralUpload")
			$ActID = 25;
		elseif ($ActType == "EditMode")
			$ActID = 26;
		elseif ($ActType == "EditReferral")
		{
			$ActID = 31;
			$SingleLog = true;
		}
		elseif ($ActType == "EditPrescription")
		{
			$ActID = 32;
			$SingleLog = true;
		}
		elseif ($ActType == "LabReportViewed")
		{
			$ActID = 33;
			$SingleLog = true;
		}
		elseif ($ActType == "EditMedItemNo")
			$ActID = 34;
		elseif ($ActType == "BookingCallStatus")
			$ActID = 35;*/
		$Add = true;
		if ($SingleLog)
		{
			$Query = "SELECT logid, markdeleted FROM bookingactlog WHERE bookid = '".$BookID."' AND actid = ".$ActID;
			$rstRow = mysqli_query($GLOBALS['Conn'],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				if ($objRow->markdeleted == 0)
					$Add = false;
				/*$Query = "UPDATE bookingactlog SET markdeleted = 0 WHERE logid = ".$objRow->logid;
				mysqli_query($GLOBALS['Conn'],$Query);
				$Add = false;*/
			}
		}
		if ($Add)
		{
			$Query = "INSERT INTO bookingactlog".
				" (bookid, actid, logtime, adminid, doctorid)".
				" VALUES ('".$BookID."', ".$ActID.", NOW(), ".$_SESSION[SessionID].",".$DoctorID.")";
			mysqli_query($GLOBALS['Conn'],$Query);
		}
	}
?>