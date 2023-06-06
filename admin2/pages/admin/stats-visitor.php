<?php
	$PageID = array(9,0,0);
	$PagePath = "../../";
	$PageMenu = "Visitor Stats View";
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
		$txtStartDate = date("Y-m-d"). " 00:00:00";
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d")." 23:59:59";
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
	<script type="text/javascript">
		function VisitorGraph()
		{
			var Win = Popup("user-visitor-graph","KS_BullkySms_View",740,1024,100,100);
			Win.focus();
		}
		function StatsDetails(StatID)
		{
			var Win = Popup("stats-visitor-view-detail?StatID="+StatID,"KS_BullkySms_View",740,1024,100,100);
			Win.focus();
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini sidebar-collapse">
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
					<form name="Form" role="form" action="stats-visitor" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "IP Address";
											$ComboData[] = "Country Name";
											$ComboData[] = "User Agent";
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
							</div>

							<div class="row mb-2">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary" onclick="return Verify();">
										<i class="fa fa-search"></i> &nbsp; Search Visitor Stats
									</button>
								</div>
								<div class="col-md-4">
									<button type="button" title="Detail" onclick="VisitorGraph();" class="btn btn-primary">
										<i class="fa fa-eye"></i> &nbsp; View Visitor Graph 
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th width="4%"  style="text-align:left;"  >Sr #</th>
										<th width="10%" style="text-align:center;">Stats date</th>
										<th width="14%" style="text-align:center;">IP Adress</th>
										<th width="22%" style="text-align:center;">Referer</th>
										<th width="22%" style="text-align:center;">Request Src</th>
										<th width="8%" style="text-align:center;" >Pages Visited</th>
										<th width="10%" style="text-align:center;">Last Visit</th>
										<th width="5%" style="text-align:center;" >Country</th>
										<th width="5%" style="text-align:center;">UserAgent</th>
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
										$PageLink = "stats-visitor";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch.
											"&txtStartDate=".$txtStartDate."&txtCloseDate=".$txtCloseDate;
										$QuerySelect = "SELECT SUM(SD.visitcount) AS TtlVisits,".
											" S.statid, S.ipaddress, S.referer, S.request, S.useragent,".
											" SD.statdate, SD.lastvisit,".
											" AC.countryname, AC.codeiso3";
										$QueryJoin 	 = "".									
											" FROM zstatsdetail SD".
											" INNER JOIN zstats S ON SD.statid = S.statid".
											" INNER JOIN address_country AC ON S.countryid = AC.countryid";
										$QueryWhere  = "".
											" WHERE 1 = 1".
											" AND SD.statdate BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."'";
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 1)
												$QueryWhere .= " AND S.ipaddress LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 2)
												$QueryWhere .= " AND AC.countryname LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 3)
												$QueryWhere .= " AND S.useragent LIKE '%".$txtSearch."%'";
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere.
											" GROUP BY SD.statid";
										$rstRow = mysqli_query($Conn,$Query);
										$Total  = mysqli_num_rows($rstRow);
										$objRow = mysqli_fetch_object($rstRow);
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere."".
											" GROUP BY SD.statid".
											" ORDER BY SD.statdate DESC". 
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											$PageRequest = $objRow->request == "/" ? "/index" : $objRow->request;
											$IPAddLength = strlen($objRow->ipaddress);
											if ($IPAddLength > 20)
											{
												$IPAddress = explode(":", $objRow->ipaddress);
												$IPAddress = array_chunk($IPAddress, ceil(count($IPAddress) / 2));
												$IPAddress = implode(":",$IPAddress[0]).":<br>".implode(":", $IPAddress[1]);
											}
											else
											{
												$IPAddress = $objRow->ipaddress;
											}
	 								?>
									<tr id="Row<?php echo($Index);?>">
										<td align="left"><?php echo($objRow->statid);?></td>
										<td align="center"><?php echo(ShowDate($objRow->statdate,4));?></td>
										<td align="center"><?php echo($IPAddress);?></td>
										<td align="left"><textarea style="width:100%;" disabled="disabled"><?php echo($objRow->referer);?></textarea></td>
										<td align="left"><textarea style="width:100%;" disabled="disabled"><?php echo($PageRequest);?></textarea></td>
										<td align="center"><?php echo($objRow->TtlVisits);?></td>
										<td align="center"><?php echo(ShowDate($objRow->lastvisit,4));?></td>
										<td align="center"><span title="<?php echo($objRow->countryname);?>"><?php echo($objRow->codeiso3);?></span></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" title="Stats Details" onclick="StatsDetails(<?php echo($objRow->statid);?>);" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-user"></i>
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
	function Verify()
	{
		if (document.Form.cboSearch.value > 0)
		{
			if (IsEmpty(document.Form.txtSearch.value) == true &&  document.Form.cboSearch.value == 1)
			{
				ShowError(true,"Error!","Please Enter IP Address First.",undefined,"txtSearch");
				return(false);
			}
			if (IsEmpty(document.Form.txtSearch.value) == true &&  document.Form.cboSearch.value == 2)
			{
				ShowError(true,"Error!","Please Enter Country Name First.",undefined,"txtSearch");
				return(false);
			}
			if (IsEmpty(document.Form.txtSearch.value) == true &&  document.Form.cboSearch.value == 3)
			{
				ShowError(true,"Error!","Please Enter User Agent First.",undefined,"txtSearch");
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