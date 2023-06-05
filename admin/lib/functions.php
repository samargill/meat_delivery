<?php
	function GetUniqueCode($Length)
	{
		if (function_exists("random_bytes"))
		{
			$Bytes = random_bytes(ceil($Length / 2));
		}
		elseif (function_exists("openssl_random_pseudo_bytes"))
		{
			$Bytes = openssl_random_pseudo_bytes(ceil($Length / 2));
		}
		else
		{
			throw new Exception("no cryptographically secure random function available");
		}
		return(substr(bin2hex($Bytes),0,$Length));
	}

	function GetMax($Table,$Field,$Where="1",$DBName="")
	{
		if ($DBName == "")
			$DBName = DBName;
		if (substr($Table,0,1) == "@")
		{
			$Debug = true;
			$Table = substr($Table,1);
		}
		else
			$Debug = false;
		$Query = "SELECT MAX(".$Field.") + 1 As MaxID FROM ".$Table." WHERE ".$Where;
		if ($Debug == true)
		{
			echo($Query); die;
		}
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

	function GetColCode($ColID,$RowID)
	{
		if ($ColID <= 25)
			return(chr(65+$ColID).$RowID);
		else
			return("A".chr(65+($ColID-26)).$RowID);
	}

	function GetAddress($AddressID)
	{
		$Address = $PostCode = "";
		$Status = 0;
		$Query = "SELECT suburb, state, postcode, status".
			" FROM addresses WHERE addressid = ".$AddressID;
		$rstRow = mysqli_query($GLOBALS['Conn'],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Address  = $objRow->suburb.", ".$objRow->state;
			$PostCode = $objRow->postcode;
			$Status   = $objRow->status;
		}
		return(array($Address,$PostCode,$Status));
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

	function UrlString($UrlString)
	{
		$UrlString = strtolower(stripslashes(stripslashes($UrlString)));
		$UrlString = str_replace("&","and",$UrlString);
		$UrlString = str_replace("-"," ",$UrlString);
		$UrlString = str_replace("_"," ",$UrlString);
		$UrlString = str_replace(" ","_",$UrlString);
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
		$UrlString = str_replace(" ","_",$UrlString);
		return($UrlString);
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

	function GetBarcode($Barcode)
	{
		$Length = strlen($Barcode);
		$i = $Index = $Even = $Odd = 0;
		for ($i = $Length - 2; $i >= 0; $i--)
		{
			$Index++;
			if ($Index % 2 == 0)
				$Even = $Even + intval(substr($Barcode,$i,1));
			else
				$Odd  = $Odd  + intval(substr($Barcode,$i,1));
		}
		$Odd = $Odd * 3;
		$Even = $Odd + $Even;
		$Even = 10 - $Even % 10;
		if ($Even == 10) $Even = 0;
		return(substr($Barcode,0,$Length - 1) . $Even);
	}

	function ShowDate($Date,$Time)
	{/* Written By : Mohammad Kaiser Anwar */
		if (($Date == "0000-00-00" || $Date == NULL) && $Time == 0)
		{
			return("00-00-0000");
		}
		elseif (($Date == "0000-00-00 00:00:00" || $Date == NULL) && $Time == 1)
		{
			return("00-00-0000 00:00:00");
		}
		elseif (($Date == "0000-00-00 00:00:00" || $Date == NULL) && ($Time == 3))
		{
			return("");
		}
		elseif (($Date == "0000-00-00 00:00:00" || $Date == NULL) && ($Time == 4))
		{
			return("");
		}
		elseif ($Time == 0)
		{
			$SplitDateTime = explode(" ",$Date);
			$SplitDate = explode("-",$SplitDateTime[0]);
			if (isset($SplitDate[2]) && isset($SplitDate[1]) && isset($SplitDate[0]))
				return(date("d-M-Y",mktime(0,0,0,$SplitDate[1],$SplitDate[2],$SplitDate[0])));
			else
				return("-----");
		}
		elseif ($Time == 3)
		{
			return(date("d-M-Y H:i",strtotime($Date)));
		}
		elseif ($Time == 4)
		{
			return(date("d-M-y H:i",strtotime($Date)));
		}
		elseif ($Time == 1 || $Time == 2)
		{
			list($SplitDate,$SplitTime) = explode(" ",$Date);
			$SplitDate = explode("-",$SplitDate);
			if (isset($SplitDate[2]) && isset($SplitDate[1]) && isset($SplitDate[0]))
			{
				$SplitTime = explode(":",$SplitTime);
				$SplitDate = mktime($SplitTime[0],$SplitTime[1],$SplitTime[2],$SplitDate[1],$SplitDate[2],$SplitDate[0]);
				if ($Time == 1)
					return(date("d-M-Y H:i:s",$SplitDate));
				elseif ($Time == 2)
					return($SplitDate);
				else
					return(date("d-M-Y",$SplitDate));
			}
			else
				return("-----");
		}
	}


	function CheckPostCode($PostCode)
	{
		if (strlen($PostCode) <> 4)
		{
			return(false);
		}
		return(preg_match("/^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$/",$PostCode));
	}

	function GetPostCodeAddress($PostCode)
	{
		$Latitude = $Longitude = 0.00;
		$Url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".str_replace(" ","%20",$_REQUEST['txtPostCode']).",%20AU&sensor=true";
		$Curl = curl_init();
		curl_setopt($Curl, CURLOPT_URL,"$Url");
		curl_setopt($Curl, CURLOPT_TIMEOUT, 0);
		curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
		$Data = curl_exec($Curl);
		if (curl_errno($Curl) && curl_errno($Curl) != 6)
		{
			echo("CURL Error [ ".curl_errno($Curl)." ] = ".curl_error($Curl));
			die;
		}
		curl_close($Curl);
		$XML = simplexml_load_string($Data);
		if ($XML->status == "OK")
		{
			$Address = strval($XML->result->formatted_address);
			$Latitude = floatval($XML->result->geometry->location->lat);
			$Longitude = floatval($XML->result->geometry->location->lng);
		}
		$Parameters = array();
		$Parameters[0] = $Latitude;
		$Parameters[1] = $Longitude;
		$Parameters[2] = $Address;
		return($Parameters);
	}

	function SendEmail($From,$To,$Subject,$Email)
	{
		$EmailHeaders = "From: " . $From . "\r\n".
			"Reply-To: " . $From . "\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/html; charset=UTF-8\r\n";
			"X-Mailer: PHP/" . phpversion();
		mail($To,$Subject,$Email,$EmailHeaders);
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

	function ForgotPasswordAdminEmail($Email,$ResetLink)
	{
		if (!filter_var($Email,FILTER_VALIDATE_EMAIL))
		{
			header("Location: index?Err=3");
			exit;
		}
		if (defined("CopyrightYear") == false)
		{
			define("CopyrightYear",GetValue("config_value","websettings","config_id = 102"));
		}
		$EmailBody = GetForgotPasswordAdmin($Email,$ResetLink);
		if (DBUserName == "myitvh")
		{
			echo $EmailBody;
			die();
		}
		else
		{
			$PHPMailer = new PHPMailer();
			$PHPMailer->SendEmail(constant("FromEmail"),$Email,"Password Reset Request",$EmailBody);
		}
	}

	function ForgotPasswordAdminMobile($Mobile,$ResetLink)
	{
		if (DBUserName != "myitvh")
		{
			$AdminName = GetValue("firstname","adminlogin","mobile = ".$Mobile);
			$Sms = "Prime Medic".
				"Hi ".$AdminName." ".
				"\n\nYou recently requested to reset your password.
				\n\nPlease click the link below to change password.
				\n\n".$ResetLink."
				\n\nOr copy and paste the link.
				\n\nIf you didn't requested a password reset, someone may have been
				trying to access your account without your permission. As long as 
				you do not click the link contained in this email, no action will be
				taken and your account will remain secure.
				\n\nKind Regards,
				\nPrime Medic's Team";
			SendSMS($Mobile,$Sms);
		}
	}

	function GetResetLink($AdminID,$Type,$Email)
	{
		$PwdCode = mt_rand(1000000000,9999999999);
		$WebsiteUrl    = constant("WebsiteUrl");
		$Query = "UPDATE adminlogin SET ".
			"  pwdresetcode = '".$PwdCode."'".
			", pwdresettime = NOW()".
			"  WHERE adminid = ".$AdminID;
		@mysqli_query($GLOBALS["Conn"],$Query);
		$ResetLink = $WebsiteUrl."/admin/reset-password?Type=".$Type."&Email=".urlencode($Email)."&Code=".$PwdCode;
		return ($ResetLink);
	}

	function GetDateMysql($Value)
	{
		$Date  = $Time = "";
		list($Date,$Time) = explode(" ",$Value);
		$Date = explode("/",$Date);
		if (count($Date) != 3)
		{
			$Date = explode("-",$Date);
		}
		if (strlen($Date[0]) == 2)
		{
			$Temp = $Date[2];
			$Date[2] = $Date[0];
			$Date[0] = $Temp;
		}
		$Date = $Date[2]."-".$Date[1]."-".$Date[0]." ".$Time;
		if (strlen($Date) == 16)
		{
			$Date = $Date.":00";
		}
		return $Date;
	}
	
	function GetPhoneMob($Mobile)
	{
		if ($Mobile != "")
		{
			$Mobile = str_replace(" ","", $Mobile);
			$Mobile = str_replace("(","", $Mobile);
			$Mobile = str_replace(")","", $Mobile);
		}
		return $Mobile;
	}

	function ShowNumber($Num)
	{
		return number_format($Num);
	}
?>