<?php
	$PageID = array(0,0,0);
	$PagePath = "";
	$PageMenu = "Dashboard";
	include("lib/variables.php");
	include("lib/opencon.php");
	include("lib/session.php");
	include("lib/functions.php");

	if (isset($_REQUEST["Signout"]))
	{
		$Query = "INSERT INTO adminloginlog".
			" (logdate, ipaddress, adminid, logtype)".
			" VALUES (NOW(), '".$_SERVER['REMOTE_ADDR']."', ".$_SESSION[SessionID].", 1)";
		mysqli_query($Conn,$Query);
		session_destroy();
		header("Location: ./");
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<style>
		h4
		{
			font-size: 1rem !important;
		}
		.icon .icon-sml
		{
			display: block !important;
		}
		@media only screen and (max-width: 480px) 
		{
			.small-box .icon > i.fa, .small-box .icon > i.fab, .small-box .icon > i.fad, .small-box .icon > i.fal, .small-box .icon > i.far, .small-box .icon > i.fas, .small-box .icon > i.ion
			{
				font-size: 40px !important;
				top: 50px !important;
			}
		}
	</style>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<?php
		if (defined("AppView") == false)
		{
	?>
	<?php
		include("includes/header.php");
	?>
	<?php
		include("includes/left.php");
	?>
	<?php
		}
	?>
	<div class="content-wrapper" <?php if (defined("AppView")) { echo("style=\"margin-left: 0px;\"");} ?> >
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<?php
						if (defined("AppView") == false)
						{
					?>
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Dashboard</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Dashboard</li>
						</ol>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
		<section class="content">
			<div class="container-fluid">
				<?php
					if (CheckRight("View","Return") == true)
					{
				?>
				<div class="row">
					<?php
						if (false)
						{
					?>
					<div class="col-6 col-md-3 col-lg-3">
						<a href="pages/customer/customer-view?ChkDate">
							<div class="small-box bg-info">
								<?php
									$Query = "SELECT COUNT(*) As Today".
										" FROM customer".
										" WHERE adddate LIKE '".date("Y-m-d")." %'";
									$rstRow = mysqli_query($Conn,$Query);
									$objRow = mysqli_fetch_object($rstRow);
								?>
								<div class="inner">
									<h4>New Customers</h4>
									<h4>Todays : &nbsp; <?php echo($objRow->Today);?></h4>
									<h4><br></h4>
								</div>
								<div class="icon icon-sml" style="display: block !important;">
									<i class="fa fa-user-tie"></i>
								</div>
							</div>
						</a>
					</div>
					<div class="col-6 col-md-3 col-lg-3">
						<a href="pages/customer/contact-request?ChkDate">
							<div class="small-box bg-primary">
								<?php
									$Query = "SELECT COUNT(*) As Today".
										" FROM contact_us".
										" WHERE adddate LIKE '".date("Y-m-d")." %'";
									$rstRow = mysqli_query($Conn,$Query);
									$objRow = mysqli_fetch_object($rstRow);
								?>
								<div class="inner">
									<h4>New Contact</h4>
									<h4>Todays : &nbsp; <?php echo($objRow->Today);?></h4>
									<h4><br></h4>
								</div>
								<div class="icon icon-sml" style="display: block !important;">
									<i class="fa fa-envelope-open-text"></i>
								</div>
							</div>
						</a>
					</div>
					<?php
						}
					?>
				</div>
				<?php
					}
				?>
			</div>
		</section>
	</div>
	<?php
		if (defined("AppView") == false)
		{
	?>
	<?php
		include("includes/footer.php");
	?>
	<?php
		}
	?>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<?php
	if (defined("AppView"))
	{
?>
<script>
	$(function () {
		$("a").click(function(evt) {
			evt.preventDefault();
		});
	});
</script>
<?php
	}
?>
</body>
</html>