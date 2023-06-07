<?php
	$PageID = array(11,0,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_REQUEST['cboType']))
		$cboType = $_REQUEST['cboType'];
	else
		$cboType = 0;


	// if (isset($_REQUEST['cboType']))
	// 	$cboTempType = $_REQUEST['cboType'];
	// else
	// 	$cboTempType = 0;

	if (!isset($_REQUEST['DataID']))
	{
		CheckRight("Add","Redirect");
		$PageTitle = "Add Data";
		$DataID    = 0;
		$BtnSave   = "Save Data";
	}
	else
	{
		CheckRight("View","Redirect");
		$PageTitle = "Edit Data";
		$DataID    = $_REQUEST['DataID'];
		$BtnSave   = "Update Data";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/croppie/croppie.css">
</head>
<body>
<div class="wrapper">
	<div class="content-wrapper" style="margin-left: 0px;">
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
						$Message = "Data Added Successfully ...";
						break;
					case 2:
						$Message = "Data Updated Successfully ...";
						break;
					case 101:
					case 102:
						$Message = "Unable To ".($_REQUEST['Err'] == 101 ? "Add" : "Edit")." Data - Fatal Error ...";
						if (isset($_SESSION["MysqlErr"]))
						{
							$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
							unset($_SESSION["MysqlErr"]);
						}
						break;
					case 103:
						$Message = "Same Data Already Exist ...";
						break;
					case 104:
						$Message = "Same Data Name Already Exist ...";
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
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<?php
						$txtTitle = $txtHead = $txtText = $txtIcon = $txtButton = $txtLink = "" ;
						$cboStatus = -1;
						$txtSorting = 0;
						$ImgPath = "";
						$Query = "SELECT WD.dataid, WD.datatitle, WD.datahead, WD.datatext,".
							" WD.dataicon, WD.dataimg, WD.databutton, WD.datalink, WD.datatype,".
							" WD.datatype, WD.status, WDT.datatypename, WD.sorting".
							" FROM webdata WD ".
							" INNER JOIN webdatatype WDT ON WD.datatype = WDT.datatypeid ".
							" WHERE dataid = ".$DataID;
							// echo("<br><br>".$Query); die;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow     = mysqli_fetch_object($rstRow);
							$txtTitle   = $objRow->datatitle;
							$txtHead    = $objRow->datahead;
							$txtText    = $objRow->datatext;
							$txtIcon    = $objRow->dataicon;
							$txtButton  = $objRow->databutton;
							$txtLink    = $objRow->datalink;
							$cboStatus  = $objRow->status;
							$cboType    = $objRow->datatype;
							$txtSorting = $objRow->sorting;
							if ($cboType == 1)
							{
								$ImgPath    = $PagePath."../images/banner/".$DataID.".jpg";
							}
							// elseif ($cboType == 8)
							// {
							// 	$ImgPath    = $PagePath."../images/team/".$DataID.".jpg";
							// }
							// else
							// {
							// 	$ImgPath    = $PagePath."../images/webdata-img/".$DataID.".jpg";
							// }
							if (!file_exists($ImgPath))
							{
								$ImgPath = "";
							}
						}
						// else
						// {
						// 	$txtTitle = $txtHead = $txtText = $txtIcon = $txtButton = $txtLink = "" ;
						// 	$cboStatus = -1;
						// 	$cboType = $txtSorting = 0;
						// 	if ($cboType == 1)
						// 	{
						// 		$ImgPath    = $PagePath."../images/banner/".$DataID.".jpg";
						// 	}
						// 	if (!file_exists($ImgPath))
						// 	{
						// 		$ImgPath = "";
						// 	}
						// }
					?>
					<form id="Form" name="Form" role="form" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label>Data Type :</label>
												<?php
													// $Disabled = "";
													// if ($DataID >= 1 && $DataID <= 44)
													// if (isset($_REQUEST['cboType']))
													// {
														$Disabled = "disabled";
													// }
													// DBCombo("cboType","webdatatype","datatypeid","datatypename","",$cboType,$cboType,"form-control select2","onchange=\"ShowFields();\" style=\"width: 100%;\"".$Disabled);
													DBCombo("cboType","webdatatype","datatypeid","datatypename","",$cboType,$cboType,"form-control select2","onchange=\"ShowFields();\" style=\"width: 100%;\"".$Disabled);
													// DBCombo($CboName,$Table,$ValueField,$DisplayField,$Condition,$Selected,$DisplayText,"form-control select2","onchange=\"SubmitForm();\" style=\"width: 100%;\"");
												?>
											</div>
										</div>
									</div>
									<div class="form-group" id="DivTitle" style="display: none;">
										<label>Data Title :</label>
										<input type="text" name="txtTitle" id="txtTitle" value="<?php echo($txtTitle);?>" class="form-control">
									</div>
									<div class="form-group" id="DivHead" style="display: none;">
										<label>Data Head :</label>
										<textarea name="txtHead" id="txtHead"  class="form-control" rows="4"><?php echo($txtHead);?></textarea>
									</div>
									<div class="form-group" id="DivText" style="display: none;">
										<label>Data Text :</label>
										<textarea name="txtText" id="txtText"  class="form-control" rows="8"><?php echo($txtText);?></textarea>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group" id="DivStatus" style="display: none;">
												<?php
													$ComboData = array();
													$ComboData[-1] = "--- Select Status ---";
													$ComboData[0] = "Disable";
													$ComboData[1] = "Enable";
												?>
												<label>Status :</label>
												<?php
													DBComboArray("cboStatus",$ComboData,-1,$cboStatus,"form-control select2","style=\"width: 100%;\"","");
												?>
											</div>
										</div>
										<div class="col-md-6" id="DivSorting" style="display: none;">
											<label>Sorting</label>
											<input type="input" name="txtSorting" id="txtSorting" value="<?php echo($txtSorting);?>" class="form-control" >
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group" id="DivIcon" style="display: none;">
										<label>Data Icon :</label>
										<input type="text" name="txtIcon" id="txtIcon" value="<?php echo($txtIcon);?>" class="form-control">
									</div>
									<div class="form-group" id="DivButtonText" style="display: none;">
										<label>Data Button Text :</label>
										<input type="text" name="txtButton" id="txtButton" value="<?php echo($txtButton);?>" class="form-control">
									</div>
									<div class="form-group" id="DivButtonLink" style="display: none;">
										<label>Data Button Link :</label>
										<input type="text" name="txtLink" id="txtLink" value="<?php echo($txtLink);?>" class="form-control">
									</div>
									<div class="col-md-12" id="DivPhoto" style="display: none;">
										<div class="row">
											<div class="col-md-6">
												<?php
													if ($ImgPath != "")
													{
												?>
												<div class="form-group">
													<img src="<?php echo($ImgPath);?>" id="txtPhotoImg" name="txtPhotoImg" width="250px">
												</div>
												<?php
													}
												?>
												<div class="form-group" id="CropImg" <?php if ($cboType != 1) echo("style=\"display:none;\"");?>>
													<div class="upload-img mt-3">
														<div class="change-photo-btn">
															<span><i class="fa fa-upload"></i> Upload Photo</span>
															<input type="file" name="txtPhoto"  id="txtPhoto" class="upload" >
															<input type="hidden" name="CroppedImg"  id="CroppedImg" value="<?php echo($cboType); ?>">
														</div>
													</div>
												</div>
												<div class="form-group" id="DataImg">
													<label for="txtImgTh">Upload Image:</label>
													<input type="file" name="txtImg" id="txtImg" accept="image/*" class="form-control">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer" id="DivSaveButton" style="display: none;">
							<input type="hidden" name="DataID" value="<?php echo($DataID);?>">
							<button type="submit" name="btnSave" class="btn btn-primary"><?php echo($BtnSave);?></button>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
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
<script src="<?php echo($PagePath);?>plugins/croppie/croppie.js"></script>
<script>
	$(function () {
		//init Select2
		$(".select2").select2();
		HideAll();
		ShowFields(<?php echo($cboType);?>);
		var cboType = parseInt(document.Form.cboType.value);
		if (cboType == 0)
		{
			cboType = parseInt(document.Form.CroppedImg.value);
		}
		var DataID  	= <?php echo($DataID);?>;
		var isMobile   	= /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
		var Browser    	= CheckBrowser();
		var BrowserVer 	= CheckBrowser(1);
		if (cboType == 1)
		{
			var ImgW  = 750;
			var ImgH  = 300;
		}
		// else if (cboType == 8)
		// {
		// 	var ImgW  = 400;
		// 	var ImgH  = 400;
		// }
		if (cboType == 1)
		{
			var ImgBW = 1000;
			var ImgBH = 400;
		}
		// else if (cboType == 8)
		// {
		// 	var ImgBW = 400;
		// 	var ImgBH = 400;
		// }
		var Orientation = 1;
		if (Browser == "Safari" && BrowserVer ==  12.1)
		{
			Orientation = 6;
		}
		if (isMobile == true)
		{
			var ImgW  = 350;
			var ImgH  = 170;
			var ImgBW = 300;
			var ImgBH = 350;
		}
		ImageCropper = $('#imgCropper').croppie({
			enableExif: true,
			enableOrientation: true,
			original: {
				width:ImgW,
				height:ImgH,
				type:'rectangle'
			},
			viewport: {
				width:ImgW,
				height:ImgH,
				type:'rectangle'
			},
			boundary:{
				width:ImgBW,
				height:ImgBH
			}
		});
		$('#txtPhoto').on('change', function() {
			var RdrFile = new FileReader();
			RdrFile.onload = function (e) {
				ImageCropper.croppie('bind', {
					url: e.target.result
				});
			}
			RdrFile.readAsDataURL(this.files[0]);
			$('#Modal-Crop-Image').modal('show');
		});
		$("#btnUploadImage").click( function(event) {
			var cboType = document.Form.cboType.value;
			if (cboType == 1)
			{
				var Width  = 1500;
				var Height = 600;
			}
			// else if (cboType == 8)
			// {
			// 	var Width  = 400;
			// 	var Height = 400;
			// }
			ImageCropper.croppie('result', {
				type: 'canvas',
				size: {
					width  : Width,
					height : Height
				}
				}).then(function(response){
					document.Form.CroppedImg.value =  response;
			});
			$('#Modal-Crop-Image').modal('hide');
		});
	});
	function ShowFields(cboType)
	{
		var cboType = cboType;
		HideAll();
		if (cboType == 1)
		{
			$("#DivHead").show();
			$("#DivIcon").show();
			$("#DivText").show();
			$("#DivPhoto").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 2)
		{
			$("#DivHead").show();
			$("#DivIcon").show();
			$("#DivText").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 3)
		{
			$("#DivHead").show();
			$("#DivText").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 4)
		{
			$("#DivHead").show();
			$("#DivText").show();
			// $("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 5)
		{
			$("#DivHead").show();
			$("#DivText").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 6)
		{
			$("#DivHead").show();
			$("#DivText").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 7)
		{
			$("#DivHead").show();
			$("#DivText").show();
			$("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 8)
		{
			$("#DivHead").show();
			$("#DivIcon").show();
			$("#DivText").show();
			$("#DivPhoto").show();
			// $("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			$("#DivSorting").show();
		}
		else if (cboType == 10)
		{
			$("#DivHead").show();
			$("#DivText").show();
			$("#DivTitle").show();
			// $("#CropImg").show();
			$("#DivStatus").show();
			$("#DivSaveButton").show();
			// $("#DivSorting").show();
		}
		// else if (cboType == 11)
		// {
		// 	$("#DivHead").show();
		// 	$("#DivText").show();
		// 	$("#CropImg").show();
		// 	$("#DivStatus").show();
		// 	$("#DivSaveButton").show();
		// 	$("#DivSorting").show();
		// }
	}
	$("#Form").submit(function(evt) {
		evt.preventDefault();
		<?php CheckRight("Edit","ShowError");?>
		if (document.Form.cboType.value == 0)
		{
			ShowError(true,"Error!","Please Select Data Type",undefined,"cboType");
			return(false);
		}
		if (document.Form.cboStatus.value < 0)
		{
			ShowError(true,"Error!","Please Select Data Status",undefined,"cboStatus");
			return(false);
		}
		if (document.Form.cboType.value == 1 && IsEmpty(document.Form.txtPhoto.value) && (<?php echo($DataID);?> == 0))
		{
			ShowError(true,"Error!","Please Upload Slider Image",undefined,undefined);
			return(false);
		}
		document.Form.cboType.disabled = false;
		var FrmData = new FormData(document.Form);
		var Result = "";
		var CarID = 0;
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: "col-md-8 col-md-offset-2",
			content: function () {
				var self = this;
				return $.ajax({
					url: "../ajaxs/admin/setting/webdata-save",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					async: false,
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
						Result  = response.Status;
						DataID = response.DataID;
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function(jqXHR,exception){
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ..."+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Added")
						{
							window.location.href = "webdata-add?DataID="+DataID;
						}
					}
				}
			},
			onClose: function () {
			}
		});
	});
	function HideAll()
	{
		$("#DivTitle").hide();
		$("#DivHead").hide();
		$("#DivText").hide();
		$("#DivStatus").hide();
		$("#DivPhoto").hide();
		$("#CropImg").hide();
		$("#DataImg").hide();
		$("#DivIcon").hide();
		$("#DivButtonText").hide();
		$("#DivButtonLink").hide();
		$("#DivSaveButton").hide();
		$("#DivSorting").hide();
	}

	/*function InitializeCropper(cboType)
	{
		var isMobile   	= /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
		var Browser    	= CheckBrowser();
		var BrowserVer 	= CheckBrowser(1);
		if (cboType == 1)
		{
			var ImgW  = 850;
			var ImgH  = 440;
		}
		else if (cboType == 8)
		{
			var ImgW  = 400;
			var ImgH  = 400;
		}
		if (cboType == 1)
		{
			var ImgBW = 1000;
			var ImgBH = 600;
		}
		else if (cboType == 8)
		{
			var ImgBW = 400;
			var ImgBH = 400;
		}
		var Orientation = 1;
		if (Browser == "Safari" && BrowserVer ==  12.1)
		{
			Orientation = 6;
		}
		if (isMobile == true)
		{
			var ImgW  = 350;
			var ImgH  = 170;
			var ImgBW = 300;
			var ImgBH = 350;
		}
		return Array.from(ImgW, ImgH, ImgBW, ImgBH);
	}*/
</script>
</body>
</html>