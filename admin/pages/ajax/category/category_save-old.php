<?php
	$PageID 	= array(2,0,0);
	$PagePath 	= "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "".
		"{".
			"\"Status\"  : \"[Status]\",".
			"\"Message\" : \"[Message]\",".
			"\"UserID\"  : \"[UserID]\"".
		"}";
	if (isset($_POST["CategoryID"]))
	{
		$CategoryID = $_POST['CategoryID'];
		if ($CategoryID == 0)
		{
			$AddNew = true;
			if (CheckRight("Add","Return") == false)
			{
				$RespHead = "Error";
				$RespText = "You Are Not Authorized To Perform This Operation";
				goto Response;
			}
		}
		else
		{
			$AddNew = false;
			if (CheckRight("Edit","Return") == false)
			{
				$RespHead = "Error";
				$RespText = "You Are Not Authorized To Perform This Operation";
				goto Response;
			}
		}
		$Query = "SELECT category_id FROM product_category".
			" WHERE name = '".TrimText($_REQUEST["txtName"],1)."'";
		if ($CategoryID > 0)
		{
			$Query .= " AND category_id <> ".$CategoryID;
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$RespHead = "Error";
			$RespText = "Same Category Name Already Exist ...";
			goto Response;
		}

		if ($CategoryID == 0)
		{
			$Add 	= true;
			$CategoryID = GetMax("product_category","category_id");
			$Query  = "INSERT INTO product_category".
				" (category_id, name, parent_id, meta_title, meta_desc, meta_keywords, adddate, status)".
				" VALUES (".$CategoryID.", '".TrimText($_POST['txtName'],1)."', ".$_POST["cboParent"].", ".
				" '".TrimText($_POST['txtTitle'],1)."', '".TrimText($_POST['txtKeywords'],1)."',".
				" '".TrimText($_POST['txtDesc'],1)."', NOW(), ".$_POST['cboStatus'].")";
		}
		else
		{
			$Add = false;
			$Query = "UPDATE product_category SET ";
			$Query .= "".
				"  firstname      		= '".TrimText($_POST['txtName'],1)."'".
				", parent_id       		= '".TrimText($_POST['txtLastName'],1)."'".
				", meta_title       	= '".TrimText($_POST['txtPhone'],1)."'".
				", meta_desc        	= '".TrimText($_POST['txtMobile'],1)."'".
				", meta_keywords 		= '".TrimText($_POST['txtEmail'],1)."'".
				", lastedit       		=  NOW()".
				", status         		=  ".$_POST['cboStatus'].
				"  WHERE category_id 	=  ".$CategoryID;
		}
		if (!mysqli_query($Conn,$Query))
		{
			$RespHead = "Error";
			if ($Add == true)
				$RespText = "Unable To Add New Category ...";
			else
				$RespText = "Unable To Update Category ...";
				$Err = 102;
			goto Response;
		}
		$RespHead = "Done";
		if ($Add == true)
		{
			$RespText = "Category Added Successfully ...";
		}
		else
		{
			$RespText = "Category Updated Successfully ...";
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
	$Response = str_replace("[CategoryID]",$CategoryID,$Response);
	echo($Response);