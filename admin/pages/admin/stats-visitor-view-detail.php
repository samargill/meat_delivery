<?php
	$PageID = array(9,0,0);
	$PagePath = "../../";
	$PageName = "Visitor Details View";
	$PageMenu = "Visitor Details";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	// CheckRight("View");
	include($PagePath."lib/functions.php");
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
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
						<h1 class="m-0 text-dark"><?php echo($PageName);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">Home</li>
							<li class="breadcrumb-item"><?php echo($PageMenu);?></li>
							<li class="breadcrumb-item active"><a href="#"><?php echo($PageName);?></a></li>
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
							<div class="col-sm-6">
								<?php
									$Query = "SELECT Z.ipaddress, Z.useragent, AC.countryname". 
										" FROM zstats Z".
										" INNER JOIN address_country AC ON Z.countryid = AC.countryid".
										" WHERE Z.statid =".$_REQUEST['StatID'];
									$rstRow = mysqli_query($Conn,$Query);
									if (mysqli_num_rows($rstRow) > 0)
									{
										$objRow = mysqli_fetch_object($rstRow);	
								?>
								<div class="row">
									<div class="col-sm-6">
										<strong>Country :</strong>
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<button type="button" class="btn btn-info">
													<i class="fa fa-globe color-white"></i>
												</button>
											</div>
											<input type="text" class="form-control" value="<?php echo($objRow->countryname);?>" data-mask readonly>
										</div>
									</div>
									<div class="col-sm-6">
										<strong>IP Address :</strong>
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<button type="button" class="btn btn-info">
													<i class="fa fa-check color-white"></i>
												</button>
											</div>
											<input type="text" class="form-control" value="<?php echo($objRow->ipaddress);?>" data-mask readonly>
										</div>
									</div>
								</div>		
								<div class="form-group">
									<label>User Agent :</label>
									<textarea class="form-control" readonly rows="4"><?php echo($objRow->useragent);?></textarea>
								</div>
								<?php
									}
								?>
							</div>
							<div class="col-sm-6">	
								<div class="card card-info">	
									<div class="card-header">
										<h3 class="card-title">Visited Pages</h3>
										<div class="card-tools">
											<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
											</button>
										</div>
									</div>
									<div class="card-body">
										<table id="MyDataTable" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th width="10%" style="text-align:left;">Sr #</th>
													<th width="30%" style="text-align:left;"  >Page Name </th>
													<th width="20%" style="text-align:center;"  >Visit Count</th>
													<th width="20%" style="text-align:center;"  >Visit date</th>
													<th width="20%" style="text-align:center;"  >Last Visit</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$Query = "SELECT S.statid, S.useragent, WM.menuname,".
													" SD.pageid, SD.statdate, SD.visitcount, SD.lastvisit".
													" FROM zstats S".
													" Left JOIN zstatsdetail SD ON S.statid = SD.statid".
													" INNER JOIN webmenu WM ON SD.pageid = WM.menuid".
													" WHERE S.statid =".$_REQUEST['StatID']."".
													" ORDER BY SD.statdate";
												$rstRow = mysqli_query($Conn,$Query);
												if (mysqli_num_rows($rstRow) > 0)
												{
													while ($objRow = mysqli_fetch_object($rstRow))
													{
											?>
												<tr>
													<td align="left"><?php echo($objRow->statid);?></td>
													<td align="left"><?php echo($objRow->menuname);?></td>
													<td align="center"><?php echo($objRow->visitcount);?></td>
													<td align="center"><?php echo(ShowDate($objRow->statdate,3));?></td>
													<td align="center"><?php echo(ShowDate($objRow->lastvisit,3));?></td>
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
		</section>						
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
</body>
</html>