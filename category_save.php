<?php
	$PageID 	= array(2,0,0);
	$PagePath 	= "../../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$Response = "".
		"{".
			"\"Status\"  	: \"[Status]\",".
			"\"Message\" 	: \"[Message]\",".
			"\"CategoryID\" : \"[CategoryID]\"".
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
		$PhotoUpload = false;
		// Upload Thumbnail
		if (is_uploaded_file($_FILES["txtPhoto"]["tmp_name"]))
		{
			/*$ImgSize 	= getimagesize($_FILES["txtPhoto"]["tmp_name"]);
			$Width 		= $ImgSize[0];
			$Height 	= $ImgSize[1];
			if ($Width != 80 && $Height != 80)
			{
				$RespHead = "Error";
				$RespText = "Please Upload Image of Size 80 x 80 ...";
				goto Response;
			}*/
			$Photo = GetValue("photo", "product_category", "category_id = ".$CategoryID);
			if (DBUserName == "root") 
			{
				$PhotoPath = $_SERVER['DOCUMENT_ROOT']."/meatdlv/assets/images/category/";
			}
			else
			{
				$PhotoPath 	= $PagePath."../assets/images/category/";
			}
			if ($Photo == "")
			{
				$Photo = md5(uniqid($CategoryID,true));
			}
			else
			{
				@unlink($PhotoPath.$Photo);
				$Photo = pathinfo($Photo, PATHINFO_FILENAME);
			}
			$Ext = pathinfo($_FILES['txtPhoto']['name'], PATHINFO_EXTENSION);
			$PhotoUpload = move_uploaded_file($_FILES["txtPhoto"]["tmp_name"],$PhotoPath.$Photo.".".$Ext);
			/*if ($PhotoUpload)
			{
				if (!extension_loaded('imagick'))
				{
					$RespHead = "Error";
					$RespText = "Imagick Not Installed ...";
					goto Response;
				}
				$Imagick = new Imagick($PhotoPath.$Photo.".".$Ext);
				if ($Ext != "png")
				{
					$Imagick->setImageFormat('png');
					$Imagick->writeImages($FilePath.".png",true);
					unlink($FilePath.".".$Ext);
				}
				// Create Thumbnail Image
				$Imagick->thumbnailImage(80, 80, true, true);
				$Imagick->setImageFormat('png');
				$Imagick->writeImages($PhotoPath."/thumb/".$Photo.".png",true);
			}*/
			if (file_exists($PhotoPath.$Photo.".png"))
				$Photo = $Photo.".png";
			else
				$Photo = "";
		}

		if ($CategoryID == 0)
		{
			$Add 	= true;
			$CategoryID = GetMax("product_category","category_id");
			$Query  = "INSERT INTO product_category".
				" (category_id, name, parent_id, photo, meta_title, meta_desc, meta_keywords, adddate, status)".
				" VALUES (".$CategoryID.", '".TrimText($_POST['txtName'],1)."', ".$_POST["cboParent"].", '".$Photo."',".
				" '".TrimText($_POST['txtTitle'],1)."', '".TrimText($_POST['txtKeywords'],1)."',".
				" '".TrimText($_POST['txtDesc'],1)."', NOW(), ".$_POST['cboStatus'].")";
		}
		else
		{
			$Add = false;
			$Query = "UPDATE product_category SET ";
			$Query .= "".
				"  name      			= '".TrimText($_POST['txtName'],1)."'".
				", parent_id       		=  ".$_POST["cboParent"].
				", meta_title       	= '".TrimText($_POST['txtTitle'],1)."'".
				", meta_desc        	= '".TrimText($_POST['txtDesc'],1)."'".
				", meta_keywords 		= '".TrimText($_POST['txtKeywords'],1)."'";
			if ($PhotoUpload)
			{
			$Query .= "".
				", photo      			= '".$Photo."'";
			}
			$Query .= "".
				", lastedit 			=  NOW()".
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