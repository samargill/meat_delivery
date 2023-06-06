<?php
	$PageID = array(3,2,0);
	$PagePath = "../../";
	$PageMenu = "Capmpaigns";
	$PageSubMenu ="View campaign";
	$PageTitle= "View messages";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	$SearchBy = "";
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
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script type="text/javascript">
		function Verify()
		{
			if (document.getElementById('cboSearch').value != 0)
			{
				if (document.getElementById('txtSearch').value == "")
				{
					ShowError(true,"Error!","Please Enter Search text",undefined,"txtSearchBy");
					return(false);
				}
			}
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
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
							<li class="breadcrumb-item active"><?php echo($PageSubMenu);?></li>
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
					<form name="Form" role="form" action="message-view" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Search By :</label>
										<select name="cboSearch" id="cboSearch" CLASS="form-control select2" style="width: 100%;">
											<option value="0">-- Select --</option>
											<option value="1" <?php if ($cboSearch == 1) echo("selected");?>>Mobile No</option>
											<option value="2" <?php if ($cboSearch == 2) echo("selected");?>>SMS Text</option>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search Text</label>
										<input type="text" name="txtSearch" id="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Date Range :</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-clock-o"></i>&nbsp;&nbsp;
													<!-- <input type="checkbox" name="ChkDate" <?php //if (isset($_REQUEST['ChkDate'])) echo("CHECKED");?>> -->
												</span>
											</div>
											<input type="text" name="cboDate" id="cboDate" readonly class="form-control pull-right" style="background-color:#FFFFFF;">
											<input type="hidden" name="txtStartDate" value="<?php echo($txtStartDate);?>">
											<input type="hidden" name="txtCloseDate" value="<?php echo($txtCloseDate);?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row py-2">
								<div class="col-md-2">
									<input type="hidden" name="SmsQueID" id="SmsQueID" value="<?php echo($_REQUEST['SmsQueID']);?>">
									<button type="submit" name="btnSearch" class="btn btn-primary" onclick="return Verify();">Search</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="12%" style="text-align:left;"  >Send To </th>
										<th width="54%" style="text-align:left;"  >Text Message</th>
										<th width="12%" style="text-align:left;"  >SMS Add Time</th>
										<th width="12%" style="text-align:left;"  >Delivered Time</th>
										<th width="5%"  style="text-align:center; min-width:50px;">-</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec = 50;
										$Page = (isset($_REQUEST['Page'])) ? $_REQUEST['Page'] : 1;
										$PageLink    = "message-view";
										$PageParam   = "SmsQueID=".$_REQUEST['SmsQueID']."&cboSearch=".$cboSearch.
											"&txtSearch=".$txtSearch."&txtStartDate=".$txtStartDate."&txtCloseDate=".$txtCloseDate;
										$QuerySelect = "SELECT smsid, mobile, smstext, smsaddtime, smssent";
										$QueryJoin   = " FROM smsquelist ";
										$QueryWhere = "".
											" WHERE smsqueid = ".$_REQUEST['SmsQueID']."".
											" AND (smsaddtime BETWEEN '".$txtStartDate." 00:00:00' AND '".$txtCloseDate." 23:59:59')";
										if ($cboSearch > 0)
										{
											if ($cboSearch == 1)
											{ 
												$QueryWhere .= " AND mobile LIKE '%".$txtSearch."%'";
											}
											elseif ($cboSearch == 2)
											{
												$QueryWhere .= " AND smstext LIKE '%".$txtSearch."%'";
											}
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total  = $objRow->Total;
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere.
											" ORDER BY smssent DESC".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="center"><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->mobile);?></td>
										<td align="left"  ><textarea style="width: 100%;" rows="3" cols="10"><?php echo($objRow->smstext);?></textarea></td>
										<td align="left"  ><?php echo(ShowDate($objRow->smsaddtime,1));?></td>
										<td align="left"  ><?php echo(ShowDate($objRow->smssent,1));?></td>
										<td align="center">
											<div class="btn-group">
											<?php
												$Disabled   = "";
												$UserPkgID	= intval(GetValue("pkgtype", "client", "clientid = ".$_SESSION[SessionID."ClientID"]));
												if ($UserPkgID == 9)
												{
													$Disabled = "disabled";
												}
											?>		
												<button type="button" title="Delete SMS From Campaign" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body" 
													onclick="DeleteSms(<?php echo($Index);?>,<?php echo($objRow->smsid);?>);" <?php echo($Disabled); ?>>
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
						<input type="hidden" name="SmsQueID" id="SmsQueID" value="<?php echo($_REQUEST['SmsQueID']);?>">
					</form>
				</div>
			</div>	
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function() {
		//Init Select2
		$(".select2").select2();
	});
	function DeleteSms(Index,SmsID)
	{
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Remove This Message From Campaign ?",
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
									url: "<?php echo($PagePath);?>pages/ajaxs/campaign-delete-sms",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"SmsID": SmsID,
										"SmsQueID": $("#SmsQueID").val()
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
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "down";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>