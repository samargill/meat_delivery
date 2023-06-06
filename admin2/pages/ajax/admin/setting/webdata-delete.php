<?php
	$PagePath = "../../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	$LoginReq = false;
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\", \"ReturnID\": \"[ReturnID]\" }";
	if (isset($_POST['DelData']))
	{
		$DataType = $_POST['DataType'];
		$Query = "DELETE FROM webdata WHERE dataid = ".$_POST['DataID'];
		mysqli_query($Conn,$Query);
		if ($DataType == 1)
		{
			$FileName = $_POST['DataID'].".jpg";
			$FilePath = $PagePath."../images/banner/".$FileName;
		}
		else
		{
			$FileName = $_POST['DataID'].".jpg";
			$FilePath = $PagePath."../images/webdata-img/".$FileName;
		}
		if (file_exists($FilePath))
		{
			unlink($FilePath);
		}
		if (mysqli_errno($Conn) == 0)
		{
			$RespHead = "Done";
			$RespText = "Data Deleted Successfully.";
		}
		else
		{
			$RespHead = "Error";
			$RespText = "An Unexpected Error Occured. ".mysqli_error($Conn);
		}
		$Response = str_replace("[Status]",$RespHead,$Response);
		$Response = str_replace("[Message]",$RespText,$Response);
		echo($Response);
		die;
	}
	if (strpos($Response,"[Status]") > 0)
	{
		$Response = str_replace("[Status]","Error",$Response);
		$Response = str_replace("[Message]","Undefined Operation ...",$Response);
		echo($Response);
		die;
	}
?>