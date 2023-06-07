<?php
	$PageID = array(0,0,0);
	$PagePath = "../../";
	$PageMenu = "Dashboard";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	if (isset($_REQUEST["Signout"]))
	{
		session_destroy();
		header("Location: ../login");
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/iCheck/flat/blue.css">
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/morris/morris.css">
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/datepicker/bootstrap-datepicker3.min.css">
	<style>
		.small-box .icon-sml {
			top: 10px;
			font-size: 40px;
		}
		.small-box:hover .icon-sml {
			font-size: 48px;
		}
	</style>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<?php
		include($PagePath."includes/header.php");
	?>
	<!-- Left Menu -->
	<?php
		include($PagePath."includes/left.php");
	?>
	<!-- Page Content -->
	<div class="content-wrapper">
		<!-- Page Header BreadCrumb -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageMenu);?><small> (Control panel)</small></h1>
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
			<?php
				if (CheckRight("View","Return") == true)
				{
					$QueryDate = " BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'";
			?>
			<div class="row">
				<div class="col-lg-4 col-md-6 col-xs-4">
					<a href="<?php echo($PagePath);?>pages/admin/client-view?ChkDate">
					<div class="small-box bg-yellow">
						<?php
							$Query = "SELECT COUNT(*) As Total".
								" FROM client";
							$rstRow = mysqli_query($Conn,$Query);
							$objRow = mysqli_fetch_object($rstRow);
						?>
						<div class="inner">
							<h4>Total Clients : <?php echo($objRow->Total);?></h4>
							<h4>&nbsp;</h4>
							<h4>&nbsp;</h4>
						</div>
						<div class="icon">
							<i class="fas fa-users"></i>
						</div>
					</div>
					</a>
				</div><!-- ./col -->
				<div class="col-lg-4 col-md-6 col-xs-4">
					<a href="<?php echo($PagePath);?>pages/admin/device-view">
					<div class="small-box bg-primary">
						<?php
							$Query = "SELECT COUNT(*) As Today,".
								" SUM(CASE WHEN token IS NOT NULL THEN 1 ELSE 0 END) As Verified,".
								" SUM(CASE WHEN token IS NULL THEN 1 ELSE 0 END) As NotVerified".
								" FROM clientmobile";
							$rstRow = mysqli_query($Conn,$Query);
							$objRow = mysqli_fetch_object($rstRow);
						?>
						<div class="inner">
							<h4>Sms Devices : &nbsp; <?php echo($objRow->Today);?></h4>
							<h4>Registered : &nbsp; <?php echo($objRow->Verified);?></h4>
							<h4>UnRegistered : &nbsp; <?php echo($objRow->NotVerified);?></h4>
						</div>
						<div class="icon">
							<i class="fa fa-mobile-phone"></i>
						</div>
					</div>
					</a>
				</div>
				<div class="col-lg-4 col-md-6 col-xs-4">
					<a href="#">
					<div class="small-box bg-red">
						<?php
							$Query = "SELECT COUNT(*) As Today,".
								" SUM(CASE WHEN SQ.status = 0 THEN 1 ELSE 0 END) As Active,".
								" SUM(CASE WHEN SQ.status = 1 THEN 1 ELSE 0 END) As Disable".
								" FROM smsque SQ".
								" INNER JOIN clientmobile CM ON SQ.mobileid = CM.mobileid";
							$rstRow = mysqli_query($Conn,$Query);
							$objRow = mysqli_fetch_object($rstRow);
						?>
						<div class="inner">
							<h4>Total Campaign : &nbsp; <?php echo($objRow->Today);?></h4>
							<h4>Active : &nbsp; <?php echo($objRow->Active);?></h4>
							<h4>Disable : &nbsp; <?php echo($objRow->Disable);?></h4>
						</div>
						<div class="icon">
							<i class="fa fa-bullhorn"></i>
						</div>
					</div>
					</a>
				</div>
				<div class="col-lg-4 col-md-6 col-xs-4">
					<a href="<?php echo($PagePath);?>pages/campaign/campaign-view">
					<div class="small-box bg-info">
						<?php
							$Query = "SELECT COUNT(*) As Today".
								" FROM contact_us".
								" WHERE adddate ".$QueryDate;
							$rstRow = mysqli_query($Conn,$Query);
							$objRow = mysqli_fetch_object($rstRow);
						?>
						<div class="inner">
							<h4>Total Queries : <?php echo($objRow->Today);?></h4>
							<h4>&nbsp;</h4>
							<h4>&nbsp;</h4>
						</div>
						<div class="icon">
							<i class="fa fa-envelope"></i>
						</div>
					</div>
					</a>
				</div>
			</div>
			<div class="row">
				<!-- LINE CHART -->
				<?php
					if (false)
					{
				?>
				<section class="col-lg-6 connectedSortable">
					<br>
					<div class="card card-info">
						<div class="card-header">
							<h3 class="card-title">
								<i class="far fa-chart-bar"></i> SMS Detail Last 7 Days
							</h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
								<button type="button" class="btn btn-tool" data-card-widget="remove">
									<i class="fas fa-times"></i>
								</button>
							</div>
						</div>
						<div class="card-body">
							<div class="chart">
								<canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
							</div>
						</div>
					</div>
				</section>
				<!-- right col (We are only adding the ID to make the widgets sortable)-->
				
				<!-- Calendar -->
				<section class="col-lg-6 connectedSortable">
					<div class="card bg-gradient-success">
						<div class="card-header border-0">
							<h3 class="card-title">
								<i class="far fa-calendar-alt"></i>
								Calendar
							</h3>
							<div class="card-tools">
								 <div class="btn-group">
									<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
										<i class="fas fa-bars"></i>
									</button>
									<div class="dropdown-menu" role="menu">
										<a href="#" class="dropdown-item">Add new event</a>
										<a href="#" class="dropdown-item">Clear events</a>
										<div class="dropdown-divider"></div>
										<a href="#" class="dropdown-item">View calendar</a>
									</div>
								</div> 
								<button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
								<button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
									<i class="fas fa-times"></i>
								</button>
							</div>
						</div>
						<div class="card-body pt-0">
							<div id="calendar" style="width: 100%"></div>
						</div>
					</div>
				</section>
				<?php
					}
				?>
			</div>
			<?php
				}
			?>
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
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo($PagePath);?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>$.widget.bridge('uibutton', $.ui.button);</script>
<!-- jQuery Knob Chart -->
<script src="<?php echo($PagePath);?>plugins/knob/jquery.knob.js"></script>
<script src="<?php echo($PagePath);?>plugins/jquery-knob/jquery.knob.min.js"></script>

<!-- daterangepicker -->
<script src="<?php echo($PagePath);?>plugins/daterangepicker/moment.min.js"></script>
<script src="<?php echo($PagePath);?>plugins/daterangepicker/daterangepicker.js"></script>

<!-- datepicker -->
<script src="<?php echo($PagePath);?>plugins/datepicker/bootstrap-datepicker.min.js"></script>
<!-- Dashboard -->
<script src="<?php echo($PagePath);?>dist/js/pages/dashboard.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="<?php echo($PagePath);?>plugins/chartjs/Chart.min.js"></script>
<?php
	$GraphLabel = $SmsData = $BookingData = "";		
	$CurDay  = mktime(0,0,0,date("m"),date("d")-6,date("Y"));
	$LastDay = 7;
	$CurDay = date_create(date("Y-m-d",$CurDay));
	for ($i = 0; $i < $LastDay; $i++)
	{
		if ($i > 0)
		{
			date_add($CurDay,date_interval_create_from_date_string("1 days"));
		}
		if ($GraphLabel != "")
		{
			$GraphLabel  .= ",";
			$SmsData .= ",";
			$BookingData .= ",";
		}
		$GraphLabel .= "\"".date_format($CurDay,"d-M")."\"";
		// Sms Data
		// $Query = "SELECT COUNT(*) As Today 
		// 	FROM clientmobile CM
		// 	INNER JOIN smsque SQ ON CM.clientmobid = SQ.clientmobid
		// 	INNER JOIN smsquelist SQT ON SQ.smsqueid = SQT.smsqueid
		// 	WHERE CM.clientid = 1 AND SQT.smsaddtime LIKE '".date_format($CurDay,"Y-m-d")." %'";
		$Query = "SELECT COUNT(*) As Today 
			FROM clientmobile CM
			INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid
			INNER JOIN smsque SQ ON CM.mobileid = SQ.mobileid
			INNER JOIN smsquelist SQT ON SQ.smsqueid = SQT.smsqueid
			WHERE CHM.clientid = 1 AND SQT.smsaddtime LIKE '".date_format($CurDay,"Y-m-d")." %'";
		$rstRow = mysqli_query($Conn,$Query);
		$Value = 0;
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			if ($objRow->Today != NULL)
			{
				$Value = $objRow->Today;
			}
		}
		$SmsData .= $Value;
	}
?>
<script>
	$(function ()
	{
		var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
		var lineChart = new Chart(lineChartCanvas);
		var lineChartData =
		{
			labels: [<?php echo($GraphLabel);?>],
			datasets:[
			{
				label: "Signups",
				fillColor: "rgba(210, 214, 222, 1)",
				strokeColor: "rgba(60,141,188,1)",
				pointColor: "rgba(210, 214, 222, 1)",
				pointStrokeColor: "rgba(60,141,188,1)",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(210, 214, 222, 1)",
				data: [<?php echo($SmsData);?>]
			}
		]
		};
		var lineChartOptions =
		{
			pointDotStrokeWidth: 6,
			//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
			pointHitDetectionRadius: 20,
			//Boolean - Whether to show a stroke for datasets
			//Boolean - If we should show the scale at all
			showScale: true,
			//Boolean - Whether grid lines are shown across the chart
			scaleShowGridLines: true,
			//String - Colour of the grid lines
			scaleGridLineColor: "rgba(0,0,0,.05)",
			//Number - Width of the grid lines
			scaleGridLineWidth: 1,
			//Boolean - Whether to show horizontal lines (except X axis)
			scaleShowHorizontalLines: true,
			//Boolean - Whether to show vertical lines (except Y axis)
			scaleShowVerticalLines: false,
			//Boolean - Whether the line is curved between points
			bezierCurve: true,
			//Number - Tension of the bezier curve between points
			bezierCurveTension: 0.3,
			//Boolean - Whether to show a dot for each point
			pointDot: true,
			//Number - Radius of each point dot in pixels
			pointDotRadius: 4,
			//Number - Pixel width of point dot stroke
			datasetStroke: true,
			//Number - Pixel width of dataset stroke
			datasetStrokeWidth: 4,
			//Boolean - Whether to fill the dataset with a color
			datasetFill: true,
			//String - A legend template
			legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
			//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
			maintainAspectRatio: true,
			//Boolean - whether to make the chart responsive to window resizing
			responsive: true
		};
		//-------------
		//- LINE CHART -
		//--------------
		lineChartOptions.datasetFill = false;
		lineChart.Line(lineChartData, lineChartOptions);
	});
</script>
</body>
</html>