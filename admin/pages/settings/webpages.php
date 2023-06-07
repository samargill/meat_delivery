<?php
	$PageID = array(11,3,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	$PageName = "Web Pages";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	CheckRight("View","Redirect");

	if (isset($_REQUEST['cboPage']))
		$cboPage = $_REQUEST['cboPage'];
	else
		$cboPage = 0;
	if (isset($_REQUEST["btnSave"]))
	{
		// if (isset($_REQUEST["CroppedImg"]))
		// {
		// 	$MyFile = $PagePath."../images/banner/".$cboPage.".jpg";
		// 	$PhotoData = $_POST['CroppedImg'];
		// 	if ($PhotoData != "")
		// 	{
		// 		$ImgAry    = explode(";",$PhotoData);
		// 		$ImgAry    = explode(",",$ImgAry[1]);
		// 		$PhotoData = base64_decode($ImgAry[1]);
		// 		file_put_contents($MyFile,$PhotoData);
		// 	}
		// }
		$cboPage = explode("-",$cboPage);
		$MenuID = $cboPage[0];	
		$Query = "UPDATE webmenu SET".
			"  menuname       = '".TrimText($_REQUEST["txtTitle"],1)."'".
			", menulink       = '".TrimText($_REQUEST["txtLink"],1)."'".
			", seotitle       = '".TrimText($_REQUEST["txtSEOTitle"],1)."'".
			", seokeyword     = '".TrimText($_REQUEST["txtMetaKey"],1)."'".
			", seodescription = '".TrimText($_REQUEST["txtMetaDesc"],1)."'".
			", pagetext       = '".TrimText($_REQUEST["txtPageText"],1)."'".
			"  WHERE menuid = ".$MenuID;
		@mysqli_query($Conn,$Query);
		if (count(mysqli_error_list($Conn)) > 0)
		{
			$_SESSION["MysqlErr"] = mysqli_error($Conn);
			echo($_SESSION["MysqlErr"]); die();
			header("Location: webpages?Err=101&cboPage=".$MenuID);
			exit;
		}
		header("Location: webpages?Err=2&cboPage=".$MenuID);
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">
		function SubmitForm()
		{
			document.Form.submit();
		}
		function Verify()
		{
			<?php CheckRight("Edit","ShowError");?>
			if (IsEmpty(document.Form.txtTitle.value) == true)
			{
				ShowError(true,"Error!","Please Enter Valid Page Heading ...",undefined,"txtTitle");
				return(false);
			}
			if (IsEmpty(document.Form.txtLink.value) == true)
			{
				ShowError(true,"Error!","Please Enter Valid Page Link ...",undefined,"txtLink");
				return(false);
			}
			if (IsEmpty(document.Form.txtSEOTitle.value) == true || document.Form.txtSEOTitle.value.length > 60) 
			{
				ShowError(true,"Error!","Please Enter Valid SEO Title Max 60 Char",undefined,"txtSEOTitle");
				return(false);
			}
			if (IsEmpty(document.Form.txtMetaKey.value) == true || document.Form.txtMetaKey.value.length > 160) 
			{
				ShowError(true,"Error!","Please Enter Valid Meta Keywords Max 160 Char",undefined,"txtMetaKey");
				return(false);
			}
			if (IsEmpty(document.Form.txtMetaDesc.value) == true || document.Form.txtMetaDesc.value.length > 160) 
			{
				ShowError(true,"Error!","Please Enter Valid Meta Description Max 160 Char",undefined,"txtMetaDesc");
				return(false);
			}
			/*if (document.Form.cboPage.value != 1)
			{
				if (IsEmpty(document.Form.txtPageText.value) == true) 
				{
					ShowError(true,"Error!","Please Enter Page Text",undefined,"txtPageText");
					return(false);
				}
			}*/
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Top Header -->
	<?php
		include($PagePath."includes/header.php");
	?>
	<!-- Left Menu -->
	<?php
		include($PagePath."includes/left.php");
	?>
	<!-- Page Content -->
	<div class="content-wrapper">
		<!-- Page Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1><?php echo($PageName)?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
							<li class="breadcrumb-item active"><?php echo($PageName)?></li>
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
					case 2:
						$Message = "Web Page Updated Successfully ...";
						break;
					case 101:
						$Message = "Unable To Update Web Page - Fatal Error ...";
						if (isset($_SESSION["MysqlErr"]))
						{
							$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
							unset($_SESSION["MysqlErr"]);
						}
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
					<form name="Form" role="form" action="webpages" method="post" enctype="multipart/form-data" >
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$Query = "SELECT menuid, menuname FROM webmenu".
												" WHERE menustatus = 1 ORDER BY menuid";
											$rstRow = mysqli_query($Conn,$Query);
										?>
										<label>Select Web Page To Edit :</label>
										<select name="cboPage" id="cboPage" onchange="SubmitForm();" CLASS="form-control select2" style="width: 100%;">
											<option value="0">-- Select Web Page --</option>
											<?php
												while ($objRow = mysqli_fetch_object($rstRow))
												{
													if ($cboPage == $objRow->menuid)
														$ComboSelect = "SELECTED";
													else
														$ComboSelect = "";
											?>
											<option value="<?php echo($objRow->menuid);?>" <?php echo($ComboSelect);?>><?php echo($objRow->menuname);?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<!-- <div class="col-md-4 form-group p-3" id="CropImg" <?php //if ($cboPage == 0) echo("style=\"display:none;\"");?>>
									<div class="upload-img mt-3">
										<div class="change-photo-btn">
											<span><i class="fa fa-upload"></i> Upload Photo</span>
											<small>Image Dimensions are 1920 x 540.</small>
											<input type="file" name="txtPhoto"  id="txtPhoto" class="upload" >
											<input type="hidden" name="CroppedImg"  id="CroppedImg" value="">
										</div>
									</div>
								</div>
								<?php
									//if (file_exists($PagePath."../images/banner/".$cboPage.".jpg"))
									{
								?>
								<div class="col-md-4 mt-2" <?php //if ($cboPage == 0) echo("style=\"display:none\"");?> >
									<img src="<?php //echo($PagePath);?>../images/banner/<?php //echo($cboPage);?>.jpg" style="width: 300px;">
								</div>
								<?php
									}
								?> -->
							</div>
							<?php
								if ($cboPage > 0)
								{
							?>
							<div class="row">
								<div class="col-md-12">
									<?php
										$cboPage = explode("-",$cboPage);
										$MenuID = $cboPage[0];
										$Query = "SELECT menuname, menulink, in_header, in_footer,".
											" seotitle, seokeyword, seodescription, pagetext, menustatus".
											" FROM webmenu WHERE menuid = ".$MenuID;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);

									?>
									<div class="form-group">
										<label>Page Heading</label>
										<input type="text" name="txtTitle" id="txtTitle" value="<?php echo($objRow->menuname);?>" class="form-control" maxlength="100">
									</div>
									<div class="form-group">
										<label>Page Link</label>
										<input type="text" name="txtLink" id="txtLink" readonly value="<?php echo($objRow->menulink);?>" class="form-control" maxlength="100">
									</div>
									<div class="form-group">
										<label>SEO Title [60 Char]</label>
										<input type="text" name="txtSEOTitle" id="txtSEOTitle" value="<?php echo($objRow->seotitle);?>" class="form-control" maxlength="60">
										<input type="text" name="txtSEOTitleCount" id="txtSEOTitleCount" value="<?php echo(strlen($objRow->seotitle));?>" class="form-control" readonly>
									</div>
									<div class="form-group">
										<label>SEO Meta Keywords [160 Char]</label>
										<textarea name="txtMetaKey" id="txtMetaKey" class="form-control" rows="4"><?php echo($objRow->seokeyword);?></textarea>
										<input type="text" name="txtMetaKeyCount" id="txtMetaKeyCount" value="<?php echo(strlen($objRow->seokeyword));?>" class="form-control" readonly>
									</div>
									<div class="form-group">
										<label>SEO Meta Description [160 Char]</label>
										<textarea name="txtMetaDesc" id="txtMetaDesc" class="form-control" rows="4"><?php echo($objRow->seodescription);?></textarea>
										<input type="text" name="txtMetaDescCount" id="txtMetaDescCount"  value="<?php echo(strlen($objRow->seodescription));?>" class="form-control" readonly>
									</div>
									<div class="form-group">
										<label>Page Text</label>
										<textarea name="txtPageText" id="txtPageText" class="form-control" rows="8"><?php echo($objRow->pagetext);?></textarea>
									</div>
								</div>
							</div>
							<?php
								}
							?>
						</div>
						<div class="card-footer">
							<?php
								if ($cboPage > 0)
								{
							?>
							<button type="submit" name="btnSave" class="btn btn-primary" onclick="return Verify();">Save Webpage</button>
							<?php
								}
							?>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div>
<div id="Modal-Crop-Image" class="modal" role="dialog">
	<div  class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Photo</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div id="imgCropper"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" name="btnUploadImage" id="btnUploadImage" class="btn btn-primary">Crop Image</button>
			</div>
		</div>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function () {
		//Initialize Select2
		$(".select2").select2();
		// var URL = window.URL;
		// var isMobile   = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
		// var Browser    = CheckBrowser();
		// var BrowserVer = CheckBrowser(1);
		// var ImgW  = 960;
		// var ImgH  = 270;
		// var ImgBW = 1000;
		// var ImgBH = 600;
		// var Orientation = 1;
		// if (Browser == "Safari" && BrowserVer ==  12.1)
		// {
		// 	Orientation = 6;
		// }
		// if (isMobile == true)
		// {
		// 	var ImgW  = 360;
		// 	var ImgH  = 101;
		// 	var ImgBW = 300;
		// 	var ImgBH = 350;
		// }
		// ImageCropper = $('#imgCropper').croppie({
		// 	enableExif: true,
		// 	enableOrientation: true,
		// 	original: {
		// 		width:ImgW,
		// 		height:ImgH,
		// 		type:'rectangle'
		// 	},
		// 	viewport: {
		// 		width:ImgW,
		// 		height:ImgH,
		// 		type:'rectangle'
		// 	},
		// 	boundary:{
		// 		width:ImgBW,
		// 		height:ImgBH
		// 	}
		// });
		// $('#txtPhoto').on('change', function() {
		// 	var RdrFile = new FileReader();
		// 	RdrFile.onload = function (e) {
		// 		ImageCropper.croppie('bind', {
		// 			url: e.target.result
		// 		});
		// 	}
		// 	RdrFile.readAsDataURL(this.files[0]);
		// 	$('#Modal-Crop-Image').modal('show');
		// });
		// $("#btnUploadImage").click(function(event) {
		// 	ImageCropper.croppie('result', {
		// 		type: 'canvas',
		// 		size: {
		// 			width  : 1920,
		// 			height : 540
		// 		}
		// 		}).then(function(response){
		// 			document.Form.CroppedImg.value =  response;
		// 	});
		// 	$('#Modal-Crop-Image').modal('hide');
		// });
		<?php
			if ($cboPage > 0)
			{
		?>
		$('#txtPageText').val(BeautifyCode($('#txtPageText').val()));
		<?php
			}
		?>
	});
	$('#txtSEOTitle').keyup(function () {
		document.Form.txtSEOTitleCount.value = document.Form.txtSEOTitle.value.length;
	});
	$('#txtMetaKey').keyup(function () {
		document.Form.txtMetaKeyCount.value = document.Form.txtMetaKey.value.length;
	});
	$('#txtMetaDesc').keyup(function () {
		document.Form.txtMetaDescCount.value = document.Form.txtMetaDesc.value.length;
	});
</script>
</body>
</html>