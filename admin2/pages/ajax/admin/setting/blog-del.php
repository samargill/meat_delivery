<?php
	$PagePath = "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	
	$Response = "{ \"Status\": \"[Status]\", \"Message\": \"[Message]\" }";
	if (isset($_REQUEST['DelBlog'])) 
	{
		$BlogID = $_REQUEST['BlogID'];
		$Query = "DELETE FROM blog WHERE blogid = ".$BlogID."";
		mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0) 
		{
			$RespHead = "Error";
			$RespText = "Failed To Delete Blog. Try Again ...<br><br>".mysqli_error($Conn);
		}
		else
		{
			$RespHead = "Done";
			$RespText = "Blog Deleted Successfully.";
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