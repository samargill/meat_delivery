<?php
	$PageID = array(3,4,0);
	$PagePath = "../../";
	$PageMenu = "Campaigns";
	$PageTitle= "Conversation";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['cboDeviceStatus']))
		$cboDeviceStatus = $_REQUEST['cboDeviceStatus'];
	else
		$cboDeviceStatus = -1;
	if (isset($_REQUEST['cboSearchBy']))
		$cboSearchBy = $_REQUEST['cboSearchBy'];
	else
		$cboSearchBy = 0;
	if (isset($_REQUEST['txtSearchBy']))
		$txtSearchBy = $_REQUEST['txtSearchBy'];
	else
		$txtSearchBy = "";

	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
		$txtStartDate = date("Y-m-d 00:00:00",strtotime("Last Month"));
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d 23:59:59");
?>
<!DOCTYPE html>
<html>
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
					case 1:
						$Message = "Conversation Deleted Successfully ...";
						break;
					case 101:
						$Message = "Unable To Perform Operation - Fatal Error ...";
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
					<form name="Form" role="form" action="conversation" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Search By Devices :</label>
										<?php
											// DBCombo("cboDevice","clientmobile","clientmobid","CONCAT(mobilename,' - ',mobileno)",
											// 	"WHERE clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
											// 	"form-control select2","onchange=\"\" style=\"width: 100%;\"");
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.clientid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Select Device --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search By Status :</label>
										<select name="cboDeviceStatus" id="cboDeviceStatus" CLASS="form-control select2" style="width: 100%;">
											<option value="-1">-- Select Status --</option>
											<option value="0" <?php if ($cboDeviceStatus == 0) echo "SELECTED" ?>>-- Opt-In --</option>
											<option value="1" <?php if ($cboDeviceStatus == 1) echo "SELECTED" ?>>-- Opt-Out --</option>
										</select>
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
										<label>Search By :</label>
										<select name="cboSearchBy" id="cboSearchBy" CLASS="form-control select2" style="width: 100%;">
											<option value="0">-- Select --</option>
											<option value="1" <?php if ($cboSearchBy == 1) echo "SELECTED" ?>>Mobile No</option>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search Text</label>
										<input type="text" name="txtSearchBy" value="<?php echo($txtSearchBy);?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row mb-2">
								<div class="col-md-2">
									<button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="6%"  style="text-align:center;">Sr #</th>
										<th width="16%" style="text-align:left;">From</th>
										<th width="58%" style="text-align:left;">Message</th>
										<th width="10%" style="text-align:left;">Date</th>
										<th width="10%" style="text-align:left;"></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec = 50;
										$Page = (isset($_REQUEST['Page'])) ? $_REQUEST['Page'] : 1;
										$PageLink = "conversation";
										$PageParam = "cboSearchBy=".$cboSearchBy."&cboDevice=".$cboDevice."&cboDeviceStatus=".$cboDeviceStatus;
										if (strlen($txtSearchBy) > 0)
										{
											$PageParam .= "&txtSearchBy=".$txtSearchBy;
										}
										if (isset($_REQUEST['ChkDate']))
										{
											$PageParam .= "&ChkDate&txtStartDate=".$txtStartDate."&txtCloseDate=".$txtCloseDate;
										}
										// $QueryRec = "SELECT MAX(SmsR.smsid) As SmsID, 'R' As SmsType,".
										// 	" MAX(SmsR.smsdate) As SmsDate, SmsR.smstext As SmsText,".
										// 	" SmsR.mobile As Mobile, CC.fullname, SmsR.optout As OptOut".
										// 	" FROM smsreclist SmsR".
										// 	" INNER JOIN clientmobile CM ON SmsR.clientmobid = CM.clientmobid".
										// 	" LEFT OUTER JOIN clientcontact CC ON SmsR.mobile = CC.mobile".
										// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"].
										// 	" [QueryWhere]".
										// 	" GROUP BY Mobile, SmsType";
										// $QuerySen = "SELECT MAX(SmsL.smsid) As SmsID, 'S' As SmsType,".
										// 	" MAX(SmsL.smssent) As SmsDate, SmsL.smstext As SmsText,".
										// 	" SmsL.mobile As Mobile, CC.fullname, 0 As OptOut".
										// 	" FROM smsque SmsQ".
										// 	" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
										// 	" INNER JOIN clientmobile CM ON SmsQ.clientmobid = CM.clientmobid".
										// 	" LEFT OUTER JOIN clientcontact CC ON SmsL.mobile = CC.mobile".
										// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsL.getapp = 1".
										// 	" [QueryWhere]".
										// 	" GROUP BY Mobile, SmsType";
										$QueryRec = "SELECT MAX(SmsR.smsid) As SmsID, 'R' As SmsType,".
											" MAX(SmsR.smsdate) As SmsDate, SmsR.smstext As SmsText,".
											" SmsR.mobile As Mobile, CC.fullname, SmsR.optout As OptOut".
											" FROM smsreclist SmsR".
											" INNER JOIN clientmobile CM ON SmsR.mobileid = CM.mobileid".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" LEFT OUTER JOIN clientcontact CC ON SmsR.mobile = CC.mobile".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"].
											" [QueryWhere]".
											" GROUP BY Mobile, SmsType";
										$QuerySen = "SELECT MAX(SmsL.smsid) As SmsID, 'S' As SmsType,".
											" MAX(SmsL.smssent) As SmsDate, SmsL.smstext As SmsText,".
											" SmsL.mobile As Mobile, CC.fullname, 0 As OptOut".
											" FROM smsque SmsQ".
											" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
											" INNER JOIN clientmobile CM ON SmsQ.mobileid = CM.mobileid".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" LEFT OUTER JOIN clientcontact CC ON SmsL.mobile = CC.mobile".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsL.getapp = 1".
											" [QueryWhere]".
											" GROUP BY Mobile, SmsType";
										$QueryWhere  = "";
										if (isset($_REQUEST['ChkDate']))
										{
											$QueryWhere .= " AND ([SmsDate] BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."')";
										}
										if ($cboDevice > 0)
										{
											$QueryWhere .= " AND CM.mobileid = ".$cboDevice;
										}
										if ($cboSearchBy == 1)
										{
											$QueryWhere .= " AND Mobile LIKE '%".$txtSearchBy."%'";
										}
										$QueryRec = str_replace("[SmsDate]","SmsR.smsdate",str_replace("[QueryWhere]", $QueryWhere, $QueryRec));
										$QuerySen = str_replace("[SmsDate]","SmsL.smssent",str_replace("[QueryWhere]", $QueryWhere, $QuerySen));
										$QuerySelect = "";
										if ($cboDeviceStatus == 1)
										{
											$QuerySelect .= $QueryRec;
										}
										else
										{
											$QuerySelect .= $QueryRec." UNION ".$QuerySen;
										}
										$QueryWhere = " WHERE 1";
										if ($cboDeviceStatus >= 0)
										{
											$QueryWhere.= " AND OptOut = ".$cboDeviceStatus;
										}
										$Total  = 0;
										$Query  = "SELECT COUNT(*) As Total FROM (".$QuerySelect.") As Temp".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										if (mysqli_num_rows($rstRow) > 0)
										{
											$objRow = mysqli_fetch_object($rstRow);
											$Total = $objRow->Total;
										}
										$Query = "SELECT * FROM (".$QuerySelect.") As Temp".$QueryWhere;
										$Query .= " GROUP BY Mobile".
											" ORDER BY SmsDate DESC".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											$BgColor = "";
											if ($objRow->OptOut == 1)
											{
												$BgColor = "style=\"background-color: #E8D5D6;\"";
											}
											$Contact = ($objRow->fullname != null) ? $objRow->fullname : $objRow->Mobile;
									?>
									<tr id="Row<?php echo($Index);?>" <?php echo($BgColor);?>>
										<td align="center"><?php echo($Index);?></td>
										<td align="left"  ><?php echo($Contact);?></td>
										<td align="left"  ><?php echo($objRow->SmsText);?></td>
										<td align="left"  ><?php echo(date("d-M-Y H:i",strtotime($objRow->SmsDate)));?></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" title="Delete" onclick="DeleteRecord('<?php echo($Index);?>','<?php echo($objRow->Mobile);?>');" 
													class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fas fa-trash-alt"></i>
												</button>
												<?php
													$Unseen = GetValue("COUNT(smsid) As Total","smsreclist","mobile = ".$objRow->Mobile." AND seen = 0");
													if ($Unseen > 0)
													{
														$btnColor = "#8D021F";
													}
													else
													{
														$btnColor = "#FFFFFF";
														$Unseen = 0;
													}
												?>
												<button type="button" title="Conversation" onclick="ViewConvers('<?php echo($objRow->SmsType."-".$objRow->SmsID);?>');" 
													class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-envelope" style="color:<?php echo($btnColor);?>">
														&nbsp;&nbsp;<?php echo($Unseen);?>
													</i>
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
						<input type="hidden" name="PageParam" value="<?php echo($PageParam); ?>">
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
	function DeleteRecord(Index,MobileNo)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Delete Complete Conversation ?",
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
									url: "conversation",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"Delete": "",
										"MobileNo": MobileNo
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