<?php
	$PageID = array(2,0,0);
	$PagePath = "../../";
	$PageMenu = "Categories";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
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
					<form name="Form" role="form" action="category-view" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[0] = "-- Show All --";
											$ComboData[1] = "Name";
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
									<button type="submit" name="btnSearch" class="btn btn-primary"><i class="fas fa-search"></i>&nbsp; Search Category</button>
								</div>
								<div class="col-md-4">
									<button type="button" name="btnAddCategory" id="btnAddCategory" class="btn btn-success">
										<i class="fa fa-plus"></i> &nbsp; Add New Category
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover table-responsive" style="min-width:800px; width: 100%;">
								<thead>
									<tr>
										<th width="6%"  style="text-align:left;">Sr #</th>
										<th width="22%" style="text-align:left;">Category Name</th>
										<th width="40%" style="text-align:left;">Description</th>
										<th width="22%" style="text-align:left;">Status</th>
										<th width="10%" style="text-align:center; min-width:80px;">-</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index = 0;
										$PerPageRec 	= 50;
										$Page 			= $_REQUEST['Page'] ?? 1;
										$PageLink 		= "category-view";
										$PageParam 		= "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect 	= " SELECT category_id, name, description, status";
										$QueryJoin 		= " FROM product_category";
										$QueryWhere  	= " WHERE 1";
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 1)
												$QueryWhere .= " AND name LIKE '%".$txtSearch."%'";
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total  = $objRow->Total;
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere." ORDER BY category_id";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->status == 0)
											{
												$Status = "Enabled";
												$Css    = "text-success";
											}
											else
											{
												$Status = "Disabled";
												$Css    = "text-danger";
											}
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="left"><?php echo($Index);?></td>
										<td align="left"><?php echo($objRow->name);?></td>
										<td align="left"><?php echo($objRow->description);?></td>
										<td align="center" class="<?php echo($Css);?>"><?php echo($Status);?></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="EditRecord(<?php echo($objRow->category_id);?>);" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="DeleteRecord(<?php echo($objRow->category_id);?>,<?php echo($Index);?>);" data-toggle="tooltip" data-container="body">
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
		// Initialize Select2 Elements
		$(".select2").select2();
		// Initialize DataTable
		$('#MyDataTable').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": false,
			"autoWidth": false,
			"iDisplayLength": 50,
			"scrollX": true
		});
	});
</script>
</body>
</html>