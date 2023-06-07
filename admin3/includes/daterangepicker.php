<?php
	$Today = mktime(0,0,0,date("m"),date("d"),date("Y"));
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
	$DateRangePickerParams = "";
	if ($GLOBALS["DateRangePickerSingle"] == true)
	{
		$DateRangePickerParams = "singleDatePicker: true,";
	}
?>
<!-- Date Range Picker -->
	<script src="../../plugins/daterangepicker/moment.min.js"></script>
	<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
	<script>
		//Date & Time Range Picker
		$(function() {
			$('input[name="cboDate"]').daterangepicker({
				<?php
					if ($TimePicker == "true")
					{
				?>
				timePicker: true,
				timePickerIncrement: 1,
				timePicker24Hour: true,
				<?php
					}
				?>
				startDate: "<?php echo(date($GLOBALS["DateRangePickerFormatShow"],strtotime($txtStartDate)));?>",
				endDate: "<?php echo(date($GLOBALS["DateRangePickerFormatShow"],strtotime($txtCloseDate)));?>",
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
					format: '<?php echo($TimePickerFormat);?>'
				},
				<?php
					if ($GLOBALS["DateRangePickerSingle"] == false)
					{
				?>
				ranges: {
					'Tomorrow': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 2 * 86400 - 1));?>'],
					'Today': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Yesterday': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 1));?>'],
					'Yesterday - 1': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 2  * 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 1 * 86400 - 1));?>'],
					'Yesterday - 2': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 3  * 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 2 * 86400 - 1));?>'],
					'Yesterday - 3': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 4  * 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today - 3 * 86400 - 1));?>'],
					'Last 7 Days': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"],  $Today - 6  * 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
					'Last 30 Days': ['<?php echo(date($GLOBALS["DateRangePickerFormatShow"], $Today - 30 * 86400));?>', '<?php echo(date($GLOBALS["DateRangePickerFormatShow"],$Today + 86400 - 1));?>'],
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