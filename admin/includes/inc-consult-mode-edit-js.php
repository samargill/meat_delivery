<script>
	function EditConsultMode()
	{
		if (document.Form.Status.value != 0)
		{
			ShowError(true,"Error!","Consult Mode Cannot Be Changed. Consultation is Already Processed.",undefined,"");
			return(false);
		}
		SetVideoMode();
		$("#Modal-Consult-Mode").modal();
	}

	// Toggle Video Consultation Mode
	function SetVideoMode()
	{
		if (document.FrmConsultMode.cboVideoMode.value == 0)
		{
			$("#Que-Form-VideoValue").hide();
		}
		else
		{
			var SelOption = $("#cboVideoMode").find("option:selected");
			$("#LblVideoMode").html(SelOption.attr("data-msg").replace("Your","Patient"));
			$("#Que-Form-VideoValue").show();
			if (SelOption.attr("data-type") == "Mobile")
			{
				$("#txtVideoMode").attr("maxlength",10);
				$("#txtVideoMode").attr("type","tel");
			}
			else if (SelOption.attr("data-type") == "Phone")
			{
				$("#txtVideoMode").attr("maxlength",20);
				$("#txtVideoMode").attr("type","tel");
			}
			else
			{
				$("#txtVideoMode").attr("maxlength",100);
				$("#txtVideoMode").attr("type","text");
			}
			$("#txtVideoMode").focus();
		}
	}

	function CheckVideoMode()
	{
		if (document.FrmConsultMode.cboVideoMode.value == 0)
		{
			ShowError(true,"Error!","Please Select Prefered Mode of Video Consultation",undefined,"cboVideoMode");
			return(false);
		}
		var SelOption = $("#cboVideoMode").find("option:selected");
		if (SelOption.attr("data-type") == "Mobile")
		{
			if (IsMobile(document.FrmConsultMode.txtVideoMode.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Mobile # on"+
					" Which "+SelOption.attr("data-name")+" is Active For Video Consultation."+
					"<br><br><ul><li>Must 10 Digit Long</li><li>No Spaces</li>"+
					"<li>No Country Code</li></ul>",undefined,"txtVideoMode");
				return(false);
			}
		}
		else if (SelOption.attr("data-type") == "Phone")
		{
			if (IsPhone(document.FrmConsultMode.txtVideoMode.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Phone # on"+
					" Which "+SelOption.attr("data-name")+" is Active For Video Consultation."+
					"<br><br><ul><li>Must 10 Digit Long</li><li>No Spaces</li>"+
					"<li>No Country Code</li></ul>",undefined,"txtVideoMode");
				return(false);
			}
		}
		else if (SelOption.attr("data-type") == "User")
		{
			if (IsSkype(document.FrmConsultMode.txtVideoMode.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid "+SelOption.attr("data-name")+" For Video Consultation."+
					"<br><br><ul><li>Must Start With A Char</li><li>No Space & Special Char</li></ul>",undefined,"txtVideoMode");
				return(false);
			}
		}
		else if (SelOption.attr("data-type") == "Email")
		{
			if (IsEmail(document.FrmConsultMode.txtVideoMode.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid "+SelOption.attr("data-name")+" For Video Consultation."+
					"<br><br><ul><li>Must Start With A Char</li><li>No Space & Special Char</li></ul>",undefined,"txtVideoMode");
				return(false);
			}
		}
	}

	function SaveConsultMode()
	{
		if (CheckVideoMode() == false)
		{
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
					url: "../ajaxs/save-consult-mode",
					dataType: "JSON",
					method: "POST",
					data: {
						"<?php echo($BookIDName);?>": document.Form.<?php echo($BookIDName);?>.value,
						"cboVideoMode": document.FrmConsultMode.cboVideoMode.value,
						"txtVideoMode": document.FrmConsultMode.txtVideoMode.value
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
							$("#Modal-Consult-Mode").modal("hide");
							$("#txtConsultMode").val($("#txtVideoMode").val());
							$("#spnConsultMode").text($("#cboVideoMode option:selected").text());
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
