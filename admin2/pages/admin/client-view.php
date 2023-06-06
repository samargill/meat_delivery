<?php
	$PageID = array(1,0,0);
	$PagePath = "../../";
	$PageMenu = "Clients";
	$PageName = "View Clients";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

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
		$txtStartDate = date("Y-m-d");
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d");
	if (isset($_REQUEST['cboPackage']))
		$cboPackage = $_REQUEST['cboPackage'];
	else
		$cboPackage = 0;
	if (isset($_REQUEST['cboStatus']))
		$cboStatus = $_REQUEST['cboStatus'];
	else
		$cboStatus = 0;
	$UrlParams = "&cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>../plugins/intl-mobile/css/intlTelInput.css">
	<script type="text/javascript">
		function ClientDetail(ClientID)
		{
			<?php CheckRight("View","ShowError");?>
			var Win = Popup("client-detail?ClientID="+ClientID,"KS_BullkySms_View",740,1024,100,100);
			Win.focus();
		}
		function ClientCountry()
		{
			<?php CheckRight("View","ShowError");?>
			var Win = Popup("client-view-country","KS_BullkySms_View",740,1024,100,100);
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
		<!-- Page Header BreadCrumb -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageName);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
							<li class="breadcrumb-item active"><?php echo($PageName);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="client-view" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Client Name";
										?>
										<?php
											DBComboArray("cboSearch",$ComboData,0,$cboSearch,"form-control select2","");
										?>
									</div>
									<div class="form-group">
										<label for="txtSearch">Search Text :</label>
										<input type="text" name="txtSearch" id="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Search By Date :</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<input type="checkbox" name="ChkDate" <?php if (isset($_REQUEST['ChkDate'])) echo("CHECKED");?>>
												</span>
											</div>
											<input type="text" name="cboDate" id="cboDate" readonly class="form-control pull-right" style="background-color:#FFFFFF;">
											<input type="hidden" name="txtStartDate" value="<?php echo($txtStartDate);?>">
											<input type="hidden" name="txtCloseDate" value="<?php echo($txtCloseDate);?>">
										</div>
									</div>
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Verified";
											$ComboData[] = "Not Verified";
											$ComboData[] = "Active";
											$ComboData[] = "Expired";
										?>
										<label>Search By :</label>
										<?php
											DBComboArray("cboStatus",$ComboData,0,$cboStatus," form-control select2","style=\"width: 100%;\"");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>By Package :</label>
										<?php
											DBCombo("cboPackage","package","pkgid","pkgname","WHERE ispkg = 1",$cboPackage,"-- Show All --","form-control select2","style=\"width: 100%;\"");
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4 p-2">
									<button type="submit" name="btnSearch" class="btn btn-primary">
										<i class="fa fa-search"></i> &nbsp; Search Clients
									</button>
								</div>
								<div class="col-md-4 p-2">
									<button type="button" title="Detail" onclick="ClientCountry();" class="btn btn-primary">
										<i class="fa fa-eye"></i> &nbsp; Clients Country
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="20%" style="text-align:left;"  >Client</th>
										<th width="23%" style="text-align:left;"  >Signup Email</th>
										<th width="6%"  style="text-align:center;">Country</th>
										<th width="8%"  style="text-align:center;">Package</th>
										<th width="8%"  style="text-align:center;">Expiry</th>
										<th width="10%" style="text-align:center;">Signup Date</th>
										<th width="10%" style="text-align:center;">Last Edit</th>
										<th width="10%" style="text-align:center; min-width:110px;">-</th>
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
										$PageLink = "client-view";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect = "SELECT C.clientid, C.clientname, CU.email,".
											" P.pkgname, C.pkgexpiry, AC.codeiso3, AC.countryname, C.adddate, C.lastedit, CU.verifydate";
										$QueryJoin 	 = "".
											" FROM client C".
											" INNER JOIN clientuser CU ON C.clientid = CU.clientid AND CU.usertype = 1".
											" INNER JOIN package P ON C.pkgtype = P.pkgid".
											" LEFT OUTER JOIN address_country AC ON C.countryid = AC.countryid";
										$QueryWhere  = "".
											" WHERE 1 = 1";
										if (isset($_REQUEST['ChkDate']))
										{
											$QueryWhere .= "".
												" AND C.adddate BETWEEN '".$txtStartDate." 00:00:00' AND '".$txtCloseDate." 23:59:59'";
										}
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 1)
												$QueryWhere .= " AND C.clientname LIKE '%".$txtSearch."%'";
										}
										if ($cboPackage > 0)
										{
											$QueryWhere .= " AND C.pkgtype = ".$cboPackage;
										}
										if ($cboStatus > 0)
										{
											if ($cboStatus == 1)
												$QueryWhere .= " AND CU.verifydate IS NOT NULL";
											elseif ($cboStatus == 2)
												$QueryWhere .= " AND CU.verifydate IS NULL";
											elseif ($cboStatus == 3)
												$QueryWhere .= " AND (C.pkgexpiry IS NULL OR C.pkgexpiry > SUBSTRING(NOW(),1,10))";
											elseif ($cboStatus == 4)
												$QueryWhere .= " AND C.pkgexpiry < SUBSTRING(NOW(),1,10)";
										}
										$Query = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total = $objRow->Total;
										$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere." ORDER BY C.clientid DESC";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->verifydate == NULL)
												$BGColor = "bgcolor=\"#e8d5d6\"";
											else
												$BGColor = "";
									?>
									<tr id="Row<?php echo($Index);?>" <?php echo($BGColor);?>>
										<td align="left"  ><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->clientname);?></td>
										<td align="left"  ><?php echo($objRow->email);?></td>
										<td align="center"><span title="<?php echo($objRow->countryname);?>"><?php echo($objRow->codeiso3);?></span></td>
										<td align="center"><?php echo($objRow->pkgname);?></td>
										<td align="center"><?php echo(ShowDate($objRow->pkgexpiry,0));?></td>
										<td align="center"><?php echo(ShowDate($objRow->adddate,4));?></td>
										<td align="center"><?php echo(ShowDate($objRow->lastedit,4));?></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" title="Detail" onclick="ClientDetail(<?php echo($objRow->clientid);?>);" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-eye"></i>
												</button>
												<button type="button" title="Edit" onclick="ClientEdit(<?php echo($Index);?>,<?php echo($objRow->clientid);?>);" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" title="Delete" onclick="ClientDelete(<?php echo($Index);?>,<?php echo($objRow->clientid);?>);" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-trash"></i>
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
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function () {
		//Init Select2
		$(".select2").select2();
	});
	function ClientDelete(Index,ClientID)
	{
		<?php CheckRight("Delete","ShowError");?>
		var DelStatus = "";
		$.confirm({
			columnClass: 'col-md-6 col-md-offset-3',
			content: function () {
				var self = this;
				return $.ajax({
					url: "../ajaxs/admin/client-delete",
					dataType: "JSON",
					method: 'POST',
					data: {
						"ClientID": ClientID,
						"Validate":""
					}
					}).done(function (response) {
						self.setTitle(response.Status);
						self.setContent(response.Message);
						DelStatus = response.ReturnID;
						if (DelStatus == "No")
						{
							self.buttons.confirm.setText("OK");
							self.buttons.cancel.hide();
						}
					}).fail(function(jqXHR,exception) {
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"confirm": {
					text: "Yes",
					btnClass: "btn-blue",
					keys: ['enter'],
					action: function() {
						if (DelStatus == "Yes")
						{
							ClientDeleteApply(Index,ClientID,DelStatus);
						}
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
				}
			}
		});
	}
	function ClientDeleteApply(Index,ClientID,Status)
	{
		<?php CheckRight("Delete","ShowError");?>
		var Flag = "";
		$.confirm({
			content: function () {
				var self = this;
				return $.ajax({
					url: "../ajaxs/admin/client-delete",
					dataType: "JSON",
					method: 'POST',
					data: {
						"ClientID": ClientID,
						"Apply": Status
					}
					}).done(function (response) {
						Flag = response.Status;
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
					btnClass: "btn-blue",
					keys: ['enter'],
					action: function() {
						if (Flag == "Done")
						{
							$("#DataRow"+Index).hide();
						}
					}
				}
			},
		});
	}
</script>
<?php
	$GLOBALS["DateRangePickerSingle"]     = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD";
	$GLOBALS["DateRangePickerAlign"]      = "left";
	$GLOBALS["DateRangePickerVAlign"]     = "down";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>