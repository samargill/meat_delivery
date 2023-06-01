<?php
	function DBCombo($CboName,$Table,$ValueField,$DisplayField,$Condition,$Selected,$DisplayText,$CssClass,$JScript,$Format=0,$DBName="")
	{/* Written By : Mohammad Kaiser Anwar */
		if ($DBName == "")
			$DB = DBName;
		else
			$DB = $DBName;
		$Query = "SELECT ";
		if (strpos($Table,"JOIN") > 0) $Query .= "DISTINCT";
		$Query .= " $ValueField, $DisplayField FROM $Table $Condition";
		if ($CssClass == "STOP")
		{
			echo($Query);
			die;
		}
		echo("<SELECT NAME=\"$CboName\" CLASS=\"$CssClass\" $JScript>");
		if ($DisplayText != "") echo("<OPTION VALUE=\"0\" SELECTED>$DisplayText</OPTION>");
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query) or die(mysqli_error($GLOBALS["Conn"]));
		if (mysqli_num_rows($rstRow) > 0)
		{
			while ($objRow = mysqli_fetch_array($rstRow))
			{
				if ($Format == 0)
					$Value = UCString(stripslashes($objRow[1]));
				else
					$Value = stripslashes($objRow[1]);
				if ($objRow[0] == $Selected)
					echo("<OPTION VALUE=\"".$objRow[0]."\" SELECTED>".$Value."</OPTION>");
				else
					echo("<OPTION VALUE=\"".$objRow[0]."\">".$Value."</OPTION>");
			}
		}
		echo("</SELECT>");
	}

	function DaysCombo($CboName,$Selected,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<SELECT NAME=\"$CboName\" CLASS=\"$CssClass\" $JScript>");
		if ($Small == true)
			echo("<OPTION VALUE=\"0\">-DD-</OPTION>");
		else
			echo("<OPTION VALUE=\"0\">-Days-</OPTION>");
		for ($i = 1; $i <= 31; $i++)
		{
			$Value = str_pad($i,2,"0",STR_PAD_LEFT);
			if ($i == $Selected)
				echo("<OPTION VALUE=\"$Value\" SELECTED>$Value</OPTION>");
			else
				echo("<OPTION VALUE=\"$Value\">$Value</OPTION>");
		}
		echo("</SELECT>");
	}

	function MonthsCombo($CboName,$Selected,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<SELECT NAME=\"$CboName\" CLASS=\"$CssClass\" $JScript>");
		if ($Small == true)
			echo("<OPTION VALUE=\"0\">-MMM-</OPTION>");
		else
			echo("<OPTION VALUE=\"0\">-Months-</OPTION>");
		$Months = array("","January","Feburary","March","April","May","June","July","August","September","October","November","December");
		for ($i = 1; $i <= 12; $i++)
		{
			if ($Small == true)
				$Value = substr($Months[$i],0,3);
			else
				$Value = $Months[$i];
			if ($Selected == str_pad($i,2,"0",STR_PAD_LEFT))
				echo("<OPTION VALUE=\"".str_pad($i,2,"0",STR_PAD_LEFT)."\" SELECTED>".$Value."</OPTION>");
			else
				echo("<OPTION VALUE=\"".str_pad($i,2,"0",STR_PAD_LEFT)."\">".$Value."</OPTION>");
		}
		echo("</SELECT>");
	}

	function YearsCombo($CboName,$StartYear,$EndYear,$Selected,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<SELECT NAME=\"$CboName\" CLASS=\"$CssClass\" $JScript>");
		if ($Small == true)
			echo("<OPTION VALUE=\"0\">-YYYY-</OPTION>");
		else
			echo("<OPTION VALUE=\"0\">-Years-</OPTION>");
		for ($i = $StartYear; $i <= $EndYear; $i++)
		{
			if ($i == $Selected)
				echo("<OPTION VALUE=\"$i\" SELECTED>$i</OPTION>");
			else
				echo("<OPTION VALUE=\"$i\">$i</OPTION>");
		}
		echo("</SELECT>");
	}

	function NumCombo($CboName,$StartValue,$EndValue,$Selected,$Default,$CssClass,$JScript,$Pad=0,$Jumps=1)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<select name=\"$CboName\" id=\"$CboName\" class=\"$CssClass\" $JScript>");
		if ($Default != "") echo("<OPTION VALUE=\"0\" SELECTED>$Default</OPTION>");
		if ($Pad == 1)
			$Pad = strlen($EndValue);
		else
			$Pad = 0;
		for ($i = $StartValue; $i <= $EndValue; $i+=$Jumps)
		{
			if ($Pad > 0)
				$Value = str_pad($i,$Pad,"0",STR_PAD_LEFT);
			else
				$Value = $i;
			if ($i == $Selected)
				echo("<OPTION VALUE=\"$Value\" SELECTED>$Value</OPTION>");
			else
				echo("<OPTION VALUE=\"$Value\">$Value</OPTION>");
		}
		echo("</SELECT>");
	}
?>