<?php
	$PageID = array(11,2,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	$PageName = "Web Data";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	CheckRight("View","Redirect");

	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";
	if (isset($_REQUEST['cboStatus']))
		$cboStatus = $_REQUEST['cboStatus'];
	else
		$cboStatus = -1;
	if (isset($_REQUEST["cboType"]))
		$cboType = $_REQUEST["cboType"];
	else
		$cboType = 1;
	if (isset($_POST["btnUpdate"]))
	{
		$Query = "UPDATE webdata".
			" SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END".
			" WHERE dataid = ".$_REQUEST['DataID'];
		mysqli_query($Conn,$Query);
		header("Location: webdata-view?Err=2&cboType=".$cboType);
		exit;
	}
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
			<?php CheckRight("Add","ShowError");?>
			<?php CheckRight("View","ShowError");?>
			var Win = Popup("webdata-add?cboType="+document.Form.cboType.value,"KS_FinHubAdminID_Edit",740,1024,100,100);
			Win.focus();
		}
		function EditRecord(DataID)
		{
			<?php CheckRight("Edit","ShowError");?>
			var Win = Popup("webdata-add?DataID="+DataID,"KS_ArtMarketGallery_Edit",740,1024,100,100);
			Win.focus();
		}
		function UpdateStatus(DataID)
		{
			<?php CheckRight("Edit","ShowError");?>
			document.Form.DataID.value = DataID;
		}
		function DeleteRecord(DataID,DataType,Index)
		{
			<?php CheckRight("Delete","ShowError");?>	
			// alert(DataID + DataType + Index);
			// return false;
			$.confirm({
				title: "Delete!",
				content: "Are You Sure You Want To Delete This Data ?",
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
										url: "../ajaxs/admin/setting/webdata-delete.php",
										dataType: "JSON",
										method: "POST",
										data: {
											"DelData"  : "",
											"DataID"   : DataID,
											"DataType" : DataType
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
			<?php
				if (isset($_REQUEST['Err']))
				{
					$Message = "";
					$MessageBG = "danger";
					$MessageHead = "Error:";
					$MessageIcon = "fa-exclamation-triangle";
					switch ($_REQUEST['Err'])
					{
						case 2:
							$Message = "Data Status Updated Successfully ...";
							break;
						case 3:
							$Message = "Data Deleted Successfully ...";
							break;
						case 101:
							$Message = "Unable To Perform Operation - Fatal Error ...";
							if (isset($_SESSION["MysqlErr"]))
							{
								$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
								unset($_SESSION["MysqlErr"]);
							}
							break;
						case 102:
							$Message = "Data Cannot Be Deleted";
							break;					}
					if ($_REQUEST['Err'] < 100)
					{
						$MessageHead = "Note:";
						$MessageBG   = "success";
						$MessageIcon = "fa-check";
					}
			?>
			<div style="padding-left: 15px; padding-right: 15px;">
				<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
					<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
					<?php echo($Message);?>
				</div>
			</div>
			<?php
				}
			?>
			<!-- Main Content -->
			<section class="content">
				<div class="container-fluid">
					<div class="card card-outline card-primary">
						<form name="Form" id="Form" role="form" method="post">
							<div class="card-body">
								<div class="row">
									<div class="col-md-4" >
										<div class="form-group">
											<label>Search By Type :</label>
											<?php
												DBCombo("cboType","webdatatype","datatypeid","datatypename","",$cboType,"","form-control select2","onchange=\"\" style=\"width: 100%;\"");
											?>
										</div>
									</div>
									<div class="col-md-4" >
										<div class="form-group">
											<?php
												$ComboData = array();
												$ComboData[-1] = "-- Show All --";
												$ComboData[1] = "Enable";
												$ComboData[0] = "Disable";
											?>
											<label>Search By Status :</label>
											<?php
												DBComboArray("cboStatus",$ComboData,-1,$cboStatus,"form-control select2","","style=\"width: 100%;\"");
											?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Search Text :</label>
											<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<button type="submit" name="btnSearch" class="btn btn-primary">Search Data</button>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<button type="button" name="btnAdd" class="btn btn-primary" onclick="AddRecord();">Add New Data</button>
										</div>
									</div>
								</div>
								<table id="MyDataTable" class="table display responsive nowrap table-bordered table-center table-hover" style="width: 100% !important;" >
									<thead>
										<tr>
											<th width="5%"  style="text-align:center;">Sr #</th>
											<th width="20%" style="text-align:left;"  >Data Head</th>
											<th width="40%" style="text-align:left;"  >Data Text</th>
											<th width="17%" style="text-align:left;"  >Data Type</th>
											<th width="10%" style="text-align:left;"  >Status</th>
											<th width="8%"  style="text-align:center; min-width: 80px;">-</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$Index = 0;
										$Query = "SELECT WD.dataid, WD.datahead, WD.datatext,".
											" WD.datatype, WD.status, WDT.datatypename".
											" FROM webdata WD ".
											" INNER JOIN webdatatype WDT ON WD.datatype = WDT.datatypeid ".
											" WHERE 1 ";
										if (strlen($txtSearch) > 0)
										{
											$Query .= " AND datahead LIKE '%".$txtSearch."%'".
													" OR datatext LIKE '%".$txtSearch."%'";
										}
										if ($cboStatus >= 0)
										{
											$Query .= " AND WD.status = ".$cboStatus;
										}
										if ($cboType > 0)
										{
											$Query .= " AND WD.datatype = ".$cboType;
										}
										$Query .= " ORDER BY WD.dataid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
									?>
										<tr id="DataRow<?php echo($Index);?>">
											<td align="right" ><?php echo(++$Index);?></td>
											<td align="left"  ><?php echo(substr($objRow->datahead,0,25));?></td>
											<td align="left"  ><?php echo(substr($objRow->datatext,0,55)."....");?></td>
											<td align="left"  ><?php echo($objRow->datatypename);?></td>
											<td align="left"  >
												<?php
													if ($objRow->status == 0)
													{
														$Value = "Disabled";
														$Type  = "danger";
														$Alt   = "Click Here To Enable";
													}
													else
													{
														$Value = "Enabled";
														$Type = "success";
														$Alt   = "Click Here To Disable";
													}
												?>
												<button type="submit" name="btnUpdate" class="btn btn-block btn-<?php echo($Type);?> btn-sm" onclick="UpdateStatus(<?php echo($objRow->dataid);?>)" title="<?php echo($Alt);?>" style="width: 70px;">
													<?php echo($Value);?>
												</button>
											</td>
											<td align="center">
												<div class="btn-group">
													<button type="button" class="btn btn-info btn-sm" title="Edit" onclick="EditRecord(<?php echo($objRow->dataid);?>);" data-toggle="tooltip" data-container="body">
														<i class="fa fa-edit"></i>
													</button>
													<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="DeleteRecord(<?php echo($objRow->dataid.",".$cboType.",".$Index);?>);" data-toggle="tooltip" data-container="body">
														<i class="fa fa-trash"></i>
													</button>
												</div>
											</td>
										</tr>
									<?php
										}
									?>
									</tbody>
									<input type="hidden" name="DataID" id="DataID" value="0">
								</table>
							</div>
						</form>
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
			$(".select2").select2();
			$('#MyDataTable').DataTable({
				"paging"         : false,
				"lengthChange"   : false,
				"searching"      : false,
				"ordering"       : false,
				"info"           : false,
				"iDisplayLength" : 50,
				"autoWidth"      : false,
				"scrollX"        : false,
				"responsive"     : true
			});
		});
	</script>
</body>
</html>