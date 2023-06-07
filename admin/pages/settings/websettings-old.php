<?php
	$PageID = array(11,1,0);
	$PagePath = "../../../";
	$PageMenu = "Settings";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");

	if (isset($_REQUEST['Tab']))
		$Tab = $_REQUEST['Tab'];
	else
		$Tab = "General";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">
		function LoadTab(TabID)
		{
			window.location = "websettings.php?Tab="+TabID;
		}
		function Verify(Type,TextBox,Empty,ConfigID)
		{
			<?php CheckRight("Edit");?>
			if (Type == "Text")
			{
				if (IsEmpty(document.Form.elements[TextBox].value) == !Empty)
				{
					ShowError(true,"Error!","Please Enter Valid Text ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Phone")
			{
				if (IsPhone(document.Form.elements[TextBox].value,Empty) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Phone ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Email")
			{
				if (IsEmail(document.Form.elements[TextBox].value,Empty) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Email ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Integer")
			{
				if (IsNumber(document.Form.elements[TextBox].value,Empty,false,0) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Numeric Whole Number Value ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Double")
			{
				if (IsNumber(document.Form.elements[TextBox].value,Empty,true,0) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Numeric Decimal Value ...","",TextBox)
					return(false);
				}
				else
				{
					document.Form.elements[TextBox].value = ShowFloat(document.Form.elements[TextBox].value,2);
				}
			}
			$.confirm({
				title: "Saving!",
				content: "Are You Sure You Want To Save This Setting ?",
				icon: "fa fa-save",
				animation: "scale",
				closeAnimation: "scale",
				opacity: 0.5,
				buttons: {
					"confirm": {
						text: "Yes",
						btnClass: "btn-blue",
						keys: ['enter'],
						action: function() {
							$.confirm({
								content: function () {
									var self = this;
									return $.ajax({
										url: '../ajax',
										dataType: 'JSON',
										method: 'POST',
										data: {"Parent":"SaveSetting","ConfigID":ConfigID,"ConfigValue":document.Form.elements[TextBox].value}
										}).done(function (response) {
											self.setTitle("Saved!");
											self.setContent(response.Message);
										}).fail(function(){
											self.setContent('Error completing operation. Please try again ...');
										});
								},
								buttons: {
									"OK": {
										text: "OK",
										btnClass: "btn-blue"
									}
								},
								onClose: function () {
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
<body class="hold-transition <?php echo(WebsiteSkin);?> sidebar-mini">
	<div class="wrapper">
		<!-- Top Header -->
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
							<h1 class="m-0 text-dark">Website Settings</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
								<li class="breadcrumb-item active">Website Settings</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<!-- Page Error -->
			<?php
				if (isset($_REQUEST['Err']))
				{
					$Message = "";
					$MessageBG = "callout-danger lead";
					$MessageHead = "Error:";
					$MessageIcon = "fa-exclamation-circle";
					switch ($_REQUEST['Err'])
					{
						case 2:
							$Message = "Website Settings Updated Successfully ...";
							break;
						case 101:
							$Message = "Unable To Update Website Settings - Fatal Error ...";
							if (isset($_SESSION["MysqlErr"]))
							{
								$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
								unset($_SESSION["MysqlErr"]);
							}
							break;
					}
					if ($_REQUEST['Err'] < 100)
					{
						$MessageHead = "Note:";
						$MessageBG = "callout-info";
						$MessageIcon = "fa-info-circle";
					}
			?>
			<div style="padding-left: 15px; padding-right: 15px;">
				<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
					<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
					<span style="font-size:16px;"><?php echo($Message);?></span>
				</div>
			</div>
			<?php
				}
			?>
			<!-- Main Content -->
			<section class="content">
				<div class="nav-tabs-custom card-tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li <?php if ($Tab == "General") echo("class=\"active\"");?>>
							<a href="#General" data-toggle="tab" onclick="LoadTab('General');">
								<i class="fa fa-gear"></i>&nbsp;&nbsp;General
							</a>
						</li>
						<li <?php if ($Tab == "Social") echo("class=\"active\"");?>>
							<a href="#Social" data-toggle="tab" onclick="LoadTab('Social');">
								<i class="fa fa-facebook-square"></i>&nbsp;&nbsp;Social Media
							</a>
						</li>
						<li <?php if ($Tab == "Payment") echo("class=\"active\"");?>>
							<a href="#Payment" data-toggle="tab" onclick="LoadTab('Payment');">
								<i class="fa fa-credit-card"></i>&nbsp;&nbsp;Credit Card Payment
							</a>
						</li>
						<li <?php if ($Tab == "Email") echo("class=\"active\"");?>>
							<a href="#Email" data-toggle="tab" onclick="LoadTab('Email');">
								<i class="fa fa-envelope"></i>&nbsp;&nbsp;Email Templates
							</a>
						</li>
						<li <?php if ($Tab == "Footer") echo("class=\"active\"");?>>
							<a href="#Footer" data-toggle="tab" onclick="LoadTab('Footer');">
								<i class="fa fa-credit-card"></i>&nbsp;&nbsp;Footer
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<?php
							if ($Tab == "General")
							{
								$Query = "SELECT config_id, config_name, config_value".
									" FROM websettings WHERE config_id BETWEEN 1 AND 10".
									" ORDER BY config_id";
								$rstRow = mysqli_query($Conn,$Query);
								while ($objRow = mysqli_fetch_object($rstRow))
								{
									switch ($objRow->config_id)
									{
										case 1:
											$txtName = $objRow->config_value;
											break;
										case 2:
											$txtPhone = $objRow->config_value;
											break;
										case 3:
											$txtFax = $objRow->config_value;
											break;
										case 4:
											$txtSupportEmail = $objRow->config_value;
											break;
										case 5:
											$txtRegiEmail = $objRow->config_value;
											break;
										case 6:
											$txtBookEmail = $objRow->config_value;
											break;
										case 7:
											$txtFromEmail = $objRow->config_value;
											break;
										case 8:
											$txtWebsite = $objRow->config_value;
											break;
										case 9:
											$txtAddress1 = $objRow->config_value;
											break;
										case 10:
											$txtAddress2 = $objRow->config_value;
											break;
										default:
											break;
									}
								}
						?>
						<div class="tab-pane active" id="General">
							<section class="content">
								<div class="container-fluid">
									<div class="card card-outline card-primary">
										<form name="Form" role="form" action="websettings.php" method="post" onsubmit="return VerifyGeneral();">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Company Name (*)</label>
															<div class="input-group">
																<input type="text" name="txtName" id="txtName" value="<?php echo($txtName);?>" class="form-control" readonly>
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtName',false,1);" disabled>Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Company Website (*)</label>
															<div class="input-group">
																<input type="text" name="txtWebsite" id="txtWebsite" value="<?php echo($txtWebsite);?>" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtWebsite',false,8);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Address 1 (*)</label>
															<div class="input-group">
																<input type="text" name="txtAddress1" id="txtAddress1" value="<?php echo($txtAddress1);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtAddress1',false,9);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Address 2 (*)</label>
															<div class="input-group">
																<input type="text" name="txtAddress2" id="txtAddress2" value="<?php echo($txtAddress2);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtAddress2',false,10);">Save</button>
																</span>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Phone (*)</label>
															<div class="input-group">
																<input type="text" name="txtPhone" id="txtPhone" value="<?php echo($txtPhone);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Phone','txtPhone',false,2);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Fax (*)</label>
															<div class="input-group">
																<input type="text" name="txtFax" id="txtFax" value="<?php echo($txtFax);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Phone','txtFax',false,3);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>From Email : To Send Emails To Customers (*)</label>
															<div class="input-group">
																<input type="text" name="txtFromEmail" id="txtFromEmail" value="<?php echo($txtFromEmail);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Email','txtFromEmail',false,7);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Support Email : To Show on Website To Visitors (*)</label>
															<div class="input-group">
																<input type="text" name="txtSupportEmail" id="txtSupportEmail" value="<?php echo($txtSupportEmail);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Email','txtSupportEmail',false,4);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>New Customer Signup Email (*)</label>
															<div class="input-group">
																<input type="text" name="txtRegiEmail" id="txtRegiEmail" value="<?php echo($txtRegiEmail);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Email','txtRegiEmail',false,5);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>New Booking Email (*)</label>
															<div class="input-group">
																<input type="text" name="txtBookEmail" id="txtBookEmail" value="<?php echo($txtBookEmail);?>" class="form-control" maxlength="50">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Email','txtBookEmail',false,6);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div><!-- /.row -->
											</div><!-- /.card-body -->
										</form>
									</div>
								</div>					
							</section>
						</div>
						<?php
							}
							else if ($Tab == "Social")
							{
								$Query = "SELECT config_id, config_name, config_value".
									" FROM websettings WHERE config_id BETWEEN 201 AND 205".
									" ORDER BY config_id";
								$rstRow = mysqli_query($Conn,$Query);
								while ($objRow = mysqli_fetch_object($rstRow))
								{
									switch ($objRow->config_id)
									{
										case 201:
											$txtFacebookID = $objRow->config_value;
											break;
										case 202:
											$txtTwitterID = $objRow->config_value;
											break;
										case 203:
											$txtGoogleID = $objRow->config_value;
											break;
										case 204:
											$txtLinkedInID = $objRow->config_value;
											break;
										case 205:
											$txtInstaID = $objRow->config_value;
											break;
										default:
											break;
									}
								}
						?>
						<div class="tab-pane active" id="Social">
							<section class="content">
								<div class="container-fluid">
									<div class="card card-outline card-primary">
										<form name="Form" role="form" action="websettings.php" method="post" onsubmit="return Verify();">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Facebook (*)</label>
															<div class="input-group">
																<input type="text" name="txtFacebookID" id="txtFacebookID" value="<?php echo($txtFacebookID);?>" maxlength="100" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtFacebookID',true,201);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Twitter (*)</label>
															<div class="input-group">
																<input type="text" name="txtTwitterID" id="txtTwitterID" value="<?php echo($txtTwitterID);?>" maxlength="100" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtTwitterID',true,202);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Google (*)</label>
															<div class="input-group">
																<input type="text" name="txtGoogleID" id="txtGoogleID" value="<?php echo($txtGoogleID);?>" maxlength="100" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtGoogleID',true,203);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Linked In (*)</label>
															<div class="input-group">
																<input type="text" name="txtLinkedInID" id="txtLinkedInID" value="<?php echo($txtLinkedInID);?>" maxlength="100" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtLinkedInID',true,204);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Instagram (*)</label>
															<div class="input-group">
																<input type="text" name="txtInstaID" id="txtInstaID" value="<?php echo($txtInstaID);?>" maxlength="100" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtInstaID',true,205);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div><!-- /.row -->
											</div><!-- /.card-body -->
										</form>
									</div>
								</div>		
							</section>
						</div>
						<?php
							}
							else if ($Tab == "Payment")
							{
								$Query = "SELECT config_id, config_name, config_value".
									" FROM websettings WHERE config_id BETWEEN 103 AND 103".
									" ORDER BY config_id";
								$rstRow = mysqli_query($Conn,$Query);
								while ($objRow = mysqli_fetch_object($rstRow))
								{
									switch ($objRow->config_id)
									{
										case 103:
											$txtPayment = $objRow->config_value;
											break;
										default:
											break;
									}
								}
						?>
						<div class="tab-pane active" id="Payment">
							<section class="content">
								<div class="container-fluid">
									<div class="card card-outline card-primary">
										<form name="Form" role="form" action="websettings.php" method="post" onsubmit="return Verify();">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Payment Message (*)</label>
															<div class="input-group">
																<textarea name="txtPayment" id="txtPayment" rows="8" class="form-control"><?php echo($txtPayment);?></textarea>
																<span class="input-group-btn" style="vertical-align: top;">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtPayment',false,103);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div><!-- /.row -->
											</div><!-- /.card-body -->
										</form>
									</div>
								</div>		
							</section>
						</div>
						<?php
							}
							else if ($Tab == "Email")
							{
								$Query = "SELECT config_id, config_name, config_value".
									" FROM websettings WHERE config_id BETWEEN 301 AND 302".
									" ORDER BY config_id";
								$rstRow = mysqli_query($Conn,$Query);
								while ($objRow = mysqli_fetch_object($rstRow))
								{
									switch ($objRow->config_id)
									{
										case 301:
											$txtRefeEmail = $objRow->config_value;
											break;
										case 302:
											$txtCertEmail = $objRow->config_value;
											break;
										default:
											break;
									}
								}
						?>
						<div class="tab-pane active" id="Email">
							<section class="content">
								<div class="container-fluid">
									<div class="card card-outline card-primary">
										<form name="Form" role="form" action="websettings.php" method="post" onsubmit="return Verify();">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Referral Letter Email Template (*)</label>
															<div class="input-group">
																<textarea name="txtRefeEmail" id="txtRefeEmail" rows="8" class="form-control"><?php echo($txtRefeEmail);?></textarea>
																<span class="input-group-btn" style="vertical-align: top;">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtRefeEmail',false,301);">Save</button>
																</span>
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>Certificate Email Template (*)</label>
															<div class="input-group">
																<textarea name="txtCertEmail" id="txtCertEmail" rows="8" class="form-control"><?php echo($txtCertEmail);?></textarea>
																<span class="input-group-btn" style="vertical-align: top;">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtCertEmail',false,302);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div><!-- /.row -->
											</div><!-- /.card-body -->
										</form>
									</div>
								</div>		
							</section>
						</div>
						<?php
							}
							else if ($Tab == "Footer")
							{
								$Query = "SELECT config_id, config_name, config_value".
									" FROM websettings WHERE config_id BETWEEN 101 AND 102".
									" ORDER BY config_id";
								$rstRow = mysqli_query($Conn,$Query);
								while ($objRow = mysqli_fetch_object($rstRow))
								{
									switch ($objRow->config_id)
									{
										case 101:
											$txtFooter = $objRow->config_value;
											break;
										case 102:
											$txtCopyright = $objRow->config_value;
											break;
										default:
											break;
									}
								}
						?>
						<div class="tab-pane active" id="Social">
							<section class="content">
								<div class="container-fluid">
									<div class="card card-outline card-primary">
										<form name="Form" role="form" action="websettings.php" method="post" onsubmit="return Verify();">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Website Footer Text (*)</label>
															<div class="input-group">
																<input type="text" name="txtFooter" id="txtFooter" value="<?php echo($txtFooter);?>" maxlength="200" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Text','txtFooter',false,101);">Save</button>
																</span>
															</div>
														</div>
														<div class="form-group">
															<label>Copyright Year (*)</label>
															<div class="input-group">
																<input type="text" name="txtCopyright" id="txtCopyright" value="<?php echo($txtCopyright);?>" maxlength="4" class="form-control">
																<span class="input-group-btn">
																	<button type="button" class="btn btn-info btn-flat" onclick="Verify('Integer','txtCopyright',false,102);">Save</button>
																</span>
															</div>
														</div>
													</div>
												</div><!-- /.row -->
											</div><!-- /.card-body -->
										</form>
									</div>
								</div>		
							</section>
						</div>
						<?php
							}
						?>
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
		});
	</script>
</body>
</html>