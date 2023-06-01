<?php
	function GetAge($DOB)
	{
		$PatAge = sprintf("%0.1f",(time() - strtotime($DOB)) / (365 * 24 * 60 * 60));
		return($PatAge);
	}

	function GetPatientDetail($PatientID)
	{
		$Patient = array();
		$Patient["ID"]         = 0;
		$Patient["Name"]       = "";
		$Patient["FirstName"]  = "";
		$Patient["LastName"]   = "";
		$Patient["Gender"]     = "";
		$Patient["GenderID"]   = 0;
		$Patient["DOB"]        = "00-00-0000";
		$Patient["Mobile"]     = "";
		$Patient["Phone"]      = "";
		$Patient["Email"]      = "";
		$Patient["AddressID"]  = 0;
		$Patient["Address"]    = "";
		$Patient["State"]      = "";
		$Patient["MedicareNo"] = "";
		$Patient["SignupType"] = "";
		$Patient["Status"]     = 0;
		$Query = "SELECT medicareno, email, phone, addressid, address, signuptype, status".
			" FROM patient WHERE patientid = ".$PatientID;
		$rstPat = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstPat) > 0)
		{
			$objPat = mysqli_fetch_object($rstPat);
			$Patient["Phone"] = $objPat->phone;
			$Patient["Email"] = $objPat->email;
			$Patient["AddressID"] = $objPat->addressid;
			list($Suburb,$PostCode,$Status) = GetAddress($objPat->addressid);
			$Patient["Address"] = $objPat->address.", ".$Suburb." ".$PostCode;
			if ($Suburb != "")
			{
				$State	= explode(",", $Suburb);
				$Patient["State"] = trim($State[count($State) - 1]);
			}
			$Patient["MedicareNo"] = $objPat->medicareno;
			$Patient["SignupType"] = $objPat->signuptype;
			$Patient["Status"]     = $objPat->status;
			// Load Family Member
			$Query = "SELECT firstname, lastname, gender, dateofbirth, mobile FROM patientfamily".
				" WHERE patientid = ".$PatientID." AND familyid = 1";
			$rstPat = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstPat) > 0)
			{
				$objPat = mysqli_fetch_object($rstPat);
				$Patient["ID"]        = $FamilyID;
				$Patient["Name"]      = $objPat->firstname." ".$objPat->lastname;
				$Patient["FirstName"] = $objPat->firstname;
				$Patient["LastName"]  = $objPat->lastname;
				$Patient["Gender"]    = ($objPat->gender == 1 ? "Male" : ($objPat->gender == 2 ? "Female" : "NULL"));
				$Patient["GenderID"]  = $objPat->gender;
				$Patient["DOB"]       = ShowDate($objPat->dateofbirth,0);
				$Patient["Mobile"]    = $objPat->mobile;
			}
		}
		return($Patient);
	}

	function GetDoctorName($DocName)
	{
		if (substr($DocName,0,4) == "Dr. ")
			$DocName = $DocName;
		elseif (substr($DocName,0,3) == "Dr ")
			$DocName = "Dr. ".trim(substr($DocName,3));
		else
			$DocName = "Dr. ".$DocName;
		return($DocName);
	}

	function GetDoctorDetail($DoctorID,$CompanyID,$PatientState,$HideRegNo=false)
	{
		$DocDetail = "";
		$Query = "SELECT AL.firstname, AL.lastname, AD.qualification, AD.providerno, AD.prescriberno, AD.registerno".
			" FROM adminlogin AL".
			" INNER JOIN admindoctor AD ON AL.adminid = AD.doctorid".
			" WHERE AL.adminid = ".$DoctorID;
		$rstDoc = mysqli_query($GLOBALS['Conn'],$Query);
		if (mysqli_num_rows($rstDoc) > 0)
		{
			$objDoc = mysqli_fetch_object($rstDoc);
			if ($CompanyID == 0)
				$DocAddress = GetStateAddress($PatientState);
			elseif ($CompanyID == 99)
				$DocAddress = GetValue("config_value","websettings","config_id = 38");
			else
				$DocAddress = GetStateAddress($CompanyID);
			$DocDetail = "".
				GetDoctorName($objDoc->firstname." ".$objDoc->lastname);
			if ($objDoc->qualification != "")
			{
			$DocDetail .= "<br>".
				"".$objDoc->qualification;
			}
			$DocDetail .= "<br>".
				"".$DocAddress;
			if ($objDoc->providerno != "" && $objDoc->providerno != "0")
			{
			$DocDetail .= "<br>".
				"Provider No : ".$objDoc->providerno;
			}
			if ($objDoc->registerno != "" && $objDoc->registerno != "0" && $HideRegNo == false)
			{
			$DocDetail .= "<br>".
				"AHPRA Registration No : ".$objDoc->registerno;
			}
		}
		return($DocDetail);
	}

	function GetPharmacyDetail($PharmacyID)
	{
		$Pharmacy = array();
		$Pharmacy["Name"]    = "";
		$Pharmacy["Address"] = "";
		$Pharmacy["Phone"]   = "";
		$Pharmacy["Fax"]     = "";
		$Pharmacy["Email"]   = "";
		$Query = "SELECT pharmacyname, phone, fax, email, addressid, address".
			" FROM pharmacy WHERE pharmacyid = ".$PharmacyID;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Pharmacy["Name"]    = $objRow->pharmacyname;
			list($Suburb,$PostCode,$Status) = GetAddress($objRow->addressid);
			$Pharmacy["Address"] = $objRow->address.", ".$Suburb." ".$PostCode;
			$Pharmacy["Phone"]   = $objRow->phone;
			$Pharmacy["Fax"]     = $objRow->fax;
			$Pharmacy["Email"]   = $objRow->email;
		}
		return($Pharmacy);
	}

	function GetStateAddress($State="NSW")
	{
		$CompanyAddress = "";
		if (ctype_digit($State) == true)
		{
			$Query = "SELECT companyaddress, addressid FROM company WHERE companyid = ".$State;
			$rstRow = mysqli_query($GLOBALS['Conn'],$Query);
			$objRow = mysqli_fetch_object($rstRow);
			list($Address,$PostCode,$Status) = GetAddress($objRow->addressid);
			$CompanyAddress = $objRow->companyaddress.", ".$Address." ".$PostCode;
		}
		if ($CompanyAddress == "")
		{
			$State = trim($State);
			if ($State == "!QLD")
				$ConfigID = "31";
			else if ($State == "!WA")
				$ConfigID = "32";
			else if ($State == "!VIC")
				$ConfigID = "33";
			else if ($State == "!TAS")
				$ConfigID = "34";
			else if ($State == "!SA")
				$ConfigID = "35";
			else if ($State == "!NT")
				$ConfigID = "36";
			else if ($State == "!ACT")
				$ConfigID = "37";
			else
				$ConfigID = "9, 10";
			$CompanyAddress = "";
			$Query = "SELECT config_id, config_value".
				" FROM websettings WHERE config_id IN (3, ".$ConfigID.") ORDER BY config_id";
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				while ($objRow = mysqli_fetch_object($rstRow))
				{
					if ($objRow->config_id == 3)
						$CompanyFax = $objRow->config_value;
					else if ($objRow->config_id == 9 || $objRow->config_id == 10)
					{
						if ($objRow->config_value != "")
						{
							if ($CompanyAddress != "") $CompanyAddress .= " ";
							$CompanyAddress .= $objRow->config_value;
						}
					}
					else
					{
						$CompanyAddress = $objRow->config_value;
					}
				}
			}
		}
		return($CompanyAddress);
	}
?>