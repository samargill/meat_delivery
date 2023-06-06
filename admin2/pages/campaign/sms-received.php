<?php
	$PageID = array(3,3,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	$PageTitle = "Received Messages";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_REQUEST['cboCamStatus']))
		$cboCamStatus = $_REQUEST['cboCamStatus'];
	else
		$cboCamStatus = 0;
	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['cboOptStatus']))
		$cboOptStatus = $_REQUEST['cboOptStatus'];
	else
		$cboOptStatus = -1;
	if (isset($_REQUEST['cboSearch']))
		$cboSearch = $_REQUEST['cboSearch'];
	else
		$cboSearch = 0;
	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";
	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
		$txtStartDate = date("Y-m-d 00:00:00");
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d 23:59:59");
	if (isset($_REQUEST['PageParam']))
	{
		$PageParam = $_REQUEST['PageParam'];
	}
	else
	{
		$PageParam = "";
	}
	$Response = "".
		"{".
			"\"Status\": \"[Status]\",".
			"\"Message\": \"[Message]\"".
		"}";

	if (isset($_POST['txtName']) && isset($_POST['txtEmail']) && isset($_POST['txtSubject']))
	{
		$TextMsg = $RespHead = $RespText = "";
		$Query = "SELECT clientid FROM clientmisscallalert WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
		$rstRow= mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			$TextMsg = "Missed Call alert service(via Email) is activated successfully";
			$Query = "INSERT INTO clientmisscallalert(clientid, name, email, subject, adddate, status)".
				" VALUES(".$_SESSION[SessionID."ClientID"].", '".$_REQUEST['txtName']."', '".$_REQUEST['txtEmail']."', '".$_REQUEST['txtSubject']."', NOW(), 1)";
		}
		else
		{
			$TextMsg = "Missed Call alert service details Updated successfully";
			$Query = "UPDATE clientmisscallalert SET".
				" name     = '".$_POST['txtName']."',".
				" email    = '".$_POST['txtEmail']."',".
				" subject  = '".$_POST['txtSubject']."',".
				" lastedit = NOW()".
				" WHERE clientid =".$_SESSION[SessionID."ClientID"];	
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$RespHead = "Error";
			$RespText = "Failed To Get Missed Call Alert Service. Please Try Again ...<br><br>".mysqli_error($Conn);
			goto Response;
		}
		else
		{
			$RespHead = "Done";
			$RespText = $TextMsg;
			goto Response;
		}
		Response:
		if (isset($RespHead) == false)
		{
			$RespHead = "Error";
			$RespText = "Undefined Operation ...";
		}
		$Response = str_replace("[Status]",$RespHead,$Response);
		$Response = str_replace("[Message]",$RespText,$Response);
		echo($Response);
		die();	
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
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
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
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
					<form name="Form" role="form" action="sms-received" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Search by Devices :</label>
										<?php
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[] = "-- Select Opt Status --";
											$ComboData[] = "Opt-In";
											$ComboData[] = "Opt-Out";
										?>
										<label>Opt Status :</label>
										<?php
											DBComboArray("cboOptStatus",$ComboData,0,$cboOptStatus,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Date Range :</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-clock-o"></i>&nbsp;&nbsp;
													<input type="checkbox" name="ChkDate" <?php if (isset($_REQUEST['ChkDate'])) echo("CHECKED");?>>
												</span>
											</div>
											<input type="text" name="cboDate" id="cboDate" readonly class="form-control pull-right" style="background-color:#FFFFFF;">
											<input type="hidden" name="txtStartDate" value="<?php echo($txtStartDate);?>">
											<input type="hidden" name="txtCloseDate" value="<?php echo($txtCloseDate);?>">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[] = "-- Search By --";
											$ComboData[] = "Mobile No";
											$ComboData[] = "Message Text";
										?>
										<label>Search By :</label>
										<?php
											DBComboArray("cboSearch",$ComboData,0,$cboSearch,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search Text</label>
										<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4 mb-2">
									<button type="submit" name="btnSearch" class="btn btn-primary">
										<i class="fas fa-search"></i>&nbsp; Search
									</button>
								</div>
								<div class="col-md-4 mb-2">
									<?php
										$BtnBgColor   = "warning";	
 										$BtnAlertText = "Update Missed Call Alert Email";
										$Query = "SELECT clientid FROM clientmisscallalert WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
										$rstRow= mysqli_query($Conn,$Query);
										if (mysqli_num_rows($rstRow) == 0)
										{
											$BtnBgColor   = "info";
											$BtnAlertText = "Add Missed Call Alert Email";
										}
									?>
									<button type="button" id="btnMissCallAlert" class="btn btn-<?php echo($BtnBgColor);?>" style="" onclick="ShowAlertModal();" title="Get Missed Call Alert Service via Email">
										<i class="fas fa-envelope"></i>&nbsp;&nbsp;<?php echo($BtnAlertText);?>
									</button>
								</div>	
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover table-responsive" width="100%">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="20%" style="text-align:left;">From Name</th>
										<th width="12%" style="text-align:left;">From Mobile</th>
										<th width="18%" style="text-align:left;">To Device</th>
										<th width="29%" style="text-align:left;">Message</th>
										<th width="10%" style="text-align:left;">Date</th>
										<th width="10%"  style="text-align:left; min-width: 120px;"></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec = 50;
										$Page = (isset($_REQUEST['Page'])) ? $_REQUEST['Page'] : 1;
										$PageLink = "sms-received";
										$PageParam = "cboCamStatus=".$cboCamStatus."&cboDevice=".$cboDevice.
											"&cboOptStatus=".$cboOptStatus."&cboSearch=".$cboSearch;
										if (strlen($txtSearch) > 0)
										{
											$PageParam .= "&txtSearch=".$txtSearch;
										}
										if (isset($_REQUEST['ChkDate']))
										{
											$PageParam .= "&ChkDate&txtStartDate=".$txtStartDate."&txtCloseDate=".$txtCloseDate;
										}
										$QuerySelect = "SELECT SmsR.smsid, CM.mobilename As ToDeviceName, CM.mobileno As ToDeviceNo,".
											" SmsR.smsdate, SmsR.smstext, SmsR.mobile As FromMobile, C.fullname As FromName, SmsR.optout";
										$QueryJoin = "".
											" FROM smsreclist SmsR".
											" INNER JOIN clientmobile CM ON SmsR.mobileid = CM.mobileid".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" LEFT OUTER JOIN clientcontact C ON SmsR.mobile = C.mobile";
										$QueryWhere = "".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"];
												
										if (isset($_REQUEST['ChkDate']))
										{
											$QueryWhere .= " AND (SmsR.smsdate BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."')";
										}
										if ($cboDevice > 0)
										{
											$QueryWhere .= " AND CM.mobileid = ".$cboDevice;
										}
										if ($cboOptStatus >= 0)
										{
											$QueryWhere.= " AND SmsR.optout = ".$cboOptStatus;
										}
										if ($cboSearch > 0)
										{
											if ($cboSearch == 1)
											{
												$QueryWhere .= " AND SmsR.mobile LIKE '%".$txtSearch."%'";
											}
											if ($cboSearch == 2)
											{
												$QueryWhere .= " AND SmsR.smstext LIKE '%".$txtSearch."%'";
											}
										}
										$Total  = 0;
										$Query = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total = $objRow->Total;
										$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere.
											" ORDER BY SmsR.smsdate".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											$BgColor = "";
											if ($objRow->optout == 1)
											{
												$BgColor = "style=\"background-color: #E8D5D6;\"";
											}
									?>
									<tr id="Row<?php echo $Index; ?>" <?php echo $BgColor;?>>
										<td align="center"><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->FromName);?></td>
										<td align="left"  ><?php echo($objRow->FromMobile);?></td>
										<td align="left"  ><?php echo($objRow->ToDeviceName." - ".$objRow->ToDeviceNo);?></td>
										<td align="left"  ><?php echo($objRow->smstext);?></td>
										<td align="left"  ><?php echo(date("d-M-Y H:i",strtotime($objRow->smsdate)));?></td>
										<td align="center">
											<div class="btn-group">
												<?php
													if ($objRow->optout == 0)
													{
														$Icon  = "fa-chain-broken";
														$Title = "Opt-Out";
														$Func  = "OptInOut($objRow->smsid,'out');";
													}
													else
													{
														$Icon = "fa-chain";
														$Title = "Opt-In";
														$Func  = "OptInOut($objRow->smsid,'in');";
													}
												?>
												<button type="button" title="<?php echo($Title)?>" onclick="<?php echo($Func);?>" 
													class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa <?php echo($Icon)?>"></i>
												</button>
												<?php
													$Unseen = GetValue("COUNT(smsid) As Total","smsreclist","mobile = ".$objRow->FromMobile." AND seen = 0");
													$btnColor = "#FFFFFF";
												?>
												<button type="button" title="Conversation" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body" 
													onclick="ViewConvers('R-<?php echo($objRow->smsid);?>');">
													<i class="fa fa-envelope" style="color:<?php echo($btnColor);?>">
														<?php echo($Unseen);?>
													</i>
												</button>
												<button type="button" title="Delete SMS" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body" 
													onclick="DeleteSms(<?php echo($Index);?>,<?php echo($objRow->smsid);?>);">
													<i class="fas fa-trash-alt"></i>
												</button>
											</div>
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
		<!-- Modal -->
		<div class="modal fade" id="MissedCallAlert" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-dialog-top" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="">Missed Call Alert Email</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form name="FrmAlertEmail" id="FrmAlertEmail" action="" method="post">
							<span><strong class="text-danger">Note:</strong> Please Fill the receiver details below</span>
							<?php
								$Name = $Email = $Subject = "";
								$Query = "SELECT name, email, subject".
									" FROM clientmisscallalert".
									" WHERE clientid =".$_SESSION[SessionID."ClientID"];
								$rstRow= mysqli_query($Conn,$Query);
								if (mysqli_num_rows($rstRow) > 0)
								{
									$objRow = mysqli_fetch_object($rstRow);
									$Name    = $objRow->name;
									$Email 	 = $objRow->email;
									$Subject = $objRow->subject;
								}	
							?>			
							<div class="form-group mt-3">
								<label>Name (*)</label>
								<input type="name" name="txtName" id="txtName" value="<?php echo($Name);?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Email (*)</label>
								<input type="email" name="txtEmail" id="txtEmail" value="<?php echo($Email);?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Subject (*)</label>
								<input type="name" name="txtSubject" id="txtSubject" value="<?php echo($Subject);?>" class="form-control">
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" onclick="return Verify();">Save changes</button>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.content-wrapper -->
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function () {
		//Init Select2
		$(".select2").select2();
	});
	function ViewConvers(SmsID)
	{
		var Win = Popup("sms-chat?SmsID="+SmsID,"KS_BullkySms_Edit",750,770,340,-40);
		Win.focus();
	}
	function DeleteSms(Index,SmsID)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Delete This SMS ?",
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			columnClass: 'col-md-6 col-md-offset-3',
			opacity: 0.5,
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
									url: "<?php echo($PagePath);?>pages/ajaxs/sms-recvd-delete",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"SmsID": SmsID
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
	function OptInOut(SmsID,OptType)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Opt-"+OptType+" The Sender on This Device ?",
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			columnClass: 'col-md-6 col-md-offset-3',
			opacity: 0.5,
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
									url: "<?php echo($PagePath);?>pages/ajaxs/sms-recvd-opt"+OptType,
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"SmsID": SmsID
									}
									}).done(function (response) {
										self.setTitle(response.Status);
										self.setContent(response.Message);
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
	function ShowAlertModal(BtnName)
	{
		$('#MissedCallAlert').modal('show');
	}
	function Verify()
	{
		if (IsEmpty(document.FrmAlertEmail.txtName.value) == true)
		{
			ShowError(true,"Error!","Please Enter Complete Name of the Receiver.",undefined,"txtName");
			return(false);
		}
		if (IsEmail(document.FrmAlertEmail.txtEmail.value,false) == false)
		{
			ShowError(true,"Error!","Please Enter Valid Email of the Receiver.",undefined,"txtEmail");
			return(false);
		}
		if (IsEmpty(document.FrmAlertEmail.txtSubject.value) == true)
		{
			ShowError(true,"Error!","Please Enter Email Subject for the Receiver.",undefined,"txtSubject");
			return(false);
		}
		var Result  = "";
		var FrmData = new FormData(document.FrmAlertEmail);
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
					url: "sms-received",
					type: "POST",
					data: FrmData,
					async: false,
					dataType: "JSON",
					contentType: false,
					processData: false
					}).done(function (response) {
						Result = response.Status;
						if (Result == "Done")
						{
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}
						else
						{
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}
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
							$('#btnMissCallAlert').html("<i class='fas fa-envelope'></i>&nbsp;&nbsp;Update Missed Call Alert Email").attr("style","color:#1f2d3d;background-color:#ffc107;border-color:#ffc107;box-shadow:none");
						}
						$('#FrmAlertEmail')[0].reset();
						$('#MissedCallAlert').modal('hide');
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "down";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>