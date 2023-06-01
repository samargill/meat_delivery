<?php
	function SendSms($Mobile,$Sms)
	{
		if (DBUserName == "myitvh")
			$ApiUrl = "http://dv-01/BullkySMS/api/sendsms.php";
		else
			$ApiUrl = "http://www.bullkysms.com/api/sendsms.php";
		$UserName   = "m.mohsin@bullkysms.com";
		$Password   = "mohsin123";
		$SMSQueID   = "14";
		$DeviceID   = "61479072226";
		if ($Mobile == "SendToDoc")
		{
			$Mobile = GetValue("mobile","adminlogin","adminid = 3");
		}
		if ($Mobile != "")
		{
			$Json = "".
				"{".
					"\"DocType\": \"SendSMS\",".
					"\"DocDate\": \"".date("Y-m-d H:i:s")."\",".
					"\"UserName\": \"".$UserName."\",".
					"\"Password\": \"".$Password."\",".
					"\"SMSQueID\": \"".$SMSQueID."\",".
					"\"DeviceID\": \"".$DeviceID."\",".
					"\"SendTo\": \"".$Mobile."\",".
					"\"Message\": \"".preg_replace("/\r\n/","\n", $Sms)."\",".
					"\"SendTime\": \"".date("Y-m-d H:i:s")."\"".
				"}";
			$Curl = curl_init($ApiUrl);
			curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($Curl, CURLOPT_POST, 1);
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $Json);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($Curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$Response = curl_exec($Curl);
			curl_close($Curl);
		}
	}
?>