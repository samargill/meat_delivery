<?php
	$PageID = array(9,0,0);
	$PagePath = "../../";
	$PageMenu = "Reports & Graphs";
	$PageName = "New visitor Graphs";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_REQUEST['cboMonth']))
		$cboMonth = $_REQUEST['cboMonth'];
	else
		$cboMonth = 0;
	if (isset($_REQUEST['cboYear']))
		$cboYear = $_REQUEST['cboYear'];
	else
		$cboYear = 0;
	if (isset($_REQUEST['cboChart']))
		$cboChart = $_REQUEST['cboChart'];
	else
		$cboChart = 1;
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<!-- Morris Chart -->
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/morris/morris.css">
	<script>
		function Verify()
		{
			if (document.Form.cboMonth.value == 0)
			{
				document.Form.cboYear.value = 0;
			}
			if (document.Form.cboMonth.value > 0)
			{
				if (document.Form.cboYear.value == 0)
				{
					ShowError(true,"Error!","Please Select Year",undefined,"cboYear");
					return(false);
				}
			}
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?>  sidebar-collapse">
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper">
		<!-- Page Header -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageName);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
							<li class="breadcrumb-item active"><?php echo($PageName);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="user-visitor-graph" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<label>Select Month :</label>
									<div class="form-group">
										<?php
											//MonthsCombo("cboMonth",$cboMonth,"Last 30 Days","form-control select2","");
											MonthsCombo("cboMonth",$cboMonth,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<label>Select Year :</label>
									<div class="form-group">
										<?php
											YearsCombo("cboYear",2019,date("Y"),$cboYear,"form-control select2","");
										?>
									</div>
								</div>
								<div class="col-md-4">
									<label>Select Chart Type :</label>
									<div class="form-group">
										<select name="cboChart" id="cboChart" class="form-control select2" style="width: 100%;">
											<option value="1" <?php if ($cboChart == 1) echo("SELECTED");?>>Line Chart</option>
											<option value="2" <?php if ($cboChart == 2) echo("SELECTED");?>>Bar Chart</option>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-info" style="margin-top: 25px;" onclick="return Verify();">Search</button>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<!-- LINE CHART -->
									<br>
									<div class="card card-info">
										<div class="card-header">
											<h3 class="card-title">
												<i class="far fa-chart-bar"></i> Visitor Stats
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
												<canvas id="MyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
											</div>
										</div>
									</div>


									<!-- <div class="box box-info">
										<div class="box-header with-border">
											<h3 class="box-title">New Visitor Graph</h3>
										</div>
										<div class="box-body">
											<div class="chart">
												<canvas id="MyChart" style="height:250px"></canvas>
											</div>
										</div>
									</div> -->
								</div><!-- /.col (RIGHT) -->
							</div><!-- /.row -->
						</div>
					</form>
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
<!-- ChartJS 1.0.1 -->
<script src="<?php echo($PagePath);?>plugins/chartjs/Chart.min.js"></script>
<!-- Page Script -->
<?php
	if ($cboChart == 2)
		$ChartType = "Bar";
	else
		$ChartType = "Line";
	$GraphLabel = $StatData = "";
	if ($cboMonth > 0)
	{
		$CurDay  = mktime(0,0,0,$cboMonth,1,$cboYear);
		$LastDay = date("t",$CurDay);
	}
	else
	{
		$CurDay  = mktime(0,0,0,date("m"),date("d")-29,date("Y"));
		$LastDay = 30;
	}
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
			$StatData    .= ",";
		}
		$GraphLabel .= "\"".date_format($CurDay,"d-M")."\"";
		// Stats Data
		$Query = "SELECT COUNT(*) As Today".
			" FROM zstats".
			" WHERE statdate = '".date_format($CurDay,"Y-m-d")." %'";
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
		$StatData .= $Value;
	}
?>
<script>
	$(function ()
	{
		var MyChartCanvas = $("#MyChart").get(0).getContext("2d");
		var MyChart = new Chart(MyChartCanvas);
		var MyChartData =
		{
			labels: [<?php echo($GraphLabel);?>],
			datasets:[
			{
				label: "Visitor Stats",
				fillColor: "rgba(60,141,188,1)",
				strokeColor: "rgba(60,141,188,1)",
				pointColor: "#3b8bba",
				pointStrokeColor: "rgba(60,141,188,1)",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(220,220,220,1)",
				data: [<?php echo($StatData);?>]
			}
		]
		};
		var MyChartOptions =
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
		MyChartOptions.datasetFill = false;
		MyChart.<?php echo($ChartType);?>(MyChartData,MyChartOptions);
	});
	$(function () {
		//Initialize Select2 Elements
		$(".select2").select2();
	});
</script>
</body>
</html>