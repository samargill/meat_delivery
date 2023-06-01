<?php
	$Path = "../";
	$PageID = 2;
	include($Path."lib/variables.php");
	include($Path."lib/opencon.php");
	$LoginReq = false;
	include($Path."lib/session.php");
	include($Path."lib/functions.php");

	//Include GP config file && User class
	include("gpConfig.php");
	$authUrl = $gClient->createAuthUrl();
?>
<!doctype html>
<html>
<head>
	<?php include($Path."includes/inc-css.php");?>
</head>
<body>
	<?php include($Path."includes/header.php");?>
	
		<div class="container">
			<div class="row text-center" style="margin: 80px 0 30px 0;">
				<div class="col-sm-6 col-md-3 col-lg-3">
					<button type="button" class="btn btn-danger" onclick="window.location = '<?php echo $authUrl; ?>';" style=" width: 200px; height: 50px;">
						Login With Google
					</button>
				</div>
			</div>
		</div>
	<?php include($Path."includes/footer.php");?>
	<?php include($Path."includes/inc-js.php");?>
</body>
</html>