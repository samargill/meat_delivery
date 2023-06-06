<?php
	function DBCombo($CboName,$Table,$ValueField,$DisplayField,$Condition,$Selected,$DisplayText,$CssClass,$JScript,$Format=0,$DBName="",$DisplayTextID=0)
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
		echo("<select name=\"$CboName\" id=\"$CboName\" class=\"$CssClass\" $JScript>");
		if ($DisplayText != "")
		{
			if ($Selected == $DisplayTextID)
				$ComboSelect = "selected";
			else
				$ComboSelect = "";
			echo("<option value=\"".$DisplayTextID."\" ".$ComboSelect.">$DisplayText</option>");
		}
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
					$ComboSelect = "selected";
				else
					$ComboSelect = "";
				echo("<option value=\"".$objRow[0]."\" ".$ComboSelect.">".$Value."</option>");
			}
		}
		echo("</select>");
	}

	function DBComboArray($CboName,$ComboData,$StartIndex,$Selected,$CssClass,$JScript)
	{/* Written By : Mohammad Kaiser Anwar */
		if (is_array($ComboData) == false)
		{
			if ($ComboData == "Yes|No")
			{
				$ComboData = array();
				$ComboData[] = "- Select -";
				$ComboData[] = "Yes";
				$ComboData[] = "No";
			}
			elseif ($ComboData == "Enable|Disable")
			{
				$ComboData = array();
				$ComboData[] = "- Select -";
				$ComboData[] = "Enable";
				$ComboData[] = "Disable";
			}
		}
		echo("<select name=\"$CboName\" id=\"$CboName\" class=\"$CssClass\" $JScript>");
		foreach ($ComboData as $ComboKey => $ComboValue)
		{
			if ($Selected == $ComboKey)
				$ComboSelect = "selected";
			else
				$ComboSelect = "";
			echo("<option value=\"".$ComboKey."\" ".$ComboSelect.">".$ComboData[$ComboKey]."</option>");
		}
		echo("</select>");
	}

	function StatusCombo($CboName,$cboStatus,$Default="")
	{
		$Start = 0;
		$ComboData = array();
		if ($Default != "")
		{
			$Start = -1;
			$ComboData[-1] = $Default;
		}
		$ComboData[0] = "Disabled";
		$ComboData[1] = "Enabled";
		echo("<select name=\"".$CboName."\" id=\"".$CboName."\" CLASS=\"form-control select2\">");
		for ($i = $Start; $i < count($ComboData); $i++)
		{
			if ($cboStatus == $i)
				$ComboSelect = "SELECTED";
			else
				$ComboSelect = "";
			echo("<option value=\"".$i."\" ".$ComboSelect.">".$ComboData[$i]."</option>");
		}
		echo("</select>");
	}

	function DaysCombo($CboName,$Selected,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<select name=\"$CboName\" class=\"$CssClass\" $JScript>");
		if ($Small == true)
			echo("<option value=\"0\">-DD-</option>");
		else
			echo("<option value=\"0\">-Days-</option>");
		for ($i = 1; $i <= 31; $i++)
		{
			$Value = str_pad($i,2,"0",STR_PAD_LEFT);
			if ($i == $Selected)
				echo("<option value=\"$Value\" selected>$Value</option>");
			else
				echo("<option value=\"$Value\">$Value</option>");
		}
		echo("</SELECT>");
	}

	function MonthsCombo($CboName,$Selected,$Default,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<select id=\"$CboName\" name=\"$CboName\" class=\"$CssClass\" $JScript>");
		if ($Default == "")
		{
			if ($Small == true)
				$Default = "-MMM-";
			else
				$Default = "-Months-";
		}
		echo("<option value=\"0\">".$Default."</option>");
		$Months = array("","January","Feburary","March","April","May","June","July","August","September","October","November","December");
		for ($i = 1; $i <= 12; $i++)
		{
			if ($Small == true)
				$Value = substr($Months[$i],0,3);
			else
				$Value = $Months[$i];
			if ($Selected == str_pad($i,2,"0",STR_PAD_LEFT))
				echo("<option value=\"".str_pad($i,2,"0",STR_PAD_LEFT)."\" selected>".$Value."</option>");
			else
				echo("<option value=\"".str_pad($i,2,"0",STR_PAD_LEFT)."\">".$Value."</option>");
		}
		echo("</select>");
	}

	function YearsCombo($CboName,$StartYear,$EndYear,$Selected,$CssClass,$JScript,$Small=true)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<select id=\"$CboName\" name=\"$CboName\" class=\"$CssClass\" $JScript>");
		if ($Small == true)
			echo("<option value=\"0\">-YYYY-</option>");
		else
			echo("<option value=\"0\">-Years-</option>");
		for ($i = $StartYear; $i <= $EndYear; $i++)
		{
			if ($i == $Selected)
				echo("<option value=\"$i\" selected>$i</option>");
			else
				echo("<option value=\"$i\">$i</option>");
		}
		echo("</SELECT>");
	}

	function NumCombo($CboName,$StartValue,$EndValue,$Selected,$Default,$CssClass,$JScript,$Pad=0,$Jumps=1)
	{/* Written By : Mohammad Kaiser Anwar */
		echo("<SELECT NAME=\"$CboName\" CLASS=\"$CssClass\" $JScript>");
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