<?php
	function GetDistanceQry($Lat,$Lon,$TAlias)
	{
		$Query = "ROUND((3959 * acos(cos(radians(".$Lat.")) * cos(radians(".$TAlias.".latitude))".
			" * cos(radians(".$TAlias.".longitude) - radians(".$Lon.")) + sin(radians(".$Lat."))".
			" * sin(radians(".$TAlias.".latitude)))),1)";
		return($Query);
	}

	if (function_exists('FindLatLong') == false)
	{
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
	}
?>