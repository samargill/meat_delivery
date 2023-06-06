<?php
	$PageID = array(8,0,0);
	$PagePath = "../../";
	$PageMenu = "App Download Stats";
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
		$txtStartDate = date("Y-m-d"). "00:00:00";
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d")."23:59:59";
	if (isset($_REQUEST['cboSource']))
		$cboSource = $_REQUEST['cboSource'];
	else
		$cboSource = 0;
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
					<form name="Form" role="form" action="stats-app" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "IP Address";
										?>
										<?php
											DBComboArray("cboSearch",$ComboData,0,$cboSearch,"form-control select2","style=\"width: 100%;\"");
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
								<div class="col-md-4">
									<div class="form-group">
										<label>Download Source :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "From Data";
											$ComboData[] = "From Application";
											DBComboArray("cboSource",$ComboData,0,$cboSource," form-control select2","style=\"width: 100%;\"");
										?>
									</div>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary" onclick="return Verify();">
										<i class="fa fa-search"></i> &nbsp; Search App Stats
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th width="6%"  style="text-align:left;" >Sr #</th>
										<th width="15%" style="text-align:center;" >Dated</th>
										<th width="14%" style="text-align:center;" >App Type</th>
										<th width="25%" style="text-align:center;" >IP Adress</th>
										<th width="40%" style="text-align:left;" >UserAgent</th>
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
										$PageLink = "stats-app";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect = "SELECT id, logdate, apptype, ipaddress, useragent";
										$QueryJoin 	 = "".									
											" FROM zlog";  
										$QueryWhere  = "".
											" WHERE 1 = 1";
										if (isset($_REQUEST['ChkDate']))
										{
											$QueryWhere .= "".
												" AND logdate BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."'";
										}
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 1)
												$QueryWhere .= " AND ipaddress LIKE '%".$txtSearch."%'";
										}
										if ($cboSource > 0)
										{
											if ($cboSource == 1)
												$QueryWhere .= " AND apptype = 0";
											elseif ($cboSource == 2)
												$QueryWhere .= " AND apptype = 1";
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere ;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total  = $objRow->Total;
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere."".
											" ORDER BY logdate DESC". 
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											$AppType = $objRow->apptype == 0 ? "Data" : "App"; 
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="left"><?php echo($objRow->id);?></td>
										<td align="center"><?php echo(ShowDate($objRow->logdate,4));?></td>
										<td align="center"><?php echo($AppType);?></td>
										<td align="center"><?php echo($objRow->ipaddress);?></td>
										<td align="left"><textarea style="width:100%;" disabled="disabled"><?php echo($objRow->useragent);?></textarea></td>
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
	function Verify()
	{
		if (document.Form.cboSearch.value > 0)
		{
			if (IsEmpty(document.Form.txtSearch.value) == true &&  document.Form.cboSearch.value == 1)
			{
				ShowError(true,"Error!","Please Enter IP Address First.",undefined,"txtSearch");
				return(false);
			}
		}
	}
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"]  = "right";
	$GLOBALS["DateRangePickerVAlign"] = "down";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>