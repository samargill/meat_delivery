
<script>
	function CallStatus(BookID)
	{
		document.FrmCallStatus.txtBookID.value = BookID;
		$("#Modal-CallStatus").modal();
	}
	function SaveStatus()
	{
		if (document.FrmCallStatus.cboReason.value == 0)
		{
			ShowError(true,"Error!","Please Select Reason",undefined,"cboReason");
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
					url: "../ajaxs/save-book-call-status",
					dataType: "JSON",
					method: "POST",
					data: {
						"BookID"    : document.FrmCallStatus.txtBookID.value,
						"cboReason" : document.FrmCallStatus.cboReason.value
					}
					}).done(function (response) {
						Result = response.Status;
						alert(response.Message);
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
							$("#Modal-CallStatus").modal("hide");
							$("#cboReason").val(0).trigger("change");
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
