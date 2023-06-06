<?php
	$PagePath = "";
	$PageMenu = "Dashboard";
	include("lib/variables.php");
	include("lib/opencon.php");
	include("lib/session.php");

	if (isset($_REQUEST["Signout"]))
	{
		session_destroy();
		header("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo(WebsiteTitle);?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.5 -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="bootstrap/font-awesome/4.5.0/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="bootstrap/ionicons/2.0.1/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="dist/css/AdminLTE.css">
	<!-- AdminLTE Skins -->
	<link rel="stylesheet" href="dist/css/skins/<?php echo(WebsiteSkin);?>.css">
	<!-- bootstrap wysihtml5 - text editor -->
	<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="bootstrap/js/html5shiv.min.js"></script>
	<script src="bootstrap/js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
	<div class="wrapper">
		<?php
			include("includes/header.php");
		?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php
			include("includes/left.php");
		?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>You Don't Have Permission ...</h1>
			</section>
		</div><!-- /.content-wrapper -->
		<?php
			include("includes/footer.php");
		?>
	</div><!-- ./wrapper -->
	<!-- jQuery 2.1.4 -->
	<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="plugins/jQuery/jquery-ui.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>$.widget.bridge('uibutton', $.ui.button);</script>
	<!-- Bootstrap 3.3.5 -->
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Bootstrap WYSIHTML5 -->
	<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
	<!-- Slimscroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.min.js"></script>
	<!-- AdminLTE App -->
	<script src="dist/js/app.min.js"></script>
</body>
</html>