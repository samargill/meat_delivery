<?php
	$Today = mktime(0,0,0,date("m"),date("d"),date("Y"));
	// Yesterday
	$YesterdayStart   = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
	// Yesterday - 1
	$Yesterday1Start  = mktime(0,0,0,date("m"),date("d")-2,date("Y"));
	// Yesterday - 2
	$Yesterday2Start  = mktime(0,0,0,date("m"),date("d")-3,date("Y"));
	// Yesterday - 3
	$Yesterday3Start  = mktime(0,0,0,date("m"),date("d")-4,date("Y"));
	// Last 7 Days
	$Yesterday7Start  = mktime(0,0,0,date("m"),date("d")-6,date("Y"));
	// Last 30 Days
	$Yesterday30Start = mktime(0,0,0,date("m"),date("d")-30,date("Y"));
	
	if (strlen($GLOBALS["DateRangePickerFormatSave"]) == 19)
	{
		$TimePicker = "true";
		$TimePickerFormat = "DD-MM-YYYY HH:mm:ss";
	}
	else
	{
		$TimePicker = "false";
		$TimePickerFormat = "DD-MM-YYYY";
	}
?>
<!-- Date Range Picker -->
	<script src="../../plugins/daterangepicker/new/moment.min.js"></script>
	<script src="../../plugins/daterangepicker/new/daterangepicker.js"></script>
	<script>
		// Date & Time Range Picker
		$(function() {
			$('input[name="cboDate"]').daterangepicker({
				<?php
					if ($TimePicker == "true")
					{
				?>
				timePicker: true,
				timePickerIncrement: 1,
				timePicker24Hour: true,
				timePickerSeconds: true,
				<?php
					}
				?>
				startDate: "<?php echo(date($GLOBALS["DateRangePickerFormatShow"],strtotime($txtStartDate)));?>",
				endDate: "<?php echo(date($GLOBALS["DateRangePickerFormatShow"],strtotime($txtCloseDate)));?>",
				<?php
					if (isset($GLOBALS["DateRangePickerMin"]))
					{
				?>
				minDate: "<?php echo($GLOBALS["DateRangePickerMin"]);?>",
				<?php
					}
				?>
				<?php
					if (isset($GLOBALS["DateRangePickerMax"]))
					{
				?>
				maxDate: "<?php echo($GLOBALS["DateRangePickerMax"]);?>",
				<?php
					}
				?>
				linkedCalendars: false,
				opens: "<?php echo($GLOBALS["DateRangePickerAlign"]);?>",
				<?php
					if ($GLOBALS["DateRangePickerSingle"] == true)
					{
				?>
				singleDatePicker: true,
				<?php
					}
				?>
				locale: {
					format: '<?php echo($TimePickerFormat);?>',
					firstDay: 1
				},
				<?php
					if ($GLOBALS["DateRangePickerSingle"] == false)
					{
				?>
				ranges: {
					'Tomorrow': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 2 * 86400 - 1));?>'],
					'Today': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Yesterday': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$YesterdayStart));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Yesterday - 1': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Yesterday1Start));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Yesterday - 2': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Yesterday2Start));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Yesterday - 3': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Yesterday3Start));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Last 7 Days': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],  $Yesterday7Start));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Last 30 Days': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"], $Yesterday30Start));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					<?php
						$MonthStart = mktime(0,0,0,date("m"),1,date("Y"));
						$MonthClose = mktime(23,59,59,date("m"),date("t",$MonthStart),date("Y"));
					?>
					'This Month': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$MonthStart));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$MonthClose));?>'],
					<?php
						$MonthStart = mktime(0,0,0,date("m")-1,1,date("Y"));
						$MonthClose = mktime(23,59,59,date("m",$MonthStart),date("t",$MonthStart),date("Y",$MonthStart));
					?>
					'Last Month': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$MonthStart));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$MonthClose));?>']
				}
				<?php
					}
				?>
			},
			function(start, end, label) {
				document.Form.txtStartDate.value = start.format("<?php echo($GLOBALS["DateRangePickerFormatSave"]);?>");
				document.Form.txtCloseDate.value = end.format("<?php echo($GLOBALS["DateRangePickerFormatSave"]);?>");
			});
		});
	</script>