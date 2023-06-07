<?php
	$PageID = array(1,2,0);
	$PagePath = "../../";
	$PageMenu = "Contacts";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	$DefCountryID = GetValue("countryid","client","clientid = ".$_SESSION[SessionID."ClientID"]);
	$DefCountry   = GetValue("codeiso2","address_country","countryid = ".$DefCountryID);
	if (isset($_REQUEST['cboDevice']))
		$cboDevice = $_REQUEST['cboDevice'];
	else
		$cboDevice = 0;
	if (isset($_REQUEST['cboSearch']))
		$cboSearch = $_REQUEST['cboSearch'];
	else
		$cboSearch = 0;
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
						<h1 class="m-0 text-dark">View Opt-Out</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Contacts</li>
							<li class="breadcrumb-item active">View Opt-Out</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" action="optout-view" method="post" role="form">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>SMS Device :</label>
										<?php
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.clientid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],$cboDevice,"-- Show All --",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="cboSearch">Search By :</label>
										<?php
											$ComboData = array();
											$ComboData[] = "Contact Name";
											$ComboData[] = "Contact Mobile";
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
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<button type="submit" name="btnSearch" class="btn btn-primary">
											<i class="fa fa-search"></i> &nbsp; Search Contact
										</button>
									</div>	
								</div>
								<!--div class="col-md-3">
									<div class="form-group">
										<button type="button" name="btnAddContact" id="btnAddContact" class="btn bg-purple" onclick="EditContact(0,0);">
											<i class="fa fa-user-plus"></i> &nbsp; Add New Opt-Out
										</button>
									</div>
								</div-->
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
								<thead>
									<tr>
										<th width="5%"  style="text-align:center;">Sr #</th>
										<th width="43%" style="text-align:left;"  >Name</th>
										<th width="20%" style="text-align:left;"  >Mobile</th>
										<th width="12%" style="text-align:left;"  >Device</th>
										<th width="12%" style="text-align:left;"  >Add Date</th>
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
										$PageLink = "optout-view";
										$PageParam = "cboSearch=".$cboSearch."&txtSearch=".$txtSearch;
										// $QuerySelect = "SELECT O.optid, CC.fullname, O.mobile, CM.mobilecode, O.adddate";
										// $QueryJoin   = "".
										// 	" FROM optout O".
										// 	" INNER JOIN clientmobile CM ON O.clientmobid = CM.clientmobid".
										// 	" LEFT OUTER JOIN clientcontact CC ON O.mobile = CC.mobile";
										// $QueryWhere  = "".
										// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"];
										$QuerySelect = "SELECT O.optid, CC.fullname, O.mobile, CM.mobilecode, O.adddate";
										$QueryJoin   = "".
											" FROM optout O".
											" INNER JOIN clientmobile CM ON O.mobileid = CM.mobileid".
											" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
											" LEFT OUTER JOIN clientcontact CC ON O.mobile = CC.mobile";
										$QueryWhere  = "".
											" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"];

										if ($cboDevice > 0)
										{
											$QueryWhere .= " AND CM.mobileid = ".$cboDevice;
										}
										if (strlen($txtSearch) > 0)
										{
											if ($cboSearch == 0)
												$QueryWhere .= " AND CC.fullname LIKE '%".$txtSearch."%'";
											elseif ($cboSearch == 1)
												$QueryWhere .= " AND O.mobile LIKE '%".$txtSearch."%'";
										}
										$Query = "SELECT COUNT(*) As Total ".$QueryJoin." ".$QueryWhere;
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
										$Total = $objRow->Total;
										$Query = $QuerySelect." ".$QueryJoin." ".$QueryWhere.
											" ORDER BY CC.fullname".
											" LIMIT ".(($Page - 1) * $PerPageRec).", ".$PerPageRec;
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
									?>
									<tr id="Row<?php echo($Index);?>">
										<td align="center"><?php echo($Index);?></td>
										<td align="left" id="divName<?php echo($Index);?>"><?php echo($objRow->fullname);?></td>
										<td align="left" id="divMobile<?php echo($Index);?>"><?php echo("+".$objRow->mobile);?></td>
										<td align="center"><?php echo($objRow->mobilecode);?></td>
										<td align="center"><?php echo(ShowDate($objRow->adddate,1));?></td>
										<td align="center">
											<!--div class="btn-group">
												<button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="EditContact(<?php echo($Index);?>,<?php echo($objRow->optid);?>);" data-toggle="tooltip" data-container="body">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="DeleteContact(<?php echo($Index);?>,<?php echo($objRow->optid);?>);" data-toggle="tooltip" data-container="body">
													<i class="fa fa-trash-o"></i>
												</button>
											</div-->
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
<div id="Modal-Contact" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form name="FrmContact" id="FrmContact" action="" method="post" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<h4 id="Modal-Contact-Title" class="modal-title">Add New Opt-Out</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Name (*) :</label>
								<input type="text" name="txtName" id="txtName" value="" class="form-control" maxlength="50">
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label>Mobile :</label><br>
								<input type="text" name="txtMobile" id="txtMobile" value="" class="form-control" maxlength="20" style="text-indent: 5px;">
								<input type="hidden" name="FullMobile" id="FullMobile" value="">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="OptID" id="OptID" value="">
					<input type="hidden" name="CountryCode" id="CountryCode" value="">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="submit" name="btnSaveEdu" class="btn btn-primary">Save Contact</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<script src="<?php echo($PagePath);?>../plugins/intl-mobile/js/intlTelInput.js"></script>
<!-- Page Script -->
<script>
	var iti;
	$(function () {
		//Init Select2
		$(".select2").select2();
		//Init Mobile
		var input = document.querySelector("#txtMobile");
		iti = window.intlTelInput(input, {
			formatOnDisplay: false,
			initialCountry: "<?php echo($DefCountry);?>",
			placeholderNumberType: "MOBILE",
			utilsScript: "<?php echo($PagePath);?>../plugins/intl-mobile/js/utils.js",
		});
	});
	function EditContact(Index,OptID)
	{
		if (OptID == 0)
		{
			$("#txtName").val("");
			$("#txtMobile").val("");
			$("#OptID").val(0);
			$("#Modal-Contact-Title").html("Add New Contact");
			$('#Modal-Contact').modal('show');
		}
		else
		{
			$("#txtName").val($("#divName"+Index).html());
			$("#txtMobile").val($("#divMobile"+Index).html());
			$("#OptID").val(OptID);
			$("#Modal-Contact-Title").html("Edit Contact");
			$('#Modal-Contact').modal('show');
		}
	}
	// Add / Edit Contact Submit
	$("#FrmContact").submit(function(evt) {
		evt.preventDefault();
		if (IsEmpty(document.FrmContact.txtName.value,false) == true) 
		{
			ShowError(true,"Error!","Please Enter Contact Full Name",undefined,"txtName");
			return(false);
		}
		if (iti.isValidNumber() == false)
		{
			ShowError(true,"Error!","Please Enter Your Valid Mobile #<br><br>"+itiErrorMap[iti.getValidationError()],undefined,"txtMobileNo");
			return(false);
		}
		if (iti.getNumberType() != 1 && iti.getNumberType() != 2)
		{
			ShowError(true,"Error!","Please Enter Your Valid Mobile #<br><br>Entered Number is Not Mobile # [ "+iti.getNumberType()+" ]",undefined,"txtMobile");
			return(false);
		}
		var CountryData = iti.getSelectedCountryData();
		document.FrmContact.CountryCode.value = CountryData.iso2;
		document.FrmContact.FullMobile.value = iti.getNumber();
		var FrmData = new FormData(document.FrmContact);
		var Result = "";
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: "col-md-8 col-md-offset-2",
			content: function () {
				var self = this;
				return $.ajax({
					url: "<?php echo($PagePath);?>pages/ajaxs/contact-save",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					async: false,
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
						Result = response.Status;
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function(jqXHR,exception){
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Done")
						{
							$("#Modal-Contact").modal("hide");
						}
					}
				}
			},
			onClose: function () {
			}
		});
	});
	function DeleteContact(Index,OptID)
	{
		<?php CheckRight("Delete","ShowError");?>
		$.confirm({
			title: "Confirm!",
			content: "Are You Sure You Want To Delete This Contact ?",
			icon: "fa fa-question-circle",
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
									url: "<?php echo($PagePath);?>pages/ajaxs/contact-delete",
									dataType: "JSON",
									method: "POST",
									timeout: 3000,
									data: {
										"OptID": OptID
									}
									}).done(function (response) {
										self.setTitle(response.Status);
										self.setContent(response.Message);
										if (response.Status == "Done")
										{
											RemoveRow = true;
										}
									}).fail(function(jqXHR,exception) {
										self.setTitle("Error!");
										self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
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
									$("#Row"+Index).hide();
								}
							}
						});
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
					action: function() {
					}
				}
			}
		});
	}
</script>
</body>
</html>