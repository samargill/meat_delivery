
<script>
	$(function () {
		$(".select2").select2();
		// Patient Cared DOB
		var StartDate = new Date("1930-01-01");
		var CloseDate = new Date();
		$('#txtCaredDOB').datepicker({
			format: 'dd/mm/yyyy',
			startView: 2,
			autoclose: true,
			todayHighlight: true,
			startDate: StartDate,
			endDate: CloseDate,
			templates: {
				leftArrow: '<i class="far fa-arrow-left"></i>',
				rightArrow: '<i class="far fa-arrow-right"></i>'
			},
		});
	});
	function EditCaredDetails()
	{
		$("#Modal-Cared-Detail").modal();
	}
	function SaveCaredDetails()
	{
		var CertID 	 = document.Form.CertID.value;
		var ConsID 	 = document.Form.ConsID.value;
		if (IsEmpty(document.FrmCaredDetails.txtCared.value) == true)
		{
			ShowError(true,"Error!","Please Enter The Person Name Being Cared",undefined,"txtCared");
			return(false);
		}
		if (document.FrmCaredDetails.cboCaredRela.value == 0)
		{
			ShowError(true,"Error!","Please Select The Relationship With The Person Being Cared",undefined,"cboCaredRela");
			return(false);
		}
		if (document.FrmCaredDetails.txtCaredDOB.value == "" || document.FrmCaredDetails.txtCaredDOB.value == "00/00/0000" 
			|| IsFullDate(document.FrmCaredDetails.txtCaredDOB.value,false) == false)
		{
			ShowError(true,"Error!","Please Select Date of Birth of The Person Being Cared",undefined,"txtCaredDOB");
			return(false);
		}
		if (IsEmpty(document.FrmCaredDetails.txtCaredCond.value) == true)
		{
			ShowError(true,"Error!","Please Tell Us About The Medical Condition of The Person Being Cared",undefined,"txtCaredCond");
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
					url: "../ajax",
					dataType: "JSON",
					method: "POST",
					data: {
						"Parent": "SaveCaredDetail",
						"CertID": CertID,
						"txtCared": document.FrmCaredDetails.txtCared.value,
						"cboCaredRela": document.FrmCaredDetails.cboCaredRela.value,
						"txtCaredDOB": document.FrmCaredDetails.txtCaredDOB.value,
						"txtCaredCond": document.FrmCaredDetails.txtCaredCond.value
					}
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
							$("#Modal-Cared-Detail").modal("hide");
							window.location = "certificate-edit?ConsID="+ConsID+"&CertType=1";
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
