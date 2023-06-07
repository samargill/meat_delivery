<?php
	$PageID = array(3,5,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	$PageTitle= "Pending SMS";
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
					<form name="Form" role="form" action="sms-pending" method="post">
						<div class="card-body">
							<div class="row">
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
									<div class="form-group">
										<label>Search Text</label>
										<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search By Devices :</label>
										<?php
											// DBCombo("cboDevice","clientmobile","clientmobid","CONCAT(mobilename,' - ',mobileno)",
											// 	"WHERE clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
											// 	"form-control select2","onchange=\"\" style=\"width: 100%;\"");
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.clientid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
								</div>
								<div class="col-md-4">
								</div>
							</div>
							<div class="row mb-2">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover table-responsive" width="100%">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="16%" style="text-align:left;">To Name</th>
										<th width="10%" style="text-align:left;">To Mobile</th>
										<th width="16%" style="text-align:left;">From Device</th>
										<th width="38%" style="text-align:left;">Message</th>
										<th width="10%" style="text-align:left;">Date</th>
										<th width="7%"  style="text-align:left; min-width: 80px;"></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec = 50;
										$Page = (isset($_REQUEST['Page'])) ? $_REQUEST['Page'] : 1;
										$PageLink = "sms-pending";
										$PageParam = "cboCamStatus=".$cboCamStatus."&cboDevice=".$cboDevice."&cboSearch=".$cboSearch;
										if (strlen($txtSearch) > 0)
										{
											$PageParam .= "&txtSearch=".$txtSearch;
										}
										// $QuerySelect = "SELECT SmsL.smsid, CM.mobilename As FromDeviceName, CM.mobileno As FromDeviceNo,".
										// 	" SmsL.smsaddtime, SmsL.smstext, SmsL.mobile As ToMobile, C.fullname As ToName";
										// $QueryJoin = "".
										// 	" FROM smsquelist SmsL".
										// 	" INNER JOIN smsque SmsQ ON SmsL.smsqueid = SmsQ.smsqueid".
										// 	" INNER JOIN clientmobile CM ON SmsQ.clientmobid = CM.clientmobid".
										// 	" LEFT OUTER JOIN clientcontact C ON SmsL.mobile = C.mobile";
										// $QueryWhere = "".
										// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
										// 	" AND SmsL.smssent IS NULL AND SmsL.smsaddtime >  '2021-01-01 00:00:00'";
										$QuerySelect = "SELECT SmsL.smsid, CM.mobilename As FromDeviceName, CM.mobileno As FromDeviceNo,".
											" SmsL.smsaddtime, SmsL.smstext, SmsL.mobile As ToMobile, C.fullname As ToName";
										$QueryJoin = "".
											" FROM smsquelist SmsL".
											" INNER JOIN smsque SmsQ ON SmsL.smsqueid = SmsQ.smsqueid".
											" INNER JOIN clientmobile CM ON SmsQ.mobileid = CM.mobileid".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" LEFT OUTER JOIN clientcontact C ON SmsL.mobile = C.mobile";
										$QueryWhere = "".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
											" AND SmsL.smssent IS NULL AND SmsL.smsaddtime >  '2021-01-01 00:00:00'";
										if ($cboDevice > 0)
										{
											$QueryWhere .= " AND CM.mobileid = ".$cboDevice;
										}
										if ($cboSearch > 0)
										{
											if ($cboSearch == 1)
											{
												$QueryWhere .= " AND SmsL.mobile LIKE '%".$txtSearch."%'";
											}
											if ($cboSearch == 2)
											{
												$QueryWhere .= " AND SmsL.smstext LIKE '%".$txtSearch."%'";
											}
										}
										$Total  = 0;
										$Query = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total = $objRow->Total;
										$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere.
											" ORDER BY SmsL.smsaddtime".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="center"><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->ToName);?></td>
										<td align="left"  ><?php echo($objRow->ToMobile);?></td>
										<td align="left"  ><?php echo($objRow->FromDeviceName." - ".$objRow->FromDeviceNo);?></td>
										<td align="left">
											<textarea style="width:100% !important"><?php echo($objRow->smstext);?></textarea>
										</td>
										<!-- <td align="left"  ><?php //echo(str_replace("\\n","<br>",str_replace("\n","<br>",$objRow->smstext)));?></td> -->
										<td align="left"  ><?php echo(date("d-M-Y H:i",strtotime($objRow->smsaddtime)));?></td>
										<td align="center">
											<?php echo($objRow->smsid);?>
											<div class="btn-group">
												<button type="button" title="Try Re-Send" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body" 
													onclick="ReSendSms(<?php echo($objRow->smsid);?>);">
													<i class="fa fa-refresh"></i>
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
	function ReSendSms(SmsID)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Re-Send This SMS ?",
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
									url: "<?php echo($PagePath);?>pages/ajaxs/sms-resend",
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