<?php
	LoadSettings();
	function SetCompany($Text)
	{
		return(str_replace("[CompanyName]",constant("WebsiteTitle"),$Text));
	}

	function Debug($Param)
	{
		echo("<pre>");
		print_r($Param);
		echo("</pre>");
	}

	function CsrfToken()
	{
		return bin2hex(random_bytes(32));
	}

	
	function GetPresID()
	{
		$PresID = 0;
		if (isset($_SESSION[SessionID]) && isset($_SESSION[SessionID."-SpeID"]) && isset($_SESSION[SessionID."-FamilyID"]))
		{
			$Query = "SELECT presid, ROUND((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(presdate)) / 86400,2) As DaysOld".
				" FROM prescriptioncart".
				" WHERE patientid = ".$_SESSION[SessionID].
				" AND familyid = ".$_SESSION[SessionID."-FamilyID"]." AND speid = ".$_SESSION[SessionID."-SpeID"];
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				if ($objRow->DaysOld > 2.00)
				{
					$Query = "DELETE FROM prescriptioncart       WHERE presid = ".$objRow->presid;
					mysqli_query($GLOBALS["Conn"],$Query);
					$Query = "DELETE FROM prescriptioncartdetail WHERE presid = ".$objRow->presid;
					mysqli_query($GLOBALS["Conn"],$Query);
					$Query = "DELETE FROM prescriptioncartfee    WHERE presid = ".$objRow->presid;
					mysqli_query($GLOBALS["Conn"],$Query);
					$PresID = 0;
				}
				else
				{
					$PresID = $objRow->presid;
				}
			}
		}
		return($PresID);
	}

	function GetUUID()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	function SetAppView($Type)
	{
		if (defined("AppView") == true)
		{
			if ($Type == "Body")
			{
				echo("style=\"padding-top: 3rem;\"");
			}
			elseif ($Type == "TC")
			{
				echo("style=\"padding-top: 0rem;\"");
			}
		}
	}

	function GetValue($Field,$Table,$Condition)
	{/* Written By : Mohammad Kaiser Anwar */
		$FieldValue = "";
		$Query = "SELECT ".$Field." FROM ".$Table." WHERE ".$Condition;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (substr($Field,0,1) == "@")
		{
			echo $Query;
			echo mysqli_error();
			die;
		}
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_array($rstRow);
			$FieldValue = $objRow[0];
		}
		if (mysqli_errno($GLOBALS["Conn"]) > 0)
		{
			error_log("PHP   4. ".pathinfo(debug_backtrace()[0]['file'])['basename']);
			error_log("PHP   5. ".$Query);
		}
		return($FieldValue);
	}

	function LoadSettings()
	{
		if ($GLOBALS["Conn"])
		{
			$Query = "SELECT config_id, config_name, config_value".
				" FROM websettings".
				" WHERE (config_id BETWEEN 1 AND 10) OR (config_id IN (49,102)) OR (config_id BETWEEN 201 AND 205)".
				" ORDER BY config_id";
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			while ($objRow = mysqli_fetch_object($rstRow))
			{
				switch ($objRow->config_id)
				{
					case 1:
						define("WebsiteTitle",$objRow->config_value);
						break;
					case 2:
						define("WebsitePhone",$objRow->config_value);
						break;
					case 3:
						define("WebsiteFax",$objRow->config_value);
						break;
					case 4:
						define("AdminEmail",$objRow->config_value);
						break;
					case 5:
						define("RegiEmail",$objRow->config_value);
						break;
					case 6:
						define("BookEmail",$objRow->config_value);
						break;
					case 7:
						define("FromEmail",$objRow->config_value);
						break;
					case 8:
						define("Website",$objRow->config_value);
						break;
					case 9:
						define("WebsiteAddress1",$objRow->config_value);
						break;
					case 10:
						define("WebsiteAddress2",$objRow->config_value);
						break;
					case 49:
						define("BulkBillFlag",$objRow->config_value);
						break;
					case 102:
						define("CopyrightYear",$objRow->config_value);
						break;
					case 201:
						define("FacebookID",$objRow->config_value);
						break;
					case 202:
						define("TwitterID",$objRow->config_value);
						break;
					case 203:
						define("GoogleID",$objRow->config_value);
						break;
					case 204:
						define("LinkedInID",$objRow->config_value);
						break;
					case 205:
						define("InstaID",$objRow->config_value);
						break;
					default:
						break;
				}
			}
		}
	}

	function GetPatientDetail($PatientID,$FamilyID)
	{
		$Patient = array();
		$Patient["ID"]        = 0;
		$Patient["Name"]      = "";
		$Patient["FirstName"] = "";
		$Patient["Gender"]    = "";
		$Patient["GenderID"]  = 0;
		$Patient["DOB"]       = "00-00-0000";
		$Patient["DOBStamp"]  = 0;
		$Patient["Mobile"]    = "";
		$Patient["Phone"]     = "";
		$Patient["Email"]     = "";
		$Patient["Address"]   = "";
		$Query = "SELECT email, phone, addressid, address".
			" FROM patient WHERE patientid = ".$PatientID;
		$rstPat = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstPat) > 0)
		{
			$objPat = mysqli_fetch_object($rstPat);
			$Patient["Phone"] = $objPat->phone;
			$Patient["Email"] = $objPat->email;
			list($Suburb,$PostCode,$Status) = GetAddress($objPat->addressid);
			$Patient["Address"] = $objPat->address.", ".$Suburb." ".$PostCode;
		}
		$Query = "SELECT firstname, lastname, gender,".
			" UNIX_TIMESTAMP(dateofbirth) As DOBStamp, dateofbirth, mobile".
			" FROM patientfamily".
			" WHERE patientid = ".$PatientID." AND familyid = ".$FamilyID;
		$rstPat = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstPat) > 0)
		{
			$objPat = mysqli_fetch_object($rstPat);
			$Patient["ID"]        = $FamilyID;
			$Patient["Name"]      = $objPat->firstname." ".$objPat->lastname;
			$Patient["FirstName"] = $objPat->firstname;
			$Patient["Gender"]    = ($objPat->gender == 1 ? "Male" : "Female");
			$Patient["GenderID"]  = $objPat->gender;
			$Patient["DOB"]       = ShowDate($objPat->dateofbirth,0);
			$Patient["DOBStamp"]  = $objPat->DOBStamp;
			$Patient["Mobile"]    = $objPat->mobile;
		}
		return($Patient);
	}

	function GetPharmacyDetail($PharmacyID)
	{
		$Pharmacy = array();
		$Pharmacy["Name"]    = "";
		$Pharmacy["Address"] = "";
		$Pharmacy["Phone"]   = "";
		$Pharmacy["Fax"]     = "";
		$Query = "SELECT pharmacyname, phone, fax, addressid, address".
			" FROM pharmacy WHERE pharmacyid = ".$PharmacyID;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Pharmacy["Name"]    = $objRow->pharmacyname;
			list($Suburb,$PostCode,$Status) = GetAddress($objRow->addressid);
			$Pharmacy["Address"] = $objRow->address.", ".$Suburb." ".$PostCode;
			$Pharmacy["Phone"]   = $objRow->phone;
			$Pharmacy["Fax"]     = $objRow->phone;
		}
		return($Pharmacy);
	}

	function GetAddress($AddressID)
	{
		$Suburb = $PostCode = "";
		$Status = 0;
		$Query = "SELECT suburb, postcode, state, status".
			" FROM addresses WHERE addressid = ".$AddressID;
		$rstRow = mysqli_query($GLOBALS['Conn'],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Suburb   = $objRow->suburb.", ".$objRow->state;
			$PostCode = $objRow->postcode;
			$Status   = $objRow->status;
		}
		return(array($Suburb,$PostCode,$Status));
	}

	function GetStatus($Status)
	{
		if ($Status == 0)
			$PayStatus = "In Queue";
		else if ($Status == 1)
			$PayStatus = "Accepted";
		else if ($Status > 2)
			$PayStatus = "Refused";
		else if ($Status == 3)
			$PayStatus = "Shipped";
		else
			$PayStatus = "None";
		return($PayStatus);
	}

	function GetPayStatus($Status)
	{
		if ($Status == 0)
			$PayStatus = "In Progress";
		else if ($Status == 1)
			$PayStatus = "Confirmed";
		else if ($Status == 2)
			$PayStatus = "Declined";
		else if ($Status == 3)
			$PayStatus = "Refunded";
		return($PayStatus);
	}

	function GetTiming($WeekDay)
	{
		$Query = "SELECT starttime FROM sitetiming";
		if ($WeekDay == 1)
		{
			$Query .= " WHERE weekday = 1";
		}
		elseif ($WeekDay == 2)
		{
			$Query .= " WHERE weekday = 6";
		}
		elseif ($WeekDay == 3)
		{
			$Query .= " WHERE weekday = 0";
		}
		$rstRow = mysqli_query($Conn,$Query);
		$objRow = mysqli_fetch_object($rstRow);
	}

	function EncrypeEmail($Email)
	{
		$Email = explode("@",$Email);
		if (strlen($Email[0]) <= 4)
		{
			$EmailStart = substr($Email[0],0,1);
		}
		else
		{
			$EmailStart = substr($Email[0],0,2);
		}
		$Email = $EmailStart.str_pad("",strlen($Email[0])-strlen($EmailStart)-1,"*").substr($Email[0],-1)."@".$Email[1];
		return($Email);
	}

	function GetMax($Table,$Field,$Where="1",$DBName="")
	{
		if ($DBName == "")
			$DBName = DBName;
		$Query = "SELECT MAX(".$Field.") + 1 As MaxID FROM ".$Table." WHERE ".$Where;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			if ($objRow->MaxID == NULL)
				$MaxID = 1;
			else
				$MaxID = $objRow->MaxID;
		}
		return($MaxID);
	}

	function UCString($String)
	{/* Written By : Mohammad Kaiser Anwar */
		try
		{
			$Value  = "";
			$String = ucwords(strtolower(stripslashes($String)));
			for ($i = 0; $i < strlen($String); $i++)
			{
				if ($String[$i] == "(" || $String[$i] == "-" || $String[$i] == "." || $String[$i] == "/" || $String[$i] == "&")
				{
					$Value = $Value . $String[$i] . strtoupper($String[$i+1]);
					$i++;
				}
				else
					$Value = $Value . $String[$i];
			}
		}
		catch(Exception $e)
		{
			$Value = $String;
			$Error = error_get_last();
			error_log("[".date("d-M-Y H:i:s")." ".date_default_timezone_get()."] PHP Notice:  ".$Error['message']." in ".$Error['file']." on line ".$Error['line']);
			error_log("PHP   1. ".pathinfo(debug_backtrace()[0]['file'])['basename']);
			error_log("PHP   2. ".$String);
		}
		return($Value);
	}

	function TrimText($Text,$Slashes)
	{
		$Text = str_replace("\r"," ",trim($Text));
		while (strpos($Text,"  ") > 0)
		{
			$Text = str_replace("  "," ",$Text);
		}
		if ($Slashes == 0)
			return(stripslashes($Text));
		else
			return(addslashes($Text));
	}

	function ShowFloat($Value)
	{
		if (is_numeric($Value) == false)
			return($Value);
		else
		{
			$Str = $Value;
			if ($Str == "0")
				$Str = "0.00";
			else if (!strpos($Str,"."))
				$Str = $Str . ".00";
			else
			{
				$Value = split("[.]",$Str);
				if (strlen($Value[0]) <= 0) $Value[0] = "0";
				if (strlen($Value[1]) == 0)
					$Str = $Value[0] . ".00";
				else if (strlen($Value[1]) == 1)
					$Str = $Value[0] . "." . $Value[1] . "0";
				else if (strlen($Value[1]) == 2)
					$Str = $Value[0] . "." . $Value[1];
				else if (strlen($Value[1]) > 2)
				{
					$Point = 0;
					if (intval($Value[1][2]) >= 5) $Point = 1;
					$Point = intval($Value[1][0]) * 10 + intval($Value[1][1]) + $Point;
					if (strlen($Point) > 2)
						$Str = (intval($Value[0]) + 1) . ".00";
					else if (strlen($Point) == 1)
						$Str = $Value[0] . ".0" . $Point;
					else
						$Str = $Value[0] . "." . $Point;
				}
			}
			return($Str);
		}
	}

	function ShowDate($Date,$Time)
	{/* Written By : Mohammad Kaiser Anwar */
		if ($Date == "0000-00-00" && $Time == 0)
		{
			return("00-00-0000");
		}
		else if ($Date == "0000-00-00 00:00:00" && $Time == 1)
		{
			return("00-00-0000 00:00:00");
		}
		else if ($Time == 0)
		{
			$SplitDateTime = explode(" ",$Date);
			$SplitDate = explode("-",$SplitDateTime[0]);
			if (isset($SplitDate[2]) && isset($SplitDate[1]) && isset($SplitDate[0]))
				return(date("d-M-Y",mktime(0,0,0,$SplitDate[1],$SplitDate[2],$SplitDate[0])));
			else
				return("-----");
		}
		else if ($Time == 1 || $Time == 2)
		{
			list($SplitDate,$SplitTime) = explode(" ",$Date);
			$SplitDate = explode("-",$SplitDate);
			if (isset($SplitDate[2]) && isset($SplitDate[1]) && isset($SplitDate[0]))
			{
				$SplitTime = explode(":",$SplitTime);
				$SplitDate = mktime($SplitTime[0],$SplitTime[1],$SplitTime[2],$SplitDate[1],$SplitDate[2],$SplitDate[0]);
				if ($Time == 1)
					return(date("d-M-Y H:i:s",$SplitDate));
				else if ($Time == 2)
					return($SplitDate);
				else
					return(date("d-M-Y",$SplitDate));
			}
			else
				return("-----");
		}
	}

	function SendXML($Url,$Data)
	{/* Written By : Mohammad Kaiser Anwar */
		$SpecialData = false;
		$DataLen = strlen($Data);
		if ($DataLen > 0)
		{
			if (substr($Data,0,1) == "{")
			{
				$SpecialData = true;
				$Header[] = "Accept: application/json";
			}
			elseif (substr($Data,0,5) == "<?xml")
			{
				$SpecialData = true;
				$Header[] = "Accept: text/xml";
			}
			$Header[] = "MIME-Version: 1.0";
			$Header[] = "Content-type: multipart/mixed;";
			$Header[] = "charset=UTF-8";
			$Header[] = "Content-length: ".$DataLen;
			$Header[] = "Cache-Control: no-cache";
			$Header[] = "Connection: close \r\n";
			$Header[] = $Data;
		}
		$Curl = curl_init();
		curl_setopt($Curl, CURLOPT_URL,"$Url");
		curl_setopt($Curl, CURLOPT_TIMEOUT, 0);
		curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
		if ($DataLen > 0)
		{
		curl_setopt($Curl, CURLOPT_HEADER, 1);
		curl_setopt($Curl, CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($Curl, CURLOPT_HTTPHEADER, $Header);
		}
		$Response = curl_exec($Curl);
		if (curl_errno($Curl))
		{
			echo("CURL Error = ".curl_error($Curl));
			curl_close($Curl);
			die;
		}
		else
		{
			curl_close($Curl);
			return($Response);
		}
	}

	function UrlString($UrlString)
	{
		$UrlString = strtolower(stripslashes(stripslashes($UrlString)));
		$UrlString = str_replace("&","and",$UrlString);
		$UrlString = str_replace("-"," ",$UrlString);
		$UrlString = str_replace("_"," ",$UrlString);
		$UrlString = str_replace("___","",$UrlString);
		$UrlString = TrimText($UrlString,0);
		$UrlString = str_replace(",","",$UrlString);
		$UrlString = str_replace(".","",$UrlString);
		$UrlString = str_replace("*","",$UrlString);
		$UrlString = str_replace("%","",$UrlString);
		$UrlString = str_replace("/","",$UrlString);
		$UrlString = str_replace("\\","",$UrlString);
		$UrlString = str_replace("'","",$UrlString);
		$UrlString = str_replace('"',"",$UrlString);
		$UrlString = str_replace("(","",$UrlString);
		$UrlString = str_replace(")","",$UrlString);
		$UrlString = str_replace("+","",$UrlString);
		$UrlString = str_replace("!","",$UrlString);
		$UrlString = str_replace("`","",$UrlString);

		$UrlString = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo|circumflex|umlaut|reverse question mark|esszett|reverse examation mark|paragraph);/i', '\\1', $UrlString );
		$UrlString = preg_replace('@[^\d\w\s,.;:]@', '', $UrlString);

		$UrlString = trim($UrlString);
		$UrlString = str_replace(" ","-",$UrlString);
		return($UrlString);
	}

	function uuid()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	function FindLatLong($Address,$ApiPro)
	{
		$Latitude = $Longitude = 0.00;
		if ($ApiPro == 1)
		{
			$ApiKey = "AIzaSyDsTtWrLCPC0VqVKHh6ZstgawxJWxqK-Iw";
			$Url = "https://maps.googleapis.com/maps/api/geocode/xml?key=".$ApiKey.
				"&address=".str_replace(" ","%20",$Address).",%20UK&sensor=true";
		}
		else if ($ApiPro == 2)
		{
			$Url = "http://dev.virtualearth.net/REST/v1/Locations?countryRegion=AU&adminDistrict=NSW".
				"&locality=Abbotsford&postalCode=4670&key={BingMapsKey}";
		}
		$Data = SendXML($Url,"");
		//echo($Url."<br><br>".$Data);
		$XML = simplexml_load_string($Data);
		if ($XML->status == "OK")
		{
			$Address = $XML->result->formatted_address;
			$Latitude = $XML->result->geometry->location->lat;
			$Longitude = $XML->result->geometry->location->lng;
		}
		else if ($XML->status == "OVER_QUERY_LIMIT")
		{

		}
		$Parameters = array();
		$Parameters[] = $Latitude;
		$Parameters[] = $Longitude;
		return $Parameters;
	}
?>