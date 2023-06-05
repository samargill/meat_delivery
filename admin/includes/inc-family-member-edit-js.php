<script>
	function EditFamily(PatientID,FamilyID)
	{
		$.ajax({
			url: "../ajaxs/get-set-family-member",
			method:"POST",
			dataType:"JSON",
			timeout: 3000,
			data: {
				"GetFamilyMember": FamilyID,
				"PatientID": PatientID
			},
			success:function(response) {
				if (typeof(response.Error) == "undefined")
				{
					$("#cboRelation").val(response.RelaID).trigger('change');
					$("#FrmPatient #txtFirstName").val(response.FirstName);
					$("#FrmPatient #txtLastName").val(response.LastName);
					$("#FrmPatient #txtMedNo").val(response.MedNo);
					$("#FrmPatient #txtMedRef").val(response.MedRef);
					$("#FrmPatient #txtMedExp").val(response.MedExp);
					$("#FrmPatient #cboMedCon").val(response.MedCon).trigger("change");
					$("#FrmPatient #cboGender").val(response.Gender).trigger('change');
					$("#FrmPatient #txtDOB").val(response.DOB);
					$("#FrmPatient #txtMobile").val(response.Mobile);
					$("#FrmPatient #cboDisab").val(response.Disab).trigger('change');
					document.FrmPatient.PatientID.value = PatientID;
					document.FrmPatient.FamilyID.value = FamilyID;
					$("#Modal_EditFamily").modal("show");
				}
				else
				{
					ShowError(true,"Error!","Failed To Load - Family Member Details - "+response.Error,undefined,undefined);
				}
			},
			error: function(objRequest,errortype) {
				alert("Error = "+errortype);
			}
		});
	}

	$("#FrmPatient").submit(function(e) {
		e.preventDefault();
		if (parseInt(document.FrmPatient.FamilyID.value) != 1)
		{
			if (document.FrmPatient.cboRelation.value == 0)
			{
				ShowError(true,"Error!","Please Select Relationship",undefined,undefined);
				return(false);
			}
		}
		if (IsEmpty(document.FrmPatient.txtFirstName.value) == true)
		{
			ShowError(true,"Error!","Please Enter First Name.",undefined,"FrmPatient #txtFirstName");
			return(false);
		}
		if (IsEmpty(document.FrmPatient.txtLastName.value) == true)
		{
			ShowError(true,"Error!","Please Enter Last Name",undefined,"FrmPatient #txtLastName");
			return(false);
		}
		if (document.FrmPatient.txtMedNo.value != "0")
		{
			if (IsMedicare(document.FrmPatient.txtMedNo.value) == false)
			{
				ShowError(true,"Error!","Please Enter Valid 10 Digit Medicare Number Including Issue Number (Without Spaces) ...",undefined,"FrmPatient #txtMedNo");
				return(false);
			}
			if (IsMedicareRef(document.FrmPatient.txtMedRef.value) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Single Digit Medicare Reference Number ...",undefined,"FrmPatient #txtMedRef");
				return(false);
			}
		}
		else
		{
			if (document.FrmPatient.txtMedRef.value != "0")
			{
				ShowError(true,"Error!","If Medicare is Zero Then Ref Should Be Zero ...",undefined,"FrmPatient #txtMedRef");
				return(false);
			}
		}
		if (document.FrmPatient.cboGender.value == 0)
		{
			ShowError(true,"Error!","Please Select Gender",undefined,"FrmPatient #cboGender");
			return(false);
		}
		if (document.FrmPatient.txtDOB.value == "" || document.FrmPatient.txtDOB.value == "00/00/0000" || IsFullDate(document.FrmPatient.txtDOB.value,false) == false)
		{
			ShowError(true,"Error!","Please Select / Type Date of Birth",undefined,"FrmPatient #txtDOB");
			return(false);
		}
		if (IsMobile(document.FrmPatient.txtMobile.value,false) == false)
		{
			ShowError(true,"Error!","Please Enter Valid Mobile #",undefined,"FrmPatient #txtMobile");
			return(false);
		}
		if (document.FrmPatient.cboDisab.value == 0)
		{
			ShowError(true,"Error!","Please Answer - Do Family Member Have Any Disability?",undefined,"FrmPatient #cboDisab");
			return(false);
		}
		var FrmData = new FormData(document.FrmPatient);
		var Result = "";
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: "col-md-6 col-md-offset-3",
			content: function () {
				var self = this;
				return $.ajax({
					url: "../ajaxs/get-set-family-member",
					type: "POST",
					data: FrmData,
					async: false,
					cache: false,
					contentType: false,
					processData: false
					}).done(function (response) {
						Result = response;
						if (Result == "Done")
						{
							self.setTitle("Saved!");
							self.setContent("Family Member Updated Successfully ...");
						}
						else
						{
							self.setTitle("Error!");
							self.setContent("Unable To Save Family Member ...<br><br>"+response);
						}
					}).fail(function(jqXHR,exception){
						self.setTitle("Error!");
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
							$("#Modal_EditFamily").modal("hide");
						}
					}
				}
			},
			onClose: function () {
			}
		});
	});
</script>
