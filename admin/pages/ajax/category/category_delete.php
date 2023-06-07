<?php
	$PagePath = "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "".
		"{".
			"\"Status\": \"[Status]\",".
			"\"Message\": \"[Message]\",".
			"\"ReturnID\": \"[ReturnID]\"".
		"}";
	
	if (isset($_POST['Validate']))
	{
		$Editable = "";
		$Query = "SELECT product_id FROM product".
			" WHERE category_id = ".$_REQUEST['CategoryID']." LIMIT 0,1";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$Editable = "Have Products";
			goto Response;
		}
Response:
		if ($Editable == "")
		{
			$RespHead = "Delete";
			$RespText = "Are You Sure You Want To Delete This Category ?";
			$ReturnID = "Yes";
		}
		else
		{
			$RespHead = "Unable To Delete";
			$RespText = "This Category Cannot Be Deleted.".
				"<br><br>Category ".$Editable.".".
				"<br><br>You Can Mark This Category As Deleted & This Category Will Become Hidden?".
				"<br><br>Are You Sure You Want To Mark This Category As Deleted ?";
			$ReturnID = "No";
		}
	}
	elseif (isset($_POST['Apply']))
	{
		if ($_POST['Apply'] == "Yes")
		{
			$Query = "DELETE FROM product_category WHERE category_id = ".$_REQUEST['CategoryID'];
			mysqli_query($Conn,$Query);
			if (mysqli_errno($Conn) == 0)
			{
				$RespHead = "Done";
				$RespText = "Category Deleted Successfully.";
			}
			else
			{
				$RespHead = "Error";
				$RespText = "An Unexpected Error Occured. ".mysqli_error($Conn);
			}
		}
		else
		{
			$Query = "UPDATE product_category SET status = 0, deletedate = NOW() WHERE category_id = ".$_REQUEST['CategoryID'];
			mysqli_query($Conn,$Query);
			if (mysqli_errno($Conn) == 0)
			{
				$RespHead = "Done";
				$RespText = "Category Marked As Deleted Successfully.";
			}
			else
			{
				$RespHead = "Error";
				$RespText = "An Unexpected Error Occured. ".mysqli_error($Conn);
			}
		}
	}
	if (isset($RespHead) == false)
	{
		$RespHead = "Error";
		$RespText = "Undefined Operation ...";
	}
	$Response = str_replace("[Status]",$RespHead,$Response);
	$Response = str_replace("[Message]",$RespText,$Response);
	if (isset($ReturnID))
	{
	$Response = str_replace("[ReturnID]",$ReturnID,$Response);
	}
	echo($Response);
?>