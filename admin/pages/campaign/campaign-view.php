<?php
	$PageID = array(3,2,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	$PageTitle = "View campaign";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	include($PagePath."lib/packages.php");

	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['cboCamStatus']))
		$cboCamStatus = $_REQUEST['cboCamStatus'];
	else
		$cboCamStatus = 1;
	if (isset($_REQUEST['cboDevStatus']))
		$cboDevStatus = $_REQUEST['cboDevStatus'];
	else
		$cboDevStatus = 0;
	if (isset($_REQUEST['txtCamName']))
		$txtCamName = $_REQUEST['txtCamName'];
	else
		$txtCamName = "";
	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
		$txtStartDate = date("Y-m-d 00:00:00");
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d 23:59:59");
	$UrlParams = "&cboCamStatus=".$cboCamStatus."&cboDevice=".$cboDevice."&cboDevStatus=".$cboDevStatus."&txtCamName=".$txtCamName;
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
<script language="javascript">
	function Verify()
	{
	}
	function AddCampaign()
	{
		<?php CheckRight("Add","Redirect");?>
		var Win = Popup("campaign-add","KS_BullkySms_Edit",740,1024,100,100);
		Win.focus();
	}
	function EditCampaign(SmsQueID)
	{
		<?php CheckRight("Edit","ShowError");?>
		var Win = Popup("campaign-add?SmsQueID="+SmsQueID,"KS_BullkySms_Edit",740,1024,100,100);
		Win.focus();
	}
	function ViewMessages(SmsQueID)
	{
		<?php CheckRight("View","Redirect");?>
		var Win = Popup("message-view?SmsQueID="+SmsQueID,"KS_BullkySms_Edit",740,1024,100,100);
		Win.focus();
	}
</script>
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
		<!-- Page Error -->
		<?php
			if (isset($_REQUEST['Err']))
			{
				$Message = "";
				$MessageBG = "callout-danger lead";
				$MessageHead = "Error:";
				$MessageIcon = "fa-exclamation-circle";
				switch ($_REQUEST['Err'])
				{
					case 2:
						$Message = "Campaign Updated Successfully ...";
						break;
					case 3:
						$Message = "Campaign Deleted Successfully ...";
						break;
					case 4:
						$Message = "Campaign Reset Successfully ...";
					break;
					case 101:
						$Message = "Unable To Perform Operation - Fatal Error ...";
						if (isset($_SESSION["MysqlErr"]))
						{
							$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
							unset($_SESSION["MysqlErr"]);
						}
						break;
					case 102:
						$Message = "Campaign Cannot Be Deleted - Contact is added in List ...";
						break;
					case 103:
						$Message = "Campaign Cannot Be Start - Application Is Not Running in Android Device";
						break;
				}
				if ($_REQUEST['Err'] < 100)
				{
					$MessageHead = "Note:";
					$MessageBG = "callout-info";
					$MessageIcon = "fa-info-circle";
				}
		?>
		<div class="pad margin no-print">
			<div class="callout <?php echo($MessageBG);?>" style="margin-bottom: 0!important;">
				<h4><i class="fa <?php echo($MessageIcon);?>"></i> <?php echo($MessageHead);?></h4>
				<span style="font-size:16px;"><?php echo($Message);?></span>
			</div>
		</div>
		<?php
			}
		?>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="campaign-view" method="post" onsubmit="return Verify();">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Campaign Devices :</label>
										<?php
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
									<div class="form-group">
										<label>Campaign Name</label>
										<input type="text" name="txtCamName" value="<?php echo($txtCamName);?>" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
								   	<div class="form-group">
										<label>Campaign Start Date Range :</label>
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
									<div class="form-group">
										<label>Campaign Status :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Active";
											$ComboData[] = "In-Active";
											DBComboArray("cboCamStatus",$ComboData,0,$cboCamStatus,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Device Status :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Ready";
											$ComboData[] = "Not Ready";
											DBComboArray("cboDevStatus",$ComboData,0,$cboDevStatus,"form-control select2","");
										?>
									</div>
								</div>
							</div>
							<div class="row mb-2">
								<div class="col-md-4">
									<div class="form-group">
										<button type="submit" name="btnSearch" class="btn btn-primary">
											<i class="fa fa-search"></i> &nbsp; Search Campaign
										</button>
									</div>
								</div>
								<?php
									$UserCurPkg = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
									if ($UserCurPkg != 9)
									{
								?>		
								<div class="col-md-4">
									<div class="form-group">
										<button type="button" class="btn bg-purple" onclick="AddCampaign();">
											<i class="fa fa-plus"></i> &nbsp; Add New Campaign
										</button>
									</div>
								</div>
								<?php
									}
								?>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="21%" style="text-align:left;"  >Campaign Name</th>
										<th width="20%" style="text-align:left;"  >Campaign Device</th>
										<th width="15%"  style="text-align:left;" >Device Status</th>
										<th width="12%"  style="text-align:left;" >Start Date</th>
										<th width="12%"  style="text-align:left;" >Close Date</th>
										<th width="15%"  style="text-align:center; min-width:170px;">-</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										// $UserCurPkg  = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
										// if ($UserCurPkg == 6 || $UserCurPkg == 7 || $UserCurPkg == 8 || $UserCurPkg == 9)
										// {
										// 	$TokenID = GetValue("config_value","websettings","config_id = 404");
											// $Query   = "UPDATE clientmobile SET token = '".$TokenID."' WHERE clientid =".$_SESSION[SessionID."ClientID"];
											// mysqli_query($Conn,$Query);
											// $Query   = "UPDATE clientmobile CM".
											// 	" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid SET".
											// 	" CM.token = '".$TokenID."' WHERE CHM.clientid =".$_SESSION[SessionID."ClientID"];
											// mysqli_query($Conn,$Query);
										// }
										// $Query = "SELECT SQ.smsqueid, CM.mobilename, CM.mobileno, SQ.smsquename,".
										// 	" CM.token, SQ.startdate, SQ.closedate, SQ.status".
										// 	" FROM clientmobile CM".
										// 	" INNER JOIN smsque SQ ON SQ.clientmobid = CM.clientmobid".
										// 	" WHERE clientid = ".$_SESSION[SessionID."ClientID"];
										$Query = "SELECT SQ.smsqueid, CM.mobilename, CM.mobileno, SQ.smsquename,".
											" CM.token, SQ.startdate, SQ.closedate, SQ.status".
											" FROM clientmobile CM".
											" INNER JOIN smsque SQ ON SQ.mobileid = CM.mobileid".
											" WHERE SQ.clientid = ".$_SESSION[SessionID."ClientID"];
										if (strlen($txtCamName) > 0)
										{
											$Query .= " AND SQ.smsquename LIKE '%".$txtCamName."%'";
										}
										if (isset($_REQUEST['ChkDate']))
										{
											$Query .= " AND (SQ.startdate BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."')";
										}
										// if ($cboDevStatus == 0)
										// {
										// 	$Query .= " AND CM.token != '' OR CM.token = ''";
										// }
										if ($cboDevStatus == 1)
										{
											$Query .= " AND CM.token != ''";
										}
										elseif ($cboDevStatus == 2)
										{
											$Query .= " AND CM.token = ''";
										}
										elseif ($cboDevice > 0)
										{
											$Query .= " AND CM.mobileid = ".$cboDevice;
										}
										elseif ($cboCamStatus == 1)
										{
											$Query .= " AND SQ.status = 0";
										}
										elseif ($cboCamStatus == 2)
										{
											$Query .= " AND SQ.status = 1";
										}
										$Query .= " ORDER BY SQ.smsqueid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->token != "")
												$DeviceStatus = "Device Ready";	
											else
												$DeviceStatus = "Device Not Ready";	
											if ($objRow->status == 0)
											{
												$StatusIcon  = "stop";
												$StatusColor = "btn-danger";
												$StatusTitle = "Click Here To Stop Campaign";
											}
											else
											{
												$StatusIcon  = "play";
												$StatusColor = "btn-success";
												$StatusTitle = "Click Here To Start Campaign";
											}
											// Sms Send
											$Query = "SELECT COUNT(*) As Sent".
												" FROM smsquelist".
												" WHERE smsqueid = ".$objRow->smsqueid;
											if (DBUserName != "myitvh")
											{
												$Query .= " AND smssent IS NOT NULL";
											}
											$rstPro = mysqli_query($Conn,$Query);
											$objPro = mysqli_fetch_object($rstPro);
											$SmsSent = $objPro->Sent;
											// Sms Not Sent
											$Query = "SELECT COUNT(*) As NotSent".
												" FROM smsquelist".
												" WHERE smsqueid = ".$objRow->smsqueid." AND smssent IS NOT NULL";
											$rstPro = mysqli_query($Conn,$Query);
											$objPro = mysqli_fetch_object($rstPro);
											$SmsNotSent = $objPro->NotSent;
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="center"><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->smsquename);?></td>
										<td align="left"  ><?php echo($objRow->mobilename."-".$objRow->mobileno);?></td>
										<td align="left"><?php echo($DeviceStatus);?></td>
										<td align="left"><?php echo(ShowDate($objRow->startdate,1));?></td>
										<td align="left"><?php echo(ShowDate($objRow->closedate,1));?></td>
										<td align="center">
											<div class="btn-group">
												<?php
													if ($objRow->smsquename != "SMS API")
													{
												?>
												<input type="hidden" id="CampStatus<?php echo($Index);?>" value="<?php echo($objRow->status);?>">
												<button type="button" id="btnStatus<?php echo($Index);?>" title="<?php echo($StatusTitle);?>" 
													onclick="StatusCampaign(<?php echo($Index);?>,<?php echo($objRow->smsqueid);?>);" class="btn btn-sm <?php echo($StatusColor);?>" data-toggle="tooltip" data-container="body">
													<i class="fa fa-<?php echo($StatusIcon);?>"></i>
												</button>
												<button type="button" id="btnReset<?php echo($Index);?>" title="Restart Campaign" 
													onclick="RestartCampaign(<?php echo($Index);?>,<?php echo($objRow->smsqueid);?>);" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-repeat"></i>
												</button>
												<button type="button" id="btnDelete<?php echo($Index);?>" title="Delete" 
													onclick="DeleteCampaign(<?php echo($Index);?>,<?php echo($objRow->smsqueid);?>);" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fas fa-trash-alt"></i>
												</button>
												<button type="button" id="btnEdit<?php echo($Index);?>" title="Edit" 
													onclick="EditCampaign(<?php echo($objRow->smsqueid);?>);" class="btn bg-purple btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<?php
													}
												?>
												<button type="button" title="View Campaign" 
													onclick="ViewMessages(<?php echo($objRow->smsqueid);?>);" class="btn btn-primary btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-eye"></i>
												</button>
											</div><!-- /.btn-group -->
											<br>
											<!-- /.progress-group -->
											<div class="progress-group">
												<?php
													$TotalSms = $SmsSent + $SmsNotSent;
													$Percent = 0.00;
													$ProgBarColor = "red";
													if ($TotalSms > 0)
													{
														$Percent = ($SmsSent / $TotalSms * 100);
														if ($Percent == 100)
														{
															$ProgBarColor = "green";
														}
													}
													if (DBUserName == "myitvh")
													{
														$TotalSms = $SmsSent;
														if ($TotalSms > 0)
														{
															$Percent = ($SmsSent / $TotalSms * 100);
															if ($Percent == 100)
															{
																$ProgBarColor = "green";
															}
														}
													}	
												?>
												<span class="text-bold">SMS &nbsp; <?php echo(number_format($SmsNotSent));?> &nbsp; / &nbsp; <?php echo(number_format($TotalSms));?></span>
											</div>
											<div class="progress-group">	
												<div class="progress progress-sm">
													<div class="progress-bar progress-bar-<?php echo($ProgBarColor);?>" aria-valuenow="<?php echo($Percent);?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo($Percent."%");?>"></div>
												</div>
											</div>
										</td>
									</tr>
									<?php
										}
									?>
								</tbody>
							</table>
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
		//Init DataTable
		$('#MyDataTable').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": true,
			"autoWidth": false,
			"iDisplayLength": 50,
			"scrollX": true
		});
	});
	function StatusCampaign(Index,CampaignID)
	{
		var Status = $("#CampStatus"+Index).val();
		if (Status == 0)
			Msg = "Stop";
		else
			Msg = "Start";
		Msg = "Are You Sure You Want To "+Msg+" This Campaign ?";
		$.confirm({
			title: "Confirm!",
			content: Msg,
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
									url: "<?php echo($PagePath);?>pages/ajaxs/campaign-status",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"CampaignID": CampaignID,
										"Status": Status
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
									if (Status == 0)
									{
										$("#btnStatus"+Index).removeClass("btn-danger");
										$("#btnStatus"+Index).addClass("btn-success");
										$("#btnStatus"+Index).html("<i class=\"fa fa-play\"></i>");
										$("#CampStatus"+Index).val("1");
									}
									else
									{
										$("#btnStatus"+Index).removeClass("btn-success");
										$("#btnStatus"+Index).addClass("btn-danger");
										$("#btnStatus"+Index).html("<i class=\"fa fa-stop\"></i>");
										$("#CampStatus"+Index).val("0");
									}
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
	function DeleteCampaign(Index,CampaignID)
	{
		<?php CheckRight("Delete","ShowError");?>
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Delete This Campaign & All of its SMS ?",
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
									url: "<?php echo($PagePath);?>pages/ajaxs/campaign-delete",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"CampaignID": CampaignID
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
	function RestartCampaign(Index,CampaignID)
	{
		var Box = $.confirm({
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: 'col-md-6 col-md-offset-3',
			content: function () {
				var self = this;
				return $.ajax({
					url: "<?php echo($PagePath);?>pages/ajaxs/campaign-restart",
					dataType: "JSON",
					method: "POST",
					timeout: 3000,
					data: {
						"Restart": "Check",
						"CampaignID": CampaignID
					}
					}).done(function (response) {
						if (response.Status == "Yes")
						{
							Box.close();
							RestartCampaignApply(CampaignID);
						}
						else
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
					btnClass: "btn-blue"
				}
			},
			onClose: function () {
			}
		});
	}
	function RestartCampaignApply(CampaignID)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Restart This Campaign & All of its SMS ?",
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
									url: "<?php echo($PagePath);?>pages/ajaxs/campaign-restart",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"Restart": "Apply",
										"CampaignID": CampaignID
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
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "down";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>
