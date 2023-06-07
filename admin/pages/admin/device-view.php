<?php
	$PageID = array(2,0,0);
	$PagePath = "../../";
	$PageMenu = "SMS Devices";
	$PageName = "View Devices";
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
	if (isset($_REQUEST['cboStatus']))
		$cboStatus = $_REQUEST['cboStatus'];
	else
		$cboStatus = 0;
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>../plugins/intl-mobile/css/intlTelInput.css">
	<script type="text/javascript">
		function DeviceDetail(DeviceID)
		{
			var Win = Popup("device-detail?DeviceID="+DeviceID,"KS_BullkySms_View",740,1024,100,100);
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
		<!-- Page Header -->
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
					<form name="Form" role="form" action="device-view" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "Mobile No";
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
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "Show All";
											$ComboData[] = "Registered";
											$ComboData[] = "Not Registered";
										?>
										<?php
											DBComboArray("cboStatus",$ComboData,0,$cboStatus,"form-control select2","");
										?>
									</div>
								</div>
							</div>
							<div class="row mb-2">
								<div class="col-md-3">
									<button type="submit" name="btnSearch" class="btn btn-primary">
										<i class="fa fa-search"></i> &nbsp; Search Devices
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<!-- <th width="16%" style="text-align:left;"  >Client</th> -->
										<th width="12%" style="text-align:left;"  >Device Name</th>
										<th width="13%"  style="text-align:left;"  >Device No</th>
										<th width="13%"  style="text-align:center;">Max Slot</th>
										<th width="13%" style="text-align:center;">Used Slot</th>
										<th width="10%"  style="text-align:center;">Dev Status</th>
										<th width="11%" style="text-align:center;">Add Date</th>
										<th width="11%" style="text-align:center;">Last Edit</th>
										<th width="8%"  style="text-align:center; min-width:80px;">-</th>
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
										$QuerySelect = "SELECT CM.mobileid, CM.mobileno, CM.mobilecode,".
											" CM.mobilename, CM.maxslot, CM.usedslot,".
											" CM.token, CM.adddate, CM.lastedit";
										$QueryJoin = "".
											// " FROM clientmobile CM".
											// " INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											// " INNER JOIN client C ON CHM.clientid = C.clientid";
											" FROM clientmobile CM";
										$QueryWhere  = "".
											" WHERE 1 = 1";
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 0)
												$QueryWhere .= " AND CM.mobileno LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 1)
												$QueryWhere .= " AND CM.mobilename LIKE '%".$txtSearch."%'";
										}
										if ($cboStatus > 0)
										{
											if ($cboStatus == 1)
												$QueryWhere .= " AND CM.token <> ''";
											else
												$QueryWhere .= " AND CM.token = ''";
										}
										$Query = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total = $objRow->Total;
										$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere.
											" ORDER BY CM.mobileid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->token != "")
												$DeviceStatus = "<i class=\"fa fa-check text-green text-bold\"></i>";
											else
												$DeviceStatus = "";
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="center"><?php echo($Index);?></td>
										<!-- <td align="left"><?php //echo($objRow->clientname);?></td> -->
										<td align="left"><?php echo($objRow->mobilename);?></td>
										<td align="left" id="divMobileNo<?php echo($Index);?>"><?php echo($objRow->mobileno);?></td>
										<td align="center"><?php echo($objRow->maxslot);?></td>
										<td align="center"><?php echo($objRow->usedslot);?></td>
										<td align="center"><?php echo($DeviceStatus);?></td>
										<td align="center"><?php echo(ShowDate($objRow->adddate,4));?></td>
										<td align="center"><?php echo(ShowDate($objRow->lastedit,4));?></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" title="Detail" onclick="DeviceDetail(<?php echo($objRow->mobileid);?>);" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body">
													<i class="fa fa-eye"></i>
												</button>
												<!-- <button type="button" class="btn btn-warning btn-sm" title="Edit" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" class="btn btn-danger btn-sm" title="Delete" data-toggle="tooltip" data-container="body">
													<i class="fa fa-trash"></i>
												</button> -->
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
</script>
</body>
</html>