<script>
	function ShowRefusalReason()
	{
		$("#Modal-Book-Refused").modal();
	}

	function SaveRefusalReason()
	{
		if ($("#cboReason").val() == 0)
		{
			ShowError(true,"Error!","Please Choose Valid Booking Cancellation Reason ...",undefined,"cboReason");
			return(false);
		}
		if (IsEmpty($("#txtReason").val(),false) == true)
		{
			ShowError(true,"Error!","Please Enter Valid Booking Cancellation Reason ...",undefined,"txtReason");
			return(false);
		}
		// Save Reason To Database
		var Result  = "";
		var FrmData = new FormData(document.FrmBook);
		FrmData.append("cboReason",$("#cboReason").val());
		FrmData.append("txtReason",$("#txtReason").val());
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
					url: "../ajaxs/save-booking-refused",
					type: 'POST',
					dataType: 'JSON',
					data: FrmData,
					async: false,
					cache: false,
					processData: false,
					contentType: false,
					enctype: "multipart/form-data"
				}).done(function (response) {
					Result = response.Status;
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
							$("#Modal-Book-Refused").modal("hide");
							window.location = "consultation-add";
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
