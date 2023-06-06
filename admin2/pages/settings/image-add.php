<?php
	$PageID = array(5,4,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if(isset($_REQUEST['ImgID']))
	{
		$ImgID 	   = $_REQUEST['ImgID'];
		$PageTitle = "Edit Image";
		$BtnSave   = "Update Image";
	}
	else
	{
		$ImgID 	   = 0;
		$PageTitle = "Add Image";
		$BtnSave   = "Add Image";
	}
	if (isset($_POST["btnSave"]))
	{
		$Add = false;
		$ErrPos = 0;
		if ($ImgID == 0)
		{
			if (is_uploaded_file($_FILES["txtImage"]["tmp_name"]))
			{
			    $Directory_Name = $PagePath.'../images/webdata-img/';     //folder where image will upload
			    $Tmp_Name 	= $_FILES['txtImage']['tmp_name'];
				$Image_Name = $_FILES['txtImage']['name'];
			    $File_Name 	= $Directory_Name.$Image_Name;
			    move_uploaded_file($Tmp_Name, $File_Name);

			    $Image_Size = $_FILES['txtImage']['size'];
			    $Image_Size = round($Image_Size / 1024, 2);
			    list($width, $height) = getimagesize($File_Name);
			    $Image_Dim	= $width."x".$height;
			}
			$MaxID = GetMax("webimage","imageid");
			$Query = "INSERT INTO webimage (imageid, imagename, imagedim, imagesize, adddate, status)".
				" VALUES(".$MaxID.", '".$Image_Name."', '".$Image_Dim."', ".$Image_Size.", NOW(), 1)";
		}
		else
		{
			$Image_Name = GetValue("imagename","webimage","imageid =".$ImgID);
			if (is_uploaded_file($_FILES["txtImage"]["tmp_name"]))
			{
			    $Directory_Name = $PagePath.'../images/webdata-img/';     //folder where image will upload
				unlink($Directory_Name.$Image_Name); 	//Deleting Old Photo

			    $Tmp_Name 	= $_FILES['txtImage']['tmp_name'];
				$Image_Name = $_FILES['txtImage']['name'];
			    $File_Name 	= $Directory_Name.$Image_Name;
			    move_uploaded_file($Tmp_Name, $File_Name);

			    $Image_Size = $_FILES['txtImage']['size'];
			    $Image_Size = round($Image_Size / 1024, 2);
			    list($width, $height) = getimagesize($File_Name);
			    $Image_Dim	= $width."x".$height;
			}
			$Query = "UPDATE webimage SET".
				"  imagename 	 = '".$Image_Name."'".
				", imagedim 	 = '".$Image_Dim."'".
				", imagesize 	 = ".$Image_Size."".
				", lastedit 	 = NOW()".
				", status 		 =	1".
				"  WHERE imageid = ".$ImgID;
		}
		if (!mysqli_query($Conn,$Query))
		{
			if ($Add == true)
				$Err = 101;
			else
				$Err = 102;
			$ErrPos = 1;
			$_SESSION['MysqlErr'] = mysqli_error($Conn);
		}
		else
		{
			if ($Add == true)
				$Err = 1;
			else
				$Err = 2;
		}
		$Url = "image-add?Err=".$Err;
		if ($Add == false)
		{
			$Url .= "&ImgID=".$ImgID;
		}
		if ($ErrPos > 0)
		{
			$Url .= "&ErrPos=".$ErrPos;
		}
		else
		{
			unset($_SESSION['PageVars']);
		}
		header("Location: ".$Url);
		exit; 
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
</head>
<body>
	<div class="wrapper">
		<!-- Page Content -->
		<div class="content-wrapper" style="margin-left: 0px;">
			<!-- Page Header -->
			<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1><?php echo($PageTitle)?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
							<li class="breadcrumb-item active"><?php echo($PageTitle)?></li>
						</ol>
					</div>
				</div>
			</div>
		</section>
			<!-- Page Error -->
			<?php
				if (isset($_REQUEST['Err']))
				{
					$Message = ""; 
					$MessageBG = "danger";
					$MessageHead = "Error:";
					$MessageIcon = "fa-exclamation-triangle";
					switch ($_REQUEST['Err'])
					{
						case 1:
							$Message = "Image Added Successfully ...";
							break;
						case 2:
							$Message = "Image Updated Successfully ...";
							break;
						case 101:
						case 102:
							$Message = "Unable To ".($_REQUEST['Err'] == 101 ? "Add" : "Edit")." Slide - Fatal Error ...";
							if (isset($_SESSION["MysqlErr"]))
							{
								$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
								unset($_SESSION["MysqlErr"]);
							}
							break;
						case 103:
							$Message = "Same Image Already Exist ...";
							break;
						case 104:
							$Message = "Same Image Name Already Exist ...";
							break;
					}
					if ($_REQUEST['Err'] < 100)
					{
						$MessageHead = "Note:";
						$MessageBG   = "success";
						$MessageIcon = "fa-check";
					}
			?>
			<div style="padding-left: 15px; padding-right: 15px;">
				<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
					<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
					<?php echo($Message);?>
				</div>
			</div>
			<?php
				}
			?>
			<!-- Main Content -->
			<section class="content">
				<div class="container-fluid">
					<div class="card card-outline card-primary">
					<?php
						$ImgName = "";
						$cboStatus = 1;
						$Query = "SELECT imagename FROM webimage".
							" WHERE imageid = ".$ImgID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow = mysqli_fetch_object($rstRow);
							$ImgName   = $objRow->imagename;
						}
					?>
					<form name="Form" role="form" action="image-add" method="post" onsubmit="return Verify();" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6" align="center">
									<?php
										if ($ImgName > 0) 
										{
									?>
									<div class="form-group">
										<img src="<?php echo($PagePath);?>../images/webimage/<?php echo($ImgName);?>" class="img-fluid">
									</div>
									<?php
										}
									?>
									<div class="form-group">
										<div>
											<label for="txtImage">Upload Image :<br></label>
											<input type="file" id="txtImage" name="txtImage"><br>
										</div>
										<div class="pl-5" style="text-align:left;">
											<small>Image Size Must Be 1886 x 585</small>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<?php
								if ($ImgName > 0)
								{
							?>
							<input type="hidden" name="ImgID" value="<?php echo($ImgID);?>">
							<?php
								}
							?>
							<button type="submit" name="btnSave" class="btn btn-primary"><?php echo($BtnSave);?></button>
						</div>
					</form>
				</div>
			</section>
		</div>
	</div>
	<?php
		include($PagePath."includes/inc-js.php");
	?>
	<script>
		$(function () {
			$(".select2").select2();
		});
		function Verify()
		{
			if (document.Form.txtImage.value == "")
			{
				ShowError(true,"Error!","Please Select Image File To Upload ...",undefined,"txtImage");
				return(false);
			}
		}
	</script>
</body>
</html>