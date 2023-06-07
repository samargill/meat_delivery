<?php
	$PageID = array(1,0,0);
	$PagePath = "../../";
	$PageMenu = "View Clients Country";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
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
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper" style="margin-left: 0px !important;">
		<!-- Page Header -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageMenu);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="client-view-country" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Country Name";
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
								</div>	
							</div>
							<div class="row mb-2">
								<div class="col-md-3">
									<button type="submit" name="btnSearch" class="btn btn-primary" onclick="return Verify();">
										<i class="fa fa-search"></i> &nbsp; Search
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="6%"  style="text-align:left;">Sr #</th>
										<th width="30%" style="text-align:left;">Country Name</th>
										<th width="15%" style="text-align:center;">Country Code</th>
										<th width="15%" style="text-align:center;">Total Clients</th>
										<th width="17%" style="text-align:center;">First Client</th>
										<th width="17%" style="text-align:center;">Last Client</th>
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
										$PageLink = "client-view-country";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect = "SELECT MIN(adddate) AS StartDate, MAX(adddate) AS LastDate,".
											" COUNT(C.countryid) AS CountryCount, C.clientid, C.clientname,".
											" AC.countryid, AC.countryname, C.adddate";
										$QueryJoin 	 = "".
											" FROM client C".
											" INNER JOIN address_country AC ON C.countryid = AC.countryid";
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
												$QueryWhere .= " AND AC.countryname LIKE '%".$txtSearch."%'";
										}
										$QueryGroup = "".
											" GROUP BY C.countryid";
										$QueryORDER = "".
											" ORDER BY CountryCount DESC";										
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere." ".$QueryGroup;
										$rstRow = mysqli_query($Conn,$Query);
										$Total  = mysqli_num_rows($rstRow);
										$objRow = mysqli_fetch_object($rstRow);
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere." ".$QueryGroup." ".$QueryORDER;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
									?>
									<tr id="Row<?php echo($Index);?>" >
										<td align="left"  ><?php echo($Index);?></td>
										<td align="left"  ><?php echo($objRow->countryname);?></td>
										<td align="center"><?php echo($objRow->countryid);?></td>
										<td align="center"><?php echo($objRow->CountryCount);?></td>
										<td align="center"><?php echo(ShowDate($objRow->StartDate,4));?></td>
										<td align="center"><?php echo(ShowDate($objRow->LastDate,4));?></td>
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
	function Verify()
	{
		if (document.Form.cboSearch.value > 0)
		{
			if (IsEmpty(document.Form.txtSearch.value) == true &&  document.Form.cboSearch.value == 1)
			{
				ShowError(true,"Error!","Please Enter Country Name First.",undefined,"txtSearch");
				return(false);
			}
		}
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