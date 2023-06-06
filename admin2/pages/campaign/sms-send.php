<?php
	$PageID = array(3,1,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/packages.php");
	include($PagePath."lib/combos.php");

	$DefCountry = GetClientCountryCode();
	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
		$txtStartDate = date("Y-m-d H:i:s");
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d H:i:s");
	
	if (isset($_REQUEST['cboDocType']))
		$cboDocType = $_REQUEST['cboDocType'];
	else
		$cboDocType = 0;

	/*
	if(isset($_POST["btnSavePhoto"]))
	{
		$Directory_Name = $Img_Name = $Tmp_Name = $File_Name = "";
		$Pic_Name = array();
		// $ImgType ( 0 = Profile Photo, 1 = Document Front Side photo, 2 = Document Back Side photo )
		$Arr_Img  = array($_FILES["txtProPhoto"],$_FILES['txtDocFSPhoto'],$_FILES['txtDocBSPhoto']);
		for ($i = 0; $i < count($Arr_Img); $i++)
		{
			if (isset($Arr_Img[$i]['name']))
			{
				$Directory_Name = $PagePath.'../upload/documents/';     //folder where image will upload
				$Tmp_Name  = $Arr_Img[$i]['tmp_name'];
				$Img_Name  = strtolower($_SESSION[SessionID."ClientID"]."-".$i);
				$File_Name = $Directory_Name.$Img_Name;
				array_push($Pic_Name, $Img_Name);
			}
			move_uploaded_file($Tmp_Name, $File_Name);
		}
		$Query = "INSERT INTO clientsimdocdetails(clientid, doctype, profilephoto, docfsphoto, docbsphoto, adddate, status)".
			" VALUES(".$_SESSION[SessionID."ClientID"].", ".$cboDocType.", '".$Pic_Name[0]."', '".$Pic_Name[1]."', '".$Pic_Name[2]."', NOW(), 0)";
		mysqli_query($Conn,$Query);
		$Url = "sms-send?Err=1";
		header("Location: ".$Url);
		exit;
	}
	*/		

	CheckRight("Add");
	$PageTitle = "Create a Message";
	$BtnSave = "Send";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>../plugins/intl-mobile/css/intlTelInput.css">
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Top Menu -->
	<?php
		include($PagePath."includes/header.php");
	?>
	<!-- Left Menu -->
	<?php
		include($PagePath."includes/left.php");
	?>
	<!-- Page Content -->
	<div class="content-wrapper">
		<!-- Page Header  Breadcrumb-->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageTitle);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Campaigns</li>
							<li class="breadcrumb-item active"><?php echo($PageTitle);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
				<!-- Page Error -->
					<!-- <?php
						if (isset($_REQUEST['Err']))
						{
							$Message = ""; 
							$MessageBG = "danger";
							$MessageHead = "Error:";
							$MessageIcon = "fa-exclamation-triangle";
							switch ($_REQUEST['Err'])
							{
								case 1:
									$Message = "Your document Images Added Successfully<br> Our technical team verify your document and get back to your with 2 working days...";
									break;
								case 2:
									$Message = "Image Updated Successfully ...";
									break;
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
					<div style="padding: 15px;">
						<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
							<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
							<?php echo($Message);?>
						</div>
					</div>
					<?php
						}
						$GetClientPkg = GetValue("clientid","client","pkgtype = 7");
						$SimDocStatus = GetValue("status","clientmisscallalert","clientid =".$_SESSION[SessionID."ClientID"]);
						// echo $SimDocStatus;
						// die();
						if ($GetClientPkg == 7 && $SimDocStatus == 0)
						{
					?>
					<form name="FrmImg" id="FrmImg" action="" method="post" role="form" autocomplete="off" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div id="RowMobile" class="form-group">
										<label>Document Type (*)</label><br>
										<?php
											$ComboData = array();
											$ComboData[0] = "--- Select Document Type ---";
											$ComboData[1] = "CNIC";
											$ComboData[2] = "Passport";
											$ComboData[3] = "Driving licience";
											DBComboArray("cboDocType",$ComboData,0,$cboDocType,"form-control select2","onchange=\"ChangeCampEnd();\"");
										?>
									</div>
									<div id="RowMobile" class="form-group">
										<label>Your Image (*)</label><br>
										<input type="file" name="txtProPhoto" id="txtProPhoto" class="form-control" accept=".png,.jpg,.jpeg">
										<input type="hidden" name="CroppedProImg" id="CroppedProImg" value="">
									</div>
									<div id="RowMobile" class="form-group">
										<label>Document Front Side Image(*)</label><br>
										<input type="file" name="txtDocFSPhoto" id="txtDocFSPhoto"  class="form-control" accept=".png,.jpg,.jpeg">
										<input type="hidden" name="CroppedDocFSImg" id="CroppedDocFSImg" value="">
									</div>
									<div id="RowMobile" class="form-group">
										<label>Document Back Side Image (*)</label><br>
										<input type="file" name="txtDocBSPhoto" id="txtDocBSPhoto"  class="form-control" accept=".png,.jpg,.jpeg">
										<input type="hidden" name="CroppedDocBSImg" id="CroppedDocBSImg" value="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit" name="btnSavePhoto" class="btn btn-primary" onclick="return Verify();">Save Images</button>
								</div>
							</div>
						</div>			
					</form>
					<?php
						}
					?> -->
					<form name="Form" id="Form" action="" method="post" role="form" autocomplete="off" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Send From Mobile :</label>
										<?php
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Mobile --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
									<div id="RowMobile" class="form-group">
										<label>Send To (*)</label><br>
										<input type="text" name="txtMobileNo" id="txtMobileNo" class="form-control" maxlength="20" style="text-indent: 5px;">
									</div>
									<div id="RowMessage" class="form-group">
										<label>Message (*)</label>
										<textarea name="txtMessage" id="txtMessage" rows="5" class="form-control"></textarea>
									</div>
									<div class="form-group">
										<label>Send Time :</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-clock-o"></i>&nbsp;&nbsp;
												</span>
											</div>
											<input type="text" name="cboDate" id="cboDate" readonly class="form-control pull-right" style="background-color:#FFFFFF;">
											<input type="hidden" name="txtStartDate" value="<?php echo($txtStartDate);?>">
											<input type="hidden" name="txtCloseDate" value="<?php echo($txtCloseDate);?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit" name="btnSave" class="btn btn-primary"><?php echo($BtnSave);?></button>
								</div>
							</div>		
						</div>
						<!-- <div class="box-footer">
						</div> -->
					</form>
				</div>
			</div>
		</section>
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<script src="<?php echo($PagePath);?>../plugins/intl-mobile/js/intlTelInput.js"></script>
<!-- Page Script -->
<script>
	var iti;
	$(function() {
		//Init Select2
		$(".select2").select2();
		//Init Mobile
		var input = document.querySelector("#txtMobileNo");
		iti = window.intlTelInput(input, {
			formatOnDisplay: false,
			initialCountry: "<?php echo($DefCountry);?>",
			placeholderNumberType: "MOBILE",
			utilsScript: "<?php echo($PagePath);?>../plugins/intl-mobile/js/utils.js",
		});
	});
	$("#Form").submit(function(evt) {
		evt.preventDefault();
		if (document.Form.cboDevice.value == 0)
		{
			ShowError(true,"Error!","Please Select Send From Mobile",undefined,"txtMobileNo");
			return(false);
		}
		if (iti.isValidNumber() == false)
		{
			ShowError(true,"Error!","Please Enter Valid Mobile #<br><br>"+itiErrorMap[iti.getValidationError()],undefined,"txtMobileNo");
			return(false);
		}
		if (iti.getNumberType() != 1 && iti.getNumberType() != 2)
		{
			ShowError(true,"Error!","Please Enter Your Valid Mobile #<br><br>Entered Number is Not Mobile # [ "+iti.getNumberType()+" ]",undefined,"txtMobile");
			return(false);
		}
		if (IsEmpty(document.Form.txtMessage.value) == true)
		{
			ShowError(true,"Error!","Please Enter Text Message","RowMessage","txtMessage");
			return(false);
		}
		var Result = "";
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			cache: false,
			columnClass: "col-md-8 col-md-offset-2",
			content: function () {
				var self = this;
				return $.ajax({
					url: "<?php echo($PagePath);?>pages/ajaxs/sms-chat-send",
					type: "POST",
					dataType: "JSON",
					data: {
						"ClientMobID": $("#cboDevice").val(),
						"Mobile": iti.getNumber(),
						"Message": $("#txtMessage").val()
					},
					}).done(function (response) {
						if (response.Status == "Done")
						{
							self.buttons.UpgradePkg.hide();	
							self.setTitle("SMS Sent");
							self.setContent("SMS Sent Successfully");
							$("#txtMessage").val("");
						}
						else if (response.Status == "Message")
						{
							self.buttons.OK.hide();
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}
						else if (response.Status == "Error")
						{
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}
					}).fail(function(jqXHR,exception) {
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
					}
				},
				"UpgradePkg": {
					text: "Upgrade Package",
					btnClass: "btn-success",
					action: function() {
						window.location.href = "<?php echo(WebsiteUrl);?>/admin/pages/clientuser/new-package-details?Tab=Shared"; 
					}
				}
			},
			onClose: function () {
			}
		});
	});

	// function Verify()
	// {
	// 	if (document.FrmImg.cboDocType.value == 0)
	// 	{
	// 		ShowError(true,"Error!","Please Select Document Type First",undefined,"txtMobileNo");
	// 		return(false);
	// 	}
	// 	if (IsEmpty(document.FrmImg.txtProPhoto.value) == true)
	// 	{
	// 		ShowError(true,"Error!","Please Enter Your Personal Photo For Dedicated Sim Verification",undefined,"txtProPhoto");
	// 		return(false);
	// 	}
	// 	if (IsEmpty(document.FrmImg.txtDocFSPhoto.value) == true)
	// 	{
	// 		ShowError(true,"Error!","Please Upload Your Document Front Side Photo For Dedicated Sim Verification",undefined,"txtDocFSPhoto");
	// 		return(false);
	// 	}
	// 	if (IsEmpty(document.FrmImg.txtDocBSPhoto.value) == true)
	// 	{
	// 		ShowError(true,"Error!","Please Upload Your Document Back Side Photo For Dedicated Sim Verification",undefined,"txtDocBSPhoto");
	// 		return(false);
	// 	}
	// }
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = true;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "up";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>