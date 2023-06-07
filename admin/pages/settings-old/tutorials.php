<?php
	$PageID = array(8,0,0);
	$PagePath = "../../";
	$PageMenu = "Tutorials";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
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
				<div class="row">
					<div class="col-md-3">
						<div class="card card-outline card-solid">
							<div class="card-header with-border">
								<h3 class="box-title">Tutorials</h3>
								<div class="box-tools">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="card-body no-padding">
								<ul class="nav nav-pills nav-stacked">
									<li class="active"><a href="#"><i class="fa fa-inbox"></i> Getting Started</a></li>
									<?php
										if (false)
										{
									?>
									<li><a href="#"><i class="fa fa-envelope-o"></i> Sent</a></li>
									<li><a href="#"><i class="fa fa-file-text-o"></i> Drafts</a></li>
									<li><a href="#"><i class="fa fa-filter"></i> Junk <span class="label label-warning pull-right">65</span></a></li>
									<li><a href="#"><i class="fa fa-trash-o"></i> Trash</a></li>
									<?php
										}
									?>
								</ul>
							</div><!-- /.box-body -->
						</div><!-- /. box -->
					</div><!-- /.col -->
					<div class="col-md-9">
						<div class="card card-outline card-primary">
							<div class="card-header with-border">
								<h3 class="card-title">Getting Started</h3>
							</div><!-- /.box-header -->
							<div class="card-body no-padding">
								<div class="table-responsive mailbox-messages">
									<table class="table table-hover table-striped">
										<tbody>
											<tr>
												<td>1. &nbsp; Register your desired mobile number in <a href="<?php echo($PagePath);?>pages/device/device-view">SMS Devices</a></td>
											</tr>
											<tr>
												<?php
													
												?>
												<td>2. &nbsp; <a href="<?php echo(WebsiteUrl);?>/getapp">Click Here To Download BullkySMS APK</a> & install on your Android Mobile.</td>
											</tr>
											<tr>
												<td>3. &nbsp; Allow installation of Unknown apps. (If asked)</td>
											</tr>
											<tr>
												<td>4. &nbsp; Login to the BullkySMS app using your credentials.</td>
											</tr>
											<tr>
												<td>5. &nbsp; Leave the app running in background.</td>
											</tr>
											<tr>
												<td>6. &nbsp; Go to Campaign -> <a href="<?php echo($PagePath);?>pages/campaign/sms-send">Send New SMS</a> to send SMS.</td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.mail-box-messages -->
							</div><!-- /.box-body -->
						</div><!-- /. box -->
					</div><!-- /.col -->
				</div><!-- /.row -->
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
</body>
</html>