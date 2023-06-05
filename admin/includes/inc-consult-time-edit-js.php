
<!-- Date Time Range Picker -->
<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/datepicker/datepicker3.css"/>
<!-- Date Time Range Picker -->
<script src="<?php echo($PagePath);?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
	$(function () {
		// Video Consultation Date
		var date = new Date();
		date.setDate(date.getDate());
		$('#txtConsultDate').datepicker({
			format: 'dd/mm/yyyy',
			autoclose: true,
			todayHighlight: true,
			startDate: date,
			templates: {
				leftArrow: '<i class="fa fa-arrow-left"></i>',
				rightArrow: '<i class="fa fa-arrow-right"></i>'
			},
		});
		// Load Time Slots
		if ($("#txtConsultDate").length)
		{
			LoadTimeSlots();
			$("#txtConsultDate").on("change", function(e) {
				LoadTimeSlots();
			});
		}
	});
	// Load Time Slot
	function LoadTimeSlots()
	{
		var txtDate = $("#txtConsultDate").val();
		$.ajax({
			url: "<?php echo($PagePath);?>../ajax",
			method: "POST",
			dataType: "HTML",
			timeout: 3000,
			data: {
				"GetTimeSlots": "",
				"Date": txtDate,
				"Type": "Option"
			},
			success:function (response) {
				$("#cboConsultTimeSlot").html(response);
			},
			error: function(objRequest, errortype) {
			}
		});
	}
	function EditConsultTime()
	{
		if (document.Form.Status.value != 0)
		{
			ShowError(true,"Error!","Consult Time Cannot Be Changed. Consultation is Already Processed.",undefined,"");
			return(false);
		}
		$("#Modal-Consult-Time").modal();
	}
	function SaveConsultTime()
	{
		if (document.FrmConsultTime.cboConsultTimeSlot.value == 0)
		{
			ShowError(true,"Error!","Please Select Time Slot",undefined,"");
			return(false);
		}
		var Result = ResultData = "";
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: "col-md-8 col-md-offset-2",
			content: function () {
				var self = this;
				return $.ajax({
					url: "../ajaxs/save-consult-time",
					dataType: "JSON",
					method: "POST",
					data: {
						"<?php echo($BookIDName);?>": document.Form.<?php echo($BookIDName);?>.value,
						"cboConsultTimeSlot": document.FrmConsultTime.cboConsultTimeSlot.value
					}
					}).done(function (response) {
						Result = response.Status;
						if (Result == "Done")
						{
							ResultData = response.ReturnID;
						}
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function() {
						self.setContent("Error Completing Operation. Please Try Again ...");
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Done")
						{
							$("#Modal-Consult-Time").modal("hide");
							$("#txtConsultTime").val(ReplaceChar(ResultData,"&nbsp;"," "));
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
