<?php
	function SocialLogin($SignupType,$FirstName,$LastName,$Email)
	{
		if (!filter_var($Email,FILTER_VALIDATE_EMAIL))
		{
			header("Location: ./");
			exit;
		}
		else
		{
			$Flag = false;
			// Get Details From Patient Login Table 
			$Query = "SELECT PL.patientid, PF.firstname, PF.lastname, PF.mobile, PL.status".
				" FROM patientlogin PL".
				" INNER JOIN patientfamily PF ON PL.patientid = PF.patientid AND PF.familyid = 1".
				" WHERE PL.email = '".$Email."' AND PL.signuptype = ".$SignupType;
			if (mysqli_num_rows(mysqli_query($GLOBALS["Conn"],$Query)) == 0)
			{
				// Get Details From Patient Table 
				$Query = "SELECT P.patientid, PF.firstname, PF.lastname, PF.mobile, P.status".
					" FROM patient P".
					" INNER JOIN patientfamily PF ON P.patientid = PF.patientid AND PF.familyid = 1".
					" WHERE P.email = '".$Email."'";
			}

			/*$Query = "SELECT P.patientid, PF.firstname, PF.lastname, PF.mobile, P.status".
				" FROM patient P".
				" INNER JOIN patientfamily PF ON P.patientid = PF.patientid AND PF.familyid = 1".
				" WHERE P.email = '".$Email."'";*/
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				if ($objRow->mobile == "")
				{
					// Redirect The Patient Account Detail Page To Enter Mobile #
					$Flag = true;
				}
				$PatientID = $objRow->patientid;
				$FirstName = $objRow->firstname;
				$LastName  = $objRow->lastname;
			}
			else
			{
				$PatientID = GetMax("patient","patientid");
				/*$Query = "INSERT INTO patient".
					" (patientid, email, signuptype, adddate, verifydate, status, lastedit)".
					" VALUES (".$PatientID.", '".TrimText($Email,1)."', ".$SignupType.", NOW(), NOW(), 1, NOW())";*/
				$Query = "INSERT INTO patient".
					" (patientid, email, signuptype, adddate, lastedit)".
					" VALUES (".$PatientID.", '".TrimText($Email,1)."', ".$SignupType.", NOW(), NOW())";
				mysqli_query($GLOBALS["Conn"],$Query);
				$Query = "INSERT INTO patientfamily".
					" (patientid, familyid, firstname, lastname, adddate, lastedit)".
					" VALUES (".$PatientID.", 1, '".TrimText($FirstName,1)."',".
					" '".TrimText($LastName,1)."', NOW(), NOW())";
				@mysqli_query($GLOBALS["Conn"],$Query);
				$Flag = true;
			}
			if ($Flag == true)
			{
				$_SESSION["PatientID"] = $PatientID;
				header("Location: ./account-detail");
				exit;
			}
			$_SESSION[SessionID] = $PatientID;
			$_SESSION[SessionID."-FirstName"] = $FirstName;
			$_SESSION[SessionID."-LastName"]  = $LastName;
			$_SESSION[SessionID."-Time"]      = time();
			
			if (isset($_SESSION['Social-Login-Referer']))
				$Referer = $_SESSION['Social-Login-Referer'];
			else
				$Referer = "./myaccount";
			header("Location: ".$Referer);
			exit;
		}
	}
?>