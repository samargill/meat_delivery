<?php
	$PageID = array(2,1,0);
	$PagePath = "../../";
	$PageMenu = "SMS Devices";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/packages.php");
	include($PagePath."lib/combos.php");

	$DefCountry = GetClientCountryCode();
	if (isset($_REQUEST['cboSearch']))
		$cboSearch = $_REQUEST['cboSearch'];
	else
		$cboSearch = 0;
	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";

	$Flag = false;
	$GetPkg = GetValue("pkgtype","client","clientid = ".$_SESSION[SessionID."ClientID"]);
	$PkgName = GetValue("pkgname","package","pkgid = ".$GetPkg);
	if ($GetPkg == 6 || $GetPkg == 7 || $GetPkg == 8 || $GetPkg == 9)
	{
		$Flag = true; 
		$PageMenu = "Mobile";			
	}	
?>
<!DOCTYPE html>
<html>
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
						<h1 class="m-0 text-dark">View Mobile</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">SMS Devices</li>
							<li class="breadcrumb-item active">View Devices</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="device-view" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "Mobile No";
											$ComboData[] = "Mobile Code";
											$ComboData[] = "Mobile Name";
										?>
										<?php
											DBComboArray("cboSearch",$ComboData,0,$cboSearch,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtSearch">Search Text :</label>
										<input type="text" name="txtSearch" id="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row mb-2">
								<div class="col-md-3">
									<button type="submit" name="btnSearch" class="btn btn-primary">
										<i class="fa fa-search"></i> &nbsp; Search Mobile
									</button>
								</div>
								<?php
									if ($Flag == false)
									{
								?>
								<div class="col-md-3">
									<button type="button" name="btnAddDevice" id="btnAddDevice" class="btn bg-purple" onclick="EditDevice(0,0);">
										<i class="fa fa-plus"></i> &nbsp; Add New Device
									</button>
								</div>
								<?php
									}
								?>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover table-responsive" width="100%">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="11%" style="text-align:left;"  >Mobile No</th>
										<th width="11%" style="text-align:left;"  >Mobile Code</th>
										<th width="16%" style="text-align:center;"  >Mobile Name</th>
										<?php
											if ($_SESSION[SessionID."ClientID"] == 1)
											{
										?>
										<th width="9%" style="text-align:left;"  >Max Slots</th>
										<th width="10%" style="text-align:left;"  >Used Slots</th>
										<?php
											}
											if (($_SESSION[SessionID."ClientID"] == 1) || ($Flag == false))
											{	
										?>
										<th width="11%" style="text-align:center;">Device Status</th>
										<?php
											}
											if ($_SESSION[SessionID."ClientID"] == 1)
											{
										?>	
										<th width="10%" style="text-align:center;">Add Date</th>
										<th width="10%" style="text-align:center;">Last Edit</th>
										<?php
											}
											if ($Flag == true)
											{
										?>
										<th width="25%" style="text-align:center;">Package Name</th>
										<th width="25%" style="text-align:center;">Package Expiry</th>
										<?php
											}	
										?>
										<th width="7%"  style="text-align:center; min-width:80px;">-</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec = 50;
										if (isset($_REQUEST['Page']))
											$Page = $_REQUEST['Page'];
										else
											$Page = 1;
										$PageLink = "device-view";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect = "SELECT CM.mobileid, CM.mobileno, CM.mobilecode, CM.mobilename,".
											" CM.maxslot, CM.usedslot, CM.token, CM.adddate, CM.lastedit, C.pkgexpiry";
										$QueryJoin 	 = "".
											" FROM clientmobile CM".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" INNER JOIN client C ON CHM.clientid = C.clientid";
										$QueryWhere  = "".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"];
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 0)
												$QueryWhere .= " AND CM.mobileno LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 1)
												$QueryWhere .= " AND CM.mobilecode LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 2)
												$QueryWhere .= " AND CM.mobilename LIKE '%".$txtSearch."%'";
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total  = $objRow->Total;
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere." ORDER BY CM.mobileid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->token != "")
												$DeviceStatus = "Registered";
											else
												$DeviceStatus = "UnRegistered";
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="left"><?php echo($Index);?></td>
										<td align="left" id="divMobileNo<?php echo($Index);?>"><?php echo($objRow->mobileno);?></td>
										<td align="center" id="divMobileCode<?php echo($Index);?>"><?php echo($objRow->mobilecode);?></td>
										<td align="center" id="divMobileName<?php echo($Index);?>"><?php echo($objRow->mobilename);?></td>
										<?php
											if ($_SESSION[SessionID."ClientID"] == 1)
											{
										?>
										<td align="center" id="divMaxSlot<?php echo($Index);?>"><?php echo($objRow->maxslot);?></td>
										<td align="center" id="divUsedSlot<?php echo($Index);?>"><?php echo($objRow->usedslot);?></td>
										<?php
											}
											if (($_SESSION[SessionID."ClientID"] == 1) || ($Flag == false))
											{	
										?>
										<td align="center"><?php echo($DeviceStatus);?></td>
										<?php
											}
											if ($_SESSION[SessionID."ClientID"] == 1)
											{
										?>
										<td align="center"><?php echo(ShowDate($objRow->adddate,1));?></td>
										<td align="center"><?php echo(ShowDate($objRow->lastedit,1));?></td>
										<?php
											}	
											if ($Flag == true)
											{
										?>
										<td align="center"><?php echo($PkgName." Sms Package");?></td>
										<td align="center"><?php echo(date('d-m-Y',strtotime($objRow->pkgexpiry)));?></td>
										<?php
											}
										?>	
										<td align="center">
											<div class="btn-group">
												<?php		
													if ($Flag == true)
													{
														$BtnStatus	= "disabled";
													}
													else
													{
														$BtnStatus	= "";
													}
												?>	
												<button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="EditDevice(<?php echo($Index);?>,<?php echo($objRow->mobileid);?>);" data-toggle="tooltip" data-container="body" <?php echo($BtnStatus);?>>
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="DeleteDevice(<?php echo($Index);?>,<?php echo($objRow->mobileid);?>);" data-toggle="tooltip" data-container="body" <?php echo($BtnStatus);?>>
													<i class="fas fa-trash-alt"></i>
												</button>
											</div><!-- /.btn-group -->
										</td>
									</tr>
									<?php
										}
									?>
								</tbody>
							</table>
							<?php
								include($PagePath."includes/paging.php");
							?>
						</div>
					</form>
				</div>
			</div>
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<div id="Modal-Device" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form name="FrmDevice" id="FrmDevice" action="" method="post" enctype="multipart/form-data">
				<div class="modal-header">
					<h4 id="Modal-Device-Title" class="modal-title">Add New Device</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Mobile # (*) :</label><br>
								<input type="text" name="txtMobileNo" id="txtMobileNo" value="" class="form-control" maxlength="20" style="text-indent: 5px;">
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label>Mobile Code (*) :</label><br>
								<input type="text" name="txtMobileCode" id="txtMobileCode" value="" class="form-control" maxlength="10">
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label>Mobile Description (*) :</label>
								<input type="text" name="txtMobileName" id="txtMobileName" value="" class="form-control" maxlength="50">
							</div>
						</div>
						<?php
							if ($_SESSION[SessionID."ClientID"] == 1)
							{
						?>
						<div class="col-md-8">
							<div class="form-group">
								<label>Max slots (*) :</label>
								<input type="text" name="txtMaxSlot" id="txtMaxSlot" value="" class="form-control" maxlength="2">
							</div>
						</div>
						<?php
							}
						?>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="DeviceID" id="DeviceID" value="">
					<input type="hidden" name="MobileFull" id="MobileFull" value="">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="submit" name="btnSaveEdu" class="btn btn-primary">Save Device</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<script src="<?php echo($PagePath);?>../plugins/intl-mobile/js/intlTelInput.js"></script>
<!-- Page Script -->
<script>
	var iti;
	$(function () {
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
	function EditDevice(Index,DeviceID)
	{
		if (DeviceID == 0)
		{
			<?php CheckRight("Add","ShowError");?>
			$("#txtMobileNo").val("");
			$("#txtMobileCode").val("");
			$("#txtMobileName").val("");
			$("#DeviceID").val(0);
			$("#Modal-Device-Title").html("Add New Device");
			$('#Modal-Device').modal('show');
		}
		else
		{
			<?php CheckRight("Edit","ShowError");?>
			$("#txtMobileNo").val($("#divMobileNo"+Index).html());
			$("#txtMobileCode").val($("#divMobileCode"+Index).html());
			$("#txtMobileName").val($("#divMobileName"+Index).html());
			$("#txtMaxSlot").val($("#divMaxSlot"+Index).html());
			$("#DeviceID").val(DeviceID);
			$("#Modal-Device-Title").html("Edit Device");
			$('#Modal-Device').modal('show');
		}
	}
	// Add / Edit Device Submit
	$("#FrmDevice").submit(function(evt) {
		evt.preventDefault();
		if (iti.isValidNumber() == false)
		{
			ShowError(true,"Error!","Please Enter Your Valid Mobile #<br><br>"+itiErrorMap[iti.getValidationError()],undefined,"txtMobileNo");
			return(false);
		}
		if (iti.getNumberType() != 1 && iti.getNumberType() != 2)
		{
			ShowError(true,"Error!","Please Enter Your Valid Mobile #<br><br>Entered Number is Not Mobile # [ "+iti.getNumberType()+" ]",undefined,"txtMobile");
			return(false);
		}
		

		var Msg = "Please Enter Device Code<br><br> - At Least 3 Chars<br> - Dash & Alpha Numeric<br> - Short Code To Identify The Device";
		var ShortCode = document.FrmDevice.txtMobileCode.value;
		var regExp = /^[a-zA-Z]{3,}[\-]{1,1}[0-9]{1,}$/;
		if (!regExp.test(ShortCode))
		{
			ShowError(true,"Error!",Msg,undefined,"txtMobileCode");
			return(false);	
		}
		if (IsEmpty(document.FrmDevice.txtMobileName.value,false) == true)
		{
			ShowError(true,"Error!","Please Enter Device Description - To Describe The Device",undefined,"txtMobileName");
			return(false);
		}
		// if (IsEmpty(document.FrmDevice.txtMaxSlot.value,false) == true)
		// {
		// 	ShowError(true,"Error!","Please Enter Maximum Device Slots",undefined,"txtMaxSlot");
		// 	return(false);
		// }
		document.FrmDevice.MobileFull.value = iti.getNumber();
		var FrmData = new FormData(document.FrmDevice);
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
					url: "<?php echo($PagePath);?>pages/ajaxs/device-save",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					async: false,
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
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
						if (Result == "Done")
						{
							$("#Modal-Device").modal("hide");
						}
					}
				}
			},
			onClose: function () {
			}
		});
	});
	function DeleteDevice(Index,DeviceID)
	{
		<?php CheckRight("Delete","ShowError");?>
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Delete This Device ?",
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: 'col-md-6 col-md-offset-3',
			buttons: {
				"confirm": {
					text: "Yes",
					btnClass: "btn-blue",
					keys: ['enter'],
					action: function() {
						var RemoveRow = false;
						$.confirm({
							content: function () {
								var self = this;
								return $.ajax({
									url: "<?php echo($PagePath);?>pages/ajaxs/device-delete",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"DeviceID": DeviceID
									}
									}).done(function (response) {
										self.setTitle(response.Status);
										self.setContent(response.Message);
										if (response.Status == "Done")
										{
											RemoveRow = true;
										}
									}).fail(function(jqXHR,exception) {
										self.setTitle("Error!");
										self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
								});
							},
							buttons: {
								"OK": {
									text: "OK",
									btnClass: "btn-blue"
								}
							},
							onClose: function () {
								if (RemoveRow)
								{
									$("#Row"+Index).hide();
								}
							}
						});
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
					action: function() {
					}
				}
			}
		});
	}
</script>
</body>
</html>