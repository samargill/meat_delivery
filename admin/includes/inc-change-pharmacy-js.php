<script>
	// Load Pharmacy
	function LoadChangePharmacy()
	{
		if (document.Form.PatAddressID.value == 0)
		{
			return(false);
		}
		$.ajax({
			url: "../../../ajaxs/get-pharmacy",
			type: "POST",
			dataType: "HTML",
			timeout: 3000,
			data: {
				GetPharmacy: "",
				DataType: "Option",
				AreaID: document.Form.PatAddressID.value
			},
			success: function(response) {
				$("#cboPharmacy").html(response);
				$("#cboPharmacy").select2();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
			}
		});
	}
	function OpenChangePharmacy()
	{
		if (document.Form.Status.value != 0)
		{
			/*
			ShowError(true,"Error!","Pharmacy Details Cannot Be Changed. Consultation is Already Processed.",undefined,"");
			return(false);
			*/
		}
		$("#Modal-Change-Pharmacy").modal();
		if (document.Form.PatAddressID.value > 0)
		{
			LoadChangePharmacy();
		}
	}
	function SaveChangePharmacy()
	{
		if (document.FrmPharmacy.cboPharmacy.value == 0)
		{
			ShowError(true,"Error!","Please Select Nearest Pharmacy",undefined,"cboPharmacy");
			return(false);
		}
		var Result = "";
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
					url: "../ajaxs/save-change-pharmacy",
					dataType: "JSON",
					method: "POST",
					data: {
						"<?php echo($BookIDName);?>": document.Form.<?php echo($BookIDName);?>.value,
						"PharmacyID": document.FrmPharmacy.cboPharmacy.value
					}
					}).done(function (response) {
						Result      = response.Status;
						PhaName 	= response.PhaName;
						PhaAddress 	= response.PhaAddress;
						PhaPhone 	= response.PhaPhone;
						PhaFax 		= response.PhaFax;
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function (jqXHR,textStatus,errorThrown) {
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Done")
						{
							$("#Modal-Change-Pharmacy").modal("hide");
							document.Form.txtPhaID.value      = document.FrmPharmacy.cboPharmacy.value;
							document.Form.txtPhaName.value 	  = PhaName;
							document.Form.txtPhaAddress.value = PhaAddress;
							document.Form.txtPhaPhone.value   = PhaPhone;
							document.Form.txtPhaFax.value     = PhaFax;
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
