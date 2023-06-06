<?php
	$PageID = array(1,0,0);
	$PagePath = "../../";
	$PageMenu = "Clients";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
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
		<section class="content-header">
			<h1>View-Client-Details</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">View-Client-Details</li>
			</ol>
		</section>
		<!-- Main Content -->
		<section class="content">
			<div class="box box-primary">
				<div class="box-body">
					<?php
						$QuerySelect = "SELECT C.clientname, C.pkgtype, C.maxdevice, C.maxcampaign, P.pkgname, C.pkgexpiry,".
							" CU.mobile, CU.email, AC.countryname";
						$QueryJoin 	 = "".
							" FROM client C".
							" INNER JOIN package P ON C.pkgtype = P.pkgid".
							" INNER JOIN address_country AC ON C.countryid = AC.countryid".
							" INNER JOIN clientuser CU ON C.clientid = CU.clientid";
						$QueryWhere  = "".
							" WHERE C.clientid =".$_REQUEST['ClientID'];
						$Query  = $QuerySelect." ".$QueryJoin." ".$QueryWhere;
						$rstRow = mysqli_query($Conn,$Query);
						while ($objRow = mysqli_fetch_object($rstRow))
						{
					?>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Client Name:</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-user color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo($objRow->clientname);?>" data-mask readonly>
								</div>
							</div>
						</div>
						<div class="col-sm-6">			
							<div class="form-group">
								<label>Mobile No</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-phone color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo($objRow->mobile);?>" data-mask readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="row">	
						<div class="col-sm-6">		
							<div class="form-group">
								<label>Email</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-envelope color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo($objRow->email);?>" data-mask readonly>
								</div>
							</div>
						</div>
						<div class="col-sm-6">	
							<div class="form-group">
								<label>Country</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-globe color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo($objRow->countryname);?>" data-mask readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="row">	
						<div class="col-sm-6">	
							<div class="form-group">
								<label>Package Name</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-dropbox color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo($objRow->pkgname);?>" data-mask readonly>
								</div>
							</div>
						</div>
						<div class="col-sm-6">	
							<div class="form-group">
								<label>Package Expiry</label>
								<div class="input-group">
									<div class="input-group-addon group-addon-color">
										<i class="fa fa-hourglass color-white"></i>
									</div>
									<input type="text" class="form-control" value="<?php echo(ShowDate($objRow->pkgexpiry,0));?>" data-mask readonly>
								</div>
							</div>
						</div>
					</div>
					<?php
						}
					?>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="box box-primary box-solid collapsed-box">
									<div class="box-header with-border" data-widget="collapse" style="cursor: pointer;"><i class="fa fa-plus pull-right"></i>
										<h3 class="box-title"><b>Total Devices</b></h3>
									</div>
									<div class="box-body">
										<div class="row">
											<div class="col-sm-12">
												<table id="MyDataTable" class="table table-bordered table-hover">
													<thead>
														<tr>
															<th width="10%"  style="text-align:center;">Sr #</th>
															<th width="25%" style="text-align:left;"  >Mobile Number</th>
															<th width="30%" style="text-align:left;"  >Mobile Name</th>
															<th width="30%" style="text-align:left;"  >Device Status</th>
														</tr>
													</thead>
													<tbody>
														<?php
															$Index = 0; 
															$QueryDevice  = "SELECT CM.mobileno, CM.mobilename, CM.token".
																" FROM clientmobile CM".
																" INNER JOIN client C ON CM.clientid = C.clientid".
																" WHERE C.clientid =".$_REQUEST['ClientID'];
															$rstDevice =	mysqli_query($Conn,$QueryDevice);
															while ($objDevice = mysqli_fetch_object($rstDevice))
															{
																$Index++;
																if ($objDevice->token != "")
																	$DeviceStatus = "Register";	
																else
																	$DeviceStatus = "Device Not Register";
														?>
														<tr>
															<td><?php echo($Index);?></td>
															<td><?php echo($objDevice->mobileno);?></td>
															<td><?php echo($objDevice->mobilename);?></td>
															<td><?php echo($DeviceStatus);?></td>
														</tr>
														<?php
															}
														?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<div class="box box-primary box-solid collapsed-box">
									<div class="box-header with-border" data-widget="collapse" style="cursor: pointer;"><i class="fa fa-plus pull-right"></i>
										<h3 class="box-title"><b>Total Campaign</b></h3>
									</div>
									<div class="box-body" style="display:none;">
										<div class="row">
											<div class="col-sm-12">
												<table id="MyDataTable" class="table table-bordered table-hover">
													<thead>
														<tr>
															<th width="10%"  style="text-align:center;">Sr #</th>
															<th width="41%" style="text-align:left;"  >Campaign Name</th>
															<th width="49%" style="text-align:left;"  >Campaign Device</th>
														</tr>
													</thead>
													<tbody>
														<?php
															$Index = 0; 
															$QueryCampaign = "SELECT CM.mobilename, CM.mobileno, SQ.smsquename".
																" FROM clientmobile CM".
																" INNER JOIN smsque SQ ON SQ.clientmobid = CM.clientmobid".
																" WHERE CM.clientid =".$_REQUEST['ClientID'];
															$rstCampaign =	mysqli_query($Conn,$QueryCampaign);
															if (mysqli_num_rows($rstCampaign) > 0)
															{
																while ($objCampaign = mysqli_fetch_object($rstCampaign))
																{
																	$Index++;
														?>
														<tr>
															<td><?php echo($Index);?></td>
															<td><?php echo($objCampaign->smsquename);?></td>
															<td><?php echo($objCampaign->mobilename."&nbsp - &nbsp".$objCampaign->mobileno);?></td>
														</tr>
														<?php
																}
															}	
														?>
													</tbody>
												</table>
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
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
</body>
</html>