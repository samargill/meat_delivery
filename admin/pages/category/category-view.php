<?php
	$PageID = array(2,0,0);
	$PagePath = "../../";
	$PageMenu = "Categories";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	//CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	$cboSearch = $_REQUEST['cboSearch'] ?? 0;
	$txtSearch = $_REQUEST['txtSearch'] ?? "";
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
					<form name="Form" role="form" action="userview" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[0] = "-- Show All --";
											$ComboData[1] = "Category Name";
										?>
										<label>Search By :</label>
										<?php
											DBComboArray("cboSearch",$ComboData,0,$cboSearch,"form-control select2","");
										?>
									</div>
									<div class="form-group">
										<label>Search Text :</label>
										<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary">Search Category</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th width="5%"  style="text-align:left;"  >Sr #</th>
										<th width="15%" style="text-align:left;"  >Type</th>
										<th width="25%" style="text-align:left;"  >Client Name</th>
										<th width="20%" style="text-align:left;"  >User Name</th>
										<th width="10%" style="text-align:left;"  >Mobile</th>
										<th width="10%" style="text-align:left;"  >Last Login</th>
										<th width="7%"  style="text-align:left;"  >Status</th>
										<th width="8%"  style="text-align:center; min-width: 100px;">-</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$Index = 0;
									
									$Name = "firstname";
									$User = "admin";
									$AdminTypeID = "admintypeid";
									$AdminTypeName = "admintypename";
									$AdminID = "adminid";
									$Table = "adminlogin";
									$JoinTable = "admintype";
									$SessionID = $_SESSION[SessionID];
									
									$QuerySelect= "SELECT C.{$User}id As UserID, C.{$Name} As UserName,".
										" C.email, C.mobile, C.lastlogin, CT.{$AdminTypeName} As UserTypeName,".
										" TIMESTAMPDIFF(MINUTE,C.lastactive,NOW()) As TimeDiff";
									$FromTable	= " FROM adminlogin C";
									$JoinTable	= " INNER JOIN {$JoinTable} CT ON C.{$User}type = CT.{$AdminTypeID}";
									$QueryWhere = " WHERE C.{$AdminID} = {$SessionID}";
									$Query = $QuerySelect." ".$FromTable." ".$JoinTable." ".$QueryWhere."";
									if (strlen($txtSearch) > 0)
									{
										if ($cboSearch == 1)
										{
											$Query .= " AND (C.".$Name." LIKE '%".$txtSearch."%')";
										}
									}
									$Query .= " ORDER BY C.".$AdminID." ASC";
									$rstRow = mysqli_query($Conn,$Query);
									$Index = 0;
									while ($objRow = mysqli_fetch_object($rstRow))
									{
										if ($objRow->TimeDiff < 15 && $objRow->TimeDiff != "")
										{
											$LoginStatus =	"<i class=\"fa fa-check-circle text-success\"></i>";
											$LoginTitle	 = "Online";
										}
										elseif ($objRow->TimeDiff >= 15 && $objRow->TimeDiff < 30)
										{
											$LoginStatus =	"<i class=\"fa fa-exclamation-circle text-warning\" style=\"color:#f39c12;\"></i>";
											$LoginTitle	 = "In-Active";
										}
										else
										{
											$LoginStatus =	"<i class=\"fa fa-times-circle text-danger\"></i>";
											$LoginTitle	 = "Offline";
										}
								?>
									<tr>
										<td align="left"  ><?php echo(++$Index);?></td>
										<td align="left"  ><?php echo($objRow->UserTypeName);?></td>
										<td align="left"  ><?php echo(UCString(trim($objRow->UserName)));?></td>
										<td align="left"  ><?php echo($objRow->email);?></td>
										<td align="left"  ><?php echo($objRow->mobile);?></td>
										<td align="left"  ><?php echo(ShowDate($objRow->lastlogin,3));?></td>
										<td align="center"><span data-toggle="tooltip" data-container="body" title="<?php echo $LoginTitle; ?>"><?php echo $LoginStatus; ?></span></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" onclick="EditRights(<?php echo($objRow->UserID);?>);" class="btn bg-purple btn-sm" data-toggle="tooltip" data-container="body" title="User Rights">
													<i class="fa fa-unlock"></i>
												</button>
												<button type="button" onclick="EditUser(<?php echo($objRow->UserID);?>);" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body" title="Edit">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" onclick="DeleteUser(<?php echo($objRow->UserID);?>);" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body" title="Delete">
													<i class="fa fa-trash"></i>
												</button>
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
		// Initialize Select2 Elements
		$(".select2").select2();
		// Initialize DataTable
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
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "top";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>