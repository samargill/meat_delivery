<?php
	function GetQuestion($Text,$FamilyID,$Gender,$DOB,$Path="")
	{
		$QueText = "";
		/*
		if ($FamilyID > 1)
		{
			$QueText .= "Ask ".$PatientName."<br>";
		}
		*/
		$QueText .= str_replace("[CompanyName]",WebsiteTitle,$Text);
		if (strlen($DOB) == 10)
		{
			$DOB = ShowDate($DOB,0);
		}
		$QueText  = str_replace("[DOB]",$DOB,$QueText);
		$QueText  = str_replace("[Path]",$Path,$QueText);
		$SpecWord = array();
		preg_match_all('/\[([^\]]+)\]/', $QueText, $SpecWord);
		for ($j = 0; $j < count($SpecWord[1]); $j++)
		{
			$ChanWord = explode("|",$SpecWord[1][$j]);
			if (count($ChanWord) == 2)
			{
				if ($FamilyID == 1)
				{
					$QueText = str_replace($SpecWord[0][$j],$ChanWord[0],$QueText);
				}
				else
				{
					$QueText = str_replace($SpecWord[0][$j],$ChanWord[1],$QueText);
				}
			}
		}
		$Dict = array();
		$Dict[] = "his / her";
		$Dict[] = "His / Her";
		$Dict[] = "him / her";
		$Dict[] = "he / she";
		$Dict[] = "He / She";
		if ($Gender == "Male")
			$Gender = 1;
		else if ($Gender == "Female")
			$Gender = 2;
		for ($i = 0; $i < count($Dict); $i++)
		{
			$DictOpt = explode("/",$Dict[$i]);
			$QueText = str_replace($Dict[$i],$Gender == 1 ? trim($DictOpt[0]) : trim($DictOpt[1]),$QueText);
		}
		return($QueText);
	}
	function GetAnswerJson()
	{
		$Answers = "{";
		for ($i = 1; $i <= $_POST['TotalQue']; $i++)
		{
			if (isset($_POST['rdoQue'.$i]))
			{
				if ($Answers != "{")
				{
					$Answers .= ",";
				}
				if (is_array($_POST['rdoQue'.$i]))
				{
					for ($j = 0; $j < count($_POST['rdoQue'.$i]); $j++)
					{
						if (preg_match("/^[\d]+\-[\d]+$/",$_POST['rdoQue'.$i][$j]))
							$Que = explode("-",$_POST['rdoQue'.$i][$j]);
						else
						{
							$Que = explode("-",$_POST['hdnQue'.$i]);
							$Que[1] = "[".$_POST['rdoQue'.$i][$j]."]";
						}
						if ($j == 0)
							$Answers .= " \"".$Que[0]."\":\"";
						else
							$Answers .= ", ";
						$Answers .= $Que[1];
					}
					$Answers .= "\"";
				}
			}
		}
		$Answers .= " }";
		return($Answers);
	}
	function PrintQuestion($PrintHeader,$Heading,$SubHeading,$QueJson,$Person,$Path)
	{
		if ($Heading != "")
		{
			$Heading = "<tr><td>".$Heading."</td></tr>";
		}
		if ($SubHeading != "")
		{
			$SubHeading = "<tr><td>".$SubHeading."</td></tr>";
		}
		$TDStyle = "style=\"border: 1px solid black;\"";
		$Print = <<<EOD
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		{$PrintHeader}
		{$Heading}
		{$SubHeading}
		<tr>
			<td>
			<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-family: Verdana; font-size: 10px;">
EOD;
		$Json = json_decode($QueJson,true);
		$QueIDs = join(", ",array_keys($Json));
		$QueAns = array_values($Json);
		$Query = "SELECT queid, quetext, quetype, answer, wrongalertid, wrongalerttype".
			" FROM question".
			" WHERE queid IN (".$QueIDs.") ORDER BY FIELD(queid, ".$QueIDs.")";
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		$i = -1;
		while ($objRow = mysqli_fetch_object($rstRow))
		{
			$i++;
			$QueText = GetQuestion($objRow->quetext,$Person["ID"],$Person["Gender"],$Person["DOB"],$Path);
			if ($objRow->quetype == 0)
				$QueResult = ($QueAns[$i] == 1 ? "Yes" : "No");
			elseif ($objRow->quetype == 4)
				$QueResult = substr($QueAns[$i], 1, -1);
			else
			{
				$QueResult = "";
				$Query = "SELECT choicetext FROM questionchoice".
					" WHERE queid = ".$objRow->queid." AND choiceid IN (".$QueAns[$i].") ORDER BY choiceid";
				$rstPro = mysqli_query($GLOBALS["Conn"],$Query);
				while ($objPro = mysqli_fetch_object($rstPro))
				{
					if ($QueResult != "")
					{
						$QueResult .= ", ";
					}
					$QueResult .= $objPro->choicetext;
				}
			}
			if (($objRow->quetype == 0 || $objRow->quetype == 1) && $objRow->wrongalerttype == 0 
				&& $objRow->wrongalertid > 0 && $objRow->answer > 0 && $objRow->answer != $QueAns[$i])
			{
				$QueErr = GetValue("quealerttext","questionalert","quealertid = ".$objRow->wrongalertid);
				$QueResult .= "<br><br><span style=\"font-weight: bold; color: red;\">".$QueErr."</span>";
			}
		$Print .= <<<EOD
				<tr>
					<td align="left" height="20px" {$TDStyle}>{$QueText}<br><font style="color: red;">{$QueResult}</font></td>
				</tr>
EOD;
		}
		$Print .= <<<EOD
			</table>
			</td>
		</tr>
	</table>
EOD;
		return($Print);
	}
?>