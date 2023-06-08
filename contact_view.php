<?php
	$PageID = array(5,0,0);
	$PagePath = "../../";
	$PageMenu = "Contact Us";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	$cboSearch 	= $_REQUEST['cboSearch'] ?? 0;
	$txtSearch 	= $_REQUEST['txtSearch'] ?? "";
	$cboType 	= $_REQUEST['cboType']   ?? -1;
	$txtStartDate = $_REQUEST['txtStartDate'] ?? date("Y-m-d 00:00:00");
	$txtCloseDate = $_REQUEST['txtCloseDate'] ?? date("Y-m-d 23:59:59");
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>../plugins/intl-mobile/css/intlTelInput.css">
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
						<h1 class="m-0 text-dark">Contact Us</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Contact Us</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" action="contact_view" method="post" role="form">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "-- Show All --";
											$ComboData[] = "Name";
											$ComboData[] = "Phone";
											$ComboData[] = "Email";
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
										<label>Date Range :</label>
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
										<label>Contact Type :</label>
										<?php
											$ComboData = array();
											$ComboData[-1] = "-- Show All --";
											$ComboData[0] = "Inquiry";
											$ComboData[1] = "Suggestion";
											$ComboData[2] = "Complaint";
											DBComboArray("cboType",$ComboData,0,$cboType," form-control select2","style=\"width: 100%;\"");
										?>
									</div>
								</div>
							</div>
							<div class="row mb-3">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary" onclick="return Verify();">
										<i class="fa fa-search"></i> &nbsp; Search Contacts
									</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover table-responsive" width="100%">
								<thead>
									<tr>
										<th width="6%"  style="text-align:left;"  >Sr #</th>
										<th width="24%" style="text-align:left;"  >Contact Name</th>
										<th width="14%" style="text-align:center;">Type</th>
										<th width="14%" style="text-align:left;"  >Dated</th>
										<th width="14%" style="text-align:left;"  >Mobile</th>
										<th width="26%" style="text-align:left;"  >Email</th>
										<th width="4%"  style="text-align:center; min-width:40px;">-</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$Index 		= 0;
										$PerPageRec = 50;
										$Page 		= $_REQUEST['Page'] ?? 1;
										$PageLink 	= "contact_view";
										$PageParam 	= "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										$QuerySelect = "SELECT contact_id, name, contactdate, contacttype, mobile, email";
										$QueryJoin = "".
											" FROM contactus";  
										$QueryWhere = "".
											" WHERE contactdate BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."'";
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 1)
												$QueryWhere .= " AND name LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 2)
												$QueryWhere .= " AND mobile LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 3)
												$QueryWhere .= " AND email LIKE '%".$txtSearch."%'";
										}
										if ($cboType > -1)
										{
											if ($cboType == 0)
												$QueryWhere .= " AND contacttype = 0";
											elseif ($cboType == 1)
												$QueryWhere .= " AND contacttype = 1";
											elseif ($cboType == 2)
												$QueryWhere .= " AND contacttype = 2";
										}
										$Query  = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total  = $objRow->Total;
										$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere." ORDER BY contactdate DESC".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										//echo("<br><br>".$Query); die;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
											if ($objRow->contacttype == 0)
											{
												$ContType = "Inquiry";
											}
											elseif ($objRow->contacttype == 1)
											{
												$ContType = "Suggestion";
											}
											elseif ($objRow->contacttype == 2)
											{
												$ContType = "Complaint";
											}
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="left"><?php echo($objRow->contact_id);?></td>
										<td align="left"><?php echo($objRow->name);?></td>
										<td align="center"><?php echo($ContType);?></td>
										<td align="left"><?php echo(ShowDate($objRow->contactdate,4));?></td>
										<td align="left"><?php echo($objRow->mobile);?></td>
										<td align="left"><?php echo($objRow->email);?></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" title="Contact Details" onclick="EnquiryDetail(<?php echo($objRow->contact_id);?>);" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body">
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
<script type="text/javascript">
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
	
	function EnquiryDetail(ContactID)
	{
		var Win = Popup("contact_detail?ContactID="+ContactID,"NC_HalalMeat_View",740,1024,100,100);
		Win.focus();
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