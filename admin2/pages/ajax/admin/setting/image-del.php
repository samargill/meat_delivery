<?php
	$PagePath = "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	
	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\" }";
	if (isset($_REQUEST['DelSlide'])) 
	{
		$ImgID = $_REQUEST['ImgID'];
		$Image_Name = GetValue("imagename","webimage","imageid = ".$ImgID);
		$Directory_Name = $PagePath.'../images/webdata-img/';     //folder where image will upload
		unlink($Directory_Name.$Image_Name); 	//Deleting Old Photo
		$Query = "DELETE FROM webimage WHERE imageid = ".$ImgID;
		mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0) 
		{
			$RespHead = "Error";
			$RespText = "Failed To Delete Image. Try Again ...<br><br>".mysqli_error($Conn);
		}
		else
		{
			$RespHead = "Done";
			$RespText = "Image Deleted Successfully.";
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