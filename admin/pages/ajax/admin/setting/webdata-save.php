<?php
	$PagePath = "../../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	$LoginReq = false;
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "".
		"{".
			"\"Status\"  : \"[Status]\",".
			"\"Message\" : \"[Message]\",".
			"\"DataID\"  : \"[DataID]\"".
		"}";
	if (isset($_POST["DataID"]))
	{
		$DataID = $_POST['DataID'];
		$cboType = $_REQUEST["cboType"];
		if ($DataID == 0)
			$AddNew = true;
		else
			$AddNew = false;
		// Check WebData Name
		if ($_REQUEST["txtHead"] != "")
		{
			$Query = "SELECT dataid FROM webdata".
				" WHERE datahead = '".TrimText($_REQUEST["txtHead"],1)."'";
			if ($DataID > 0)
			{
				$Query .= " AND dataid <> ".$DataID;
			}
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow))
			{
				$RespHead = "Error";
				$RespText = "Same Data Already Exitst";
				goto Response;
			}
		}
		if ($AddNew == true)
		{
			$DataID = GetMax("webdata","dataid");
			$Query = "INSERT INTO webdata".
				" (dataid, datatitle, datahead, datatext, dataicon, ".
				" databutton, datalink, datatype, status, sorting)".
				" VALUES (".$DataID.", ".
				" '".TrimText($_REQUEST['txtTitle'],1)."',".
				" '".TrimText($_REQUEST['txtHead'],1)."',".
				" '".TrimText($_REQUEST['txtText'],1)."',".
				" '".TrimText($_REQUEST['txtIcon'],1)."',".
				" '".TrimText($_REQUEST['txtButton'],1)."',".
				" '".TrimText($_REQUEST['txtLink'],1)."',".
				"  ".sprintf("%d",$_REQUEST["cboType"]).",".
				"  ".sprintf("%d",$_REQUEST["cboStatus"]).",".
				" '".TrimText($_REQUEST["txtSorting"],1)."')";
		}
		else
		{
			$Query = "UPDATE webdata SET".
				"  datatitle  = '".TrimText($_REQUEST["txtTitle"],1)."'".
				", datahead   = '".TrimText($_REQUEST["txtHead"],1)."'".
				", datatext   = '".TrimText($_REQUEST["txtText"],1)."'".
				", dataicon   = '".TrimText($_REQUEST["txtIcon"],1)."'".
				", databutton = '".TrimText($_REQUEST["txtButton"],1)."'".
				", datalink   = '".TrimText($_REQUEST["txtLink"],1)."'".
				", status     = ".sprintf("%d",$_REQUEST["cboStatus"])."".
				", datatype   = ".sprintf("%d",$_REQUEST["cboType"])."".
				", sorting    = '".TrimText($_REQUEST["txtSorting"],1)."'".
				"  WHERE DataID = ".$DataID;
		}
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			if ($AddNew == true)
				$RespText = "Unable To Add New Data";
			else
				$RespText = "Unable To Save Data";
			$RespText .= "<br><br>".mysqli_error($Conn)."<br><br>".$Query;
		}
		else
		{
			if ($cboType == 1)
			{
				if (isset($_REQUEST["CroppedImg"]))
				{
					if ($_REQUEST["cboType"] == 1)
					{
						$MyFile = $PagePath."../images/howitworks/".$DataID.".png";
					}
					// elseif ($_REQUEST["cboType"] == 8)
					// {
					// 	$MyFile = $PagePath."../images/team/".$DataID.".jpg";
					// }
					// else
					// {
					// 	$MyFile = $PagePath."../images/webdata-img/".$DataID.".jpg";
					// }

					//$MyFile = $PagePath."../images/banner/".$DataID.".jpg";
					$PhotoData = $_POST['CroppedImg'];
					if ($PhotoData != "")
					{
						$ImgAry    = explode(";",$PhotoData);
						$ImgAry    = explode(",",$ImgAry[1]);
						$PhotoData = base64_decode($ImgAry[1]);
						file_put_contents($MyFile,$PhotoData);
					}
				}
				else
				{
					if (is_uploaded_file($_FILES["txtImg"]["tmp_name"]))
					{
						if ($_REQUEST["cboType"] == 1)
						{
							$MyFile = $PagePath."../images/howitworks/".$DataID.".png";
						}
						// elseif ($_REQUEST["cboType"] == 8)
						// {
						// 	$MyFile = $PagePath."../images/team/".$DataID.".jpg";
						// }
						// else
						// {
						// 	$MyFile = $PagePath."../images/webdata-img/".$DataID.".jpg";
						// }
						move_uploaded_file($_FILES["txtImg"]["tmp_name"],$MyFile);
					}
				}
			}	
			if ($AddNew == true)
			{
				$RespHead = "Added";
				$RespText = "New Data Added Successfully.";
			}
			else
			{
				$RespHead = "Updated";
				$RespText = "Data Detail Saved Successfully.";
			}
		}
	}
Response:
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation ...";
	}
	$Response = str_replace("[Status]",$RespHead,$Response);
	$Response = str_replace("[Message]",$RespText,$Response);
	$Response = str_replace("[DataID]",$DataID,$Response);
	echo($Response);
?>