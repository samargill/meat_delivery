<?php
	$PageID = array(2,0,0);
	$PagePath = "../../";
	$PageMenu = "Device Detail";
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
	<style type="text/css">
		.group-addon-color
		{
			background-color: #3c8dbc !important;
		}
		.color-white
		{
			color: #fff;
		}
	</style>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper" style="margin-left:0px;">
		<!-- Page Header -->
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
				<div class="card card-outline card-info">
					<div class="card-body">
						<?php
							$Index = 1;
							$Query = "SELECT C.clientname, C.pkgexpiry, C.adddate, CU.verifydate, CU.email, P.pkgname".
								" FROM client C".
								" INNER JOIN clienthavemob CHM ON C.clientid = CHM.clientid".
								" INNER JOIN package P ON C.pkgtype = P.pkgid".
								" INNER JOIN clientuser CU ON C.clientid = CU.clientid AND CU.usertype = 1".
								" WHERE CHM.mobileid =".$_REQUEST['DeviceID']." AND CHM.clientid > 1";
							$rstDevice = mysqli_query($Conn,$Query);
							if (mysqli_num_rows($rstDevice) > 0)
							{
						?>
						<div class="row">
							<div class="col-md-12">
								<div class="card card-info">
									<div class="card-header">
									<h3 class="card-title">Sim Slots Detail</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
										</button>
									</div>
									</div>
									<div class="card-body">
										<table id="MyDataTable" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th width="6%" style="text-align:left;">Sr #</th>
													<th width="23%" style="text-align:left;">Client Name</th>
													<th width="23%" style="text-align:left;">Email</th>
													<th width="15%" style="text-align:left;">Pkg Name</th>
													<th width="20%" style="text-align:left;">Account Created</th>
													<th width="13%" style="text-align:left;">pkg Expiry</th>
												</tr>
											</thead>
											<?Php											
												while ($objDevice = mysqli_fetch_object($rstDevice))
												{	
													$BGColor = $Title = "";
													if ($objDevice->verifydate == NULL)
													{
														$BGColor = "bgcolor=\"#e8d5d6\"";
														$Title 	 = "title=\"Sim slot is not used properly \n because user is not verified\"";
													}
													else
													{
														$BGColor = "";
														$Title 	 = "";
													}
											?>		
											<tbody>
												<tr <?php echo($BGColor." ".$Title);?>>
													<td><?php echo($Index);?></td>
													<td><?php echo($objDevice->clientname);?></td>
													<td><?php echo($objDevice->email);?></td>
													<td><?php echo($objDevice->pkgname);?></td>
													<td><?php echo($objDevice->adddate);?></td>
													<td><?php echo($objDevice->pkgexpiry);?></td>
												</tr>
											</tbody>
											<?php
													$Index++;
												}
											?>
										</table>
									</div>
								</div>
							</div>
						</div>
						<?php
							}
						?>
					</div>
				</div>
			</div>
		</section>						
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
</body>
</html>