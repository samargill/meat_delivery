<?php
	$PageID = array(5,4,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	$PageName = "Web Images";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_REQUEST['cboStatus']))
		$cboStatus = $_REQUEST['cboStatus'];
	else
		$cboStatus = -1;
	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">
		function AddRecord()
		{
			var Win = Popup("image-add","KS_FinHubAdminID_Edit",740,1024,100,100);
			Win.focus();
		}
		function EditRecord(ImgID)
		{
			var Win = Popup("image-add?ImgID="+ImgID,"KS_FinHubAdminID_Edit",740,1024,100,100);
			Win.focus();
		}
		function DeleteSlide(ImgID,Index)
		{
			$.confirm({
				title: "Delete!",
				content: "Are You Sure You Want To Delete This Slide ?",
				icon: "fa fa-trash",
				animation: "scale",
				closeAnimation: "scale",
				opacity: 0.5,
				columnClass: 'col-md-6 col-md-offset-3',
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
										url: "../ajaxs/setting/image-del",
										dataType: "JSON",
										method: "POST",
										data: {
											"DelSlide": "",
											"ImgID": ImgID
										}
										}).done(function (response) {
											self.setTitle(response.Status);
											self.setContent(response.Message);
											if (response.Status == "Done")
											{
												RemoveRow = true;
											}
										}).fail(function(){
											self.setTitle("Error!");
											self.setContent('Error Completing Operation. Please Try Again ...');
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
										$("#DataRow"+Index).hide();
									}
								}
							});
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
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1><?php echo("View ".$PageName)?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
							<li class="breadcrumb-item active"><?php echo("View ".$PageName)?></li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Page Error -->
		<!-- Main content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="specsliderview" method="post" onsubmit="return Verify();">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<button type="button" name="btnAdd" class="btn btn-primary" onclick="AddRecord();">Add New Slide</button>
									</div>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="25%" style="text-align:left;"  >Image</th>
										<th width="10%" style="text-align:left;"  >Name</th>
										<th width="10%" style="text-align:left;"  >Dimention</th>
										<th width="10%" style="text-align:left;"  >Size</th>
										<th width="10%" style="text-align:left;"  >Add Date</th>
										<th width="10%" style="text-align:left;"  >Last Edit</th>
										<th width="10%" style="text-align:left;"  >Status</th>
										<th width="10%"  style="text-align:center; min-width:100px;">-</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$Index = 0;
									$Query = "SELECT imageid, imagename, imagedim, imagesize, adddate, lastedit, status".
										" FROM webimage".
										" WHERE status = 1";
									$rstRow = mysqli_query($Conn,$Query);
									while ($objRow = mysqli_fetch_object($rstRow))
									{
										$Index++;
										if($objRow->status == 1)
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
									<tr id="DataRow<?php echo($Index);?>">
										<td align="left" style="vertical-align:inherit;"><?php echo($Index);?></td>
										<td align="center" >
											<img src="<?php echo($PagePath);?>../images/webdata-img/<?php echo($objRow->imagename);?>" class="img-fluid" alt="">
										</td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->imagename);?></td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->imagedim);?></td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->imagesize);?></td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->adddate);?></td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->lastedit);?></td>
										<td align="center" style="vertical-align:inherit;"><?php echo($objRow->status);?></td>
										<td align="center" style="vertical-align:inherit;">
											<div class="btn-group">
												<button type="button" class="btn btn-info btn-sm" title="Edit" onclick="EditRecord(<?php echo($objRow->imageid);?>);" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<?php
													If ($Index > 1)
													{
												?>
												<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="return DeleteSlide(<?php echo($objRow->imageid);?>,<?php echo($Index);?>);" data-toggle="tooltip" data-container="body">
													<i class="fa fa-trash"></i>
												</button>
												<?php
													}
												?>
											</div><!-- /.btn-group -->
										</td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
						<input type="hidden" name="btnSubmit" value="" disabled>
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
		//Initialize Select2
		$(".select2").select2();
	});
	$(function () {
		$('#MyDataTable').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": true,
			"autoWidth": true,
			"iDisplayLength": 50,
			"scrollX": true
		});
	});
</script>
</body>
</html>