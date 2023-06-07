<?php
	$PageID = array(4,1,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	include($PagePath."lib/packages.php");

	$AddDateTime = date('d-m-y h:i:s');
	if (isset($_REQUEST['SmsQueID']))
	{
		$SmsQueID = $_REQUEST['SmsQueID'];
		$PageTitle = "Edit Campaign";
	}
	else
	{
		$SmsQueID = 0;
		$PageTitle = "Add New Campaign";
	}
	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
	{
		$txtStartDate = time();
		$Mod = date("i",$txtStartDate) % 5;
		$txtStartDate = $txtStartDate + (5 - $Mod) * 60;
		$txtStartDate = date("d-m-Y H:i",$txtStartDate);
	}
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
	{
		$txtCloseDate = "";
	}
	if (isset($_REQUEST['cboCampaignEnd']))
		$cboCampaignEnd = $_REQUEST['cboCampaignEnd'];
	else
		$cboCampaignEnd = 0;
?>
<!DOCTYPE html>
<html>
<head>
<?php
	include($PagePath."includes/inc-css.php");
?>
<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/datetimepicker/bootstrap-datetimepicker.min.css">
<script language="javascript">
	function SubmitForm()
	{
		document.Form.submit();
	}
	function ChangeCampEnd()
	{
		if (document.Form.cboCampaignEnd.value == 1)
			$("#divCloseDate").removeClass('hide');
		else
			$("#divCloseDate").addClass('hide');
	}
</script>
</head>
<body>
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper" style="margin-left:0px;">
		<!-- Page Header -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageTitle);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
							<li class="breadcrumb-item active"><?php echo($PageTitle);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Page Error -->
		<?php
			$Message = "";
			$cboCampaignEnd = 0;
			$txtCampaignName = $txtStartDate = $txtCloseDate = $txtWait = "";
			$txtCloseView = "hide";
			if ($PageTitle == "Add New Campaign")
			{
				// Check Max Campaigns
				list($RunCamp,$MaxCamp) = GetRightCampaign();
				$GetPkg = GetValue("pkgtype","client","clientid = ".$_SESSION[SessionID."ClientID"]);
				if ($GetPkg == 6 || $GetPkg == 7 || $GetPkg == 8)
				{
					$MaxCamp = array();
					$MaxCamp = GetValue("pkg_field_value","package_rate","pkg_field_id = 5 AND pkgid = ".$GetPkg);
					$MaxCamp = $MaxCamp[0];
				}
				if ($RunCamp == $MaxCamp)
				{
					$Message = GetValue("config_value","websettings","config_id = 304");
					$Message = str_replace("[Run]",$RunCamp,$Message);
					$Message = str_replace("[Max]",$MaxCamp,$Message);
				}
			}
			if (isset($_REQUEST['SmsQueID']) && $Message == "")
			{
				// $Query = "SELECT CM.clientid, SQ.clientmobid, SQ.smsquename,".
				// 	" SQ.intervalsec, SQ.startdate, SQ.closedate, SQ.adddate, SQ.status".
				// 	" FROM smsque SQ".
				// 	" INNER JOIN clientmobile CM ON SQ.clientmobid = CM.clientmobid".
				// 	" WHERE SQ.smsqueid = ".$_REQUEST['SmsQueID'];
				$Query = "SELECT CHM.clientid, SQ.mobileid, SQ.smsquename,".
					" SQ.intervalsec, SQ.startdate, SQ.closedate, SQ.adddate, SQ.status".
					" FROM smsque SQ".
					" INNER JOIN clienthavemob CHM ON SQ.mobileid = CHM.mobileid".
					" WHERE SQ.smsqueid = ".$_REQUEST['SmsQueID'];
				$rstRow = mysqli_query($Conn,$Query);
				if (mysqli_num_rows($rstRow) > 0)
				{
					if (mysqli_num_rows($rstRow) > 0)
					{
						$objRow = mysqli_fetch_object($rstRow);
						if ($objRow->clientid != $_SESSION[SessionID."ClientID"])
						{
							$Message = "Invalid Campaign Parent ...";
						}
						elseif ($objRow->status == 0)
						{
							$Message = "Campaign Cannot Be Edited - Campaign is Running. First Stop Campaign To Edit ...";
						}
						else
						{
							$cboDevice = $objRow->mobileid;
							$txtCampaignName = $objRow->smsquename;
							$txtStartDate = date("d-m-Y H:i",strtotime($objRow->startdate));
							if ($objRow->closedate == NULL)
							{
								$cboCampaignEnd = 0;
								$txtCloseView = "hide";
								$txtCloseDate = "";
							}
							else
							{
								$cboCampaignEnd = 1;
								$txtCloseView = "";
								$txtCloseDate = date("d-m-Y H:i",strtotime($objRow->closedate));
							}
							$txtWait = $objRow->intervalsec;
						}
					}
				}
				else
				{
					$Message = "Invalid Campaign Detail ...";
				}
			}
			if ($Message != "")
			{
				$MessageBG = "danger";
				$MessageHead = "Error:";
				$MessageIcon = "fa-exclamation-triangle";
		?>	
		<div style="padding-left: 15px; padding-right: 15px;">
			<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
				<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
				<span style="font-size:16px;"><?php echo($Message);?></span>
			</div>
		</div>
		<!-- Main Content -->
		<?php
			}
			if ($Message == "")
			{
		?>
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-info">
					<form name="Form" id="Form" action="" method="post" autocomplete="off" role="form" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<?php
											// $Query = "SELECT clientmobid, mobileno, mobilecode, mobilename, token, adddate, lastedit".
											// 	" FROM clientmobile WHERE clientid = ".$_SESSION[SessionID."ClientID"];
											$Query = "SELECT CM.mobileid, CM.mobileno, CM.mobilecode, CM.mobilename, CM.token, CM.adddate, CM.lastedit".
												" FROM clientmobile CM".
												" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
												" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"];
											if (isset($_REQUEST['Restart']))
											{
												$ClientMobID = GetValue("mobileid","smsque","smsqueid = ".$_REQUEST['Restart']);
												// $Query .= " AND clientmobid = ".$ClientMobID;
												$Query .= " AND CM.mobileid = ".$ClientMobID;
											}
										?>
										<label>Select Device To Send SMS :</label>
										<select name="cboDevice" id="cboDevice" onchange="SubmitForm();" CLASS="form-control select2" style="width: 100%;">
											<option value="0">-- Select Device --</option>
											<?php
												$rstRow = mysqli_query($Conn,$Query);
												while ($objRow = mysqli_fetch_object($rstRow))
												{
													if ($cboDevice == $objRow->mobileid)
														$ComboSelect = "selected";
													else
														$ComboSelect = "";
											?>
											<option value="<?php echo($objRow->mobileid);?>" <?php echo($ComboSelect);?>><?php echo($objRow->mobilename." - ".$objRow->mobileno);?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div id="divNewCamp" class="form-group">
										<label>Campaign Name</label>
										<input type="text" name="txtCampaignName" id="txtCampaignName" value="<?php echo($txtCampaignName);?>" class="form-control">
									</div>
									<div class="form-group">
										<label for="txtStartDate">Campaign Start Date</label>
										<input type="text" name="txtStartDate" id="txtStartDate" value="<?php echo($txtStartDate);?>" readonly class="form-control" placeholder="dd-mm-YYYY HH:ii"/>
									</div>
									<div class="form-group">
										<label>Campaign Option :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "No Campaign End Limit";
											$ComboData[] = "Campaign Must Stop At";
											DBComboArray("cboCampaignEnd",$ComboData,0,$cboCampaignEnd,"form-control select2","onchange=\"ChangeCampEnd();\"");
										?>
									</div>
									<div id="divCloseDate" class="form-group <?php echo($txtCloseView);?>">
										<label for="txtCloseDate">Campaign End Date</label>
										<input type="text" name="txtCloseDate" id="txtCloseDate" value="<?php echo($txtCloseDate);?>" readonly class="form-control" placeholder="dd-mm-YYYY HH:ii" />
									</div>
									<div class="form-group">
										<label>Delay Between SMS in Seconds :</label>
										<input type="text" name="txtWait" id="txtWait" value="<?php echo($txtWait);?>" class="form-control" />
									</div>
									<div class="form-group">
										<label for="cboFilter">Filter Numbers :</label>
										<select name="cboFilter" id="cboFilter" CLASS="form-control select2" style="width: 100%;">
											<option value="0">-- No Filter --</option>
											<option value="1">Keep Only Mobile Numbers</option>
											<!-- <option value="2">Keep Only Landline Numbers</option> -->
										</select>
									</div>
									<div class="form-group">
										<label for="cboDupli">Duplication :</label>
										<select name="cboDupli" id="cboDupli" CLASS="form-control select2" style="width: 100%;">
											<option value="0">Remove Duplication</option>
											<option value="1">Keep Duplicate Numbers</option>
										</select>
									</div>
									<div class="form-group">
										<label for="txtFile">Select Excel File Containing SMS List :</label>
										<input type="file" name="txtFile" id="txtFile">
									</div>
									<div class="form-group">
										<label><a href="http://www.bullkysms.com/upload/sample.xlsx" target="_blank">Download Sample Excel</a></label>
									</div>
									<div class="form-group">
										<?php
											if ($SmsQueID > 0)
											{
										?>
										<input type="hidden" name="SmsQueID" id="SmsQueID" value="<?php echo($SmsQueID);?>">
										<?php
											}
										?>
										<button type="submit" name="btnSave" id="btnSave" class="btn btn-primary">
											<i class="fa fa-floppy-o"></i> &nbsp; Save Campaign
										</button>	
									</div>
								</div>
							</div>
						</div>   
					</form>
				</div>
			</div>
		</section>
		<?php
			}
		?>
	</div>
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script src="<?php echo($PagePath);?>plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script>
	$(function() {
		$(".select2").select2();
		$("#txtStartDate").datetimepicker({
			format: 'dd-mm-yyyy hh:ii'
		});
		$("#txtCloseDate").datetimepicker({
			format: 'dd-mm-yyyy hh:ii'
		});
	});
	$("#Form").submit(function(evt) {
		evt.preventDefault();
		if (document.Form.cboDevice.value == 0)
		{
			ShowError(true,"Error!","Please Select The Device To Send Campaign SMS",undefined,"cboDevice");
			return(false);
		}
		if (document.Form.txtCampaignName.value == "")
		{
			ShowError(true,"Error!","Please Enter New Campaign Name",undefined,"txtCampaignName");
			return(false);
		}
		if (document.Form.txtStartDate.value == "")
		{
			ShowError(true,"Error!","Please Select Campaign Start Date",undefined,"txtStartDate");
			return(false);
		}
		if (document.Form.cboCampaignEnd.value == 1)
		{
			if (document.Form.txtCloseDate.value == "")
			{
				ShowError(true,"Error!","Please Select Campaign Close Date",undefined,"txtCloseDate");
				return(false);
			}
			if (CompareTime(document.Form.txtStartDate.value,document.Form.txtCloseDate.value) > 0)
			{
				ShowError(true,"Error!","Please Select Proper Date Range<br><br>Start Date Must Be Smaller Than Close Date",undefined,"txtStartDate");
				return(false);
			}
		}
		if (IsNumber(document.Form.txtWait.value,false,false,0) == false)
		{
			ShowError(true,"Error!","Please Enter Delay in Sending SMS.<br><br>Zero For Instant or Any Whole Number<br>Good Option To Avoid Spam.",undefined,"txtWait");
			return(false);
		}
		if ($("#SmsQueID").length == 0)
		{
			if (document.Form.txtFile.value == "")
			{
				ShowError(true,"Error!","Please Select Campaign File in XLSX Format",undefined,"txtFile");
				return(false);
			}
		}
		if (document.Form.txtFile.value != "")
		{
			if (CheckFile("Form","txtFile","XLSX") == false)
			{
				ShowError(true,"Error!","Please Select Campaign File in XLSX Format",undefined,"txtFile");
				return(false);
			}
		}
		var FrmData = new FormData(document.Form);
		var Result = "";
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
					xhr: function() {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function(evt) {
							if (evt.lengthComputable)
							{
								var percentComplete = (evt.loaded / evt.total) * 100;
								self.setContent(percentComplete);
								//$(".progress-bar").width(percentComplete + '%');
								//$(".progress-bar").html(percentComplete+'%');
							}
						}, false);
						return xhr;
					},
					url: "../ajaxs/campaign-save",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
						// alert(response.Message);
						Result = response.Status;
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function(jqXHR,exception){
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Done" && $("#SmsQueID").length == 0)
						{
							// window.location = "campaign-add";
							self.close();
						}
					}
				}
			},
			onClose: function () {
			}
		});
	});
</script>
</body>
</html>