<?php
	$PageID = array(0,0,0);
	$PagePath = "../../";
	$PageMenu = "Upgrade Package";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/packages.php");
	include($PagePath."lib/combos.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
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
						<h1 class="m-0 text-dark"><?php echo($PageMenu);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<!-- <li class="breadcrumb-item active">Campaigns</li> -->
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-info">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-info" role="alert">
									<h4 class="alert-heading my-3"><b class="text-warning">Note!</b></h4>
									<h5>This functionality is not supported in this package</h5>
									<hr>
									<p class="mb-4">If you want to use this functionality then upgrade your package</p>
									<div class="row ">
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="form-group">
												<a href="#" class="btn btn-danger btn-block" style="text-decoration: none;" onclick="GoToDedicatedPkg();">
													Upgrade To Dedicated Package
												</a>
											</div>
										</div>		
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<script src="<?php echo($PagePath);?>../plugins/intl-mobile/js/intlTelInput.js"></script>
<!-- Page Script -->
<script>
	function GoToDedicatedPkg()
	{
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure you want to upgrade your package',
			// type: 'red',
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: 'col-md-6 col-md-offset-3',
			buttons: {
				"Ok": {
					text: 'OK',
					btnClass: 'btn-blue',
					action: function()
					{
						window.location.href = "<?php echo($PagePath);?>pages/clientuser/new-package-details?Tab=Dedicated";
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
					action: function() {
					}
				},
			}
		});
	}
</script>
</body>
</html>