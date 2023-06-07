<script>
	function SaveDoc(ConsID,DocType,Index)
	{
		$("#Modal-Save-Doc").modal();
		document.FrmSaveDoc.ConsID.value  = ConsID;
		document.FrmSaveDoc.DocType.value = DocType;
		document.FrmSaveDoc.Index.value   = Index;
		if (DocType == 1)
		{
			$("#SpDetail").show();
			$("#GpDetail").hide();
			$(".modal-title").html("Save Specialist Details");
			$(".submit-detail").html("<i class=\"fa fa-stethoscope\"></i> &nbsp; Save Specialist");
		}
		else
		{
			$("#GpDetail").show();
			$("#SpDetail").hide();
			$(".modal-title").html("Save GP Details");
			$(".submit-detail").html("<i class=\"fa fa-stethoscope\"></i> &nbsp; Save GP");
		}
	}

	function SaveDetails()
	{
		if (document.FrmSaveDoc.DocType.value == 1)
		{
			if (IsEmpty(document.FrmSaveDoc.txtSpName.value) == true)
			{
				ShowError(true,"Error!","Please Enter Specialist Name",undefined,"txtSpName");
				return(false);
			}
			if (document.FrmSaveDoc.txtSpSuburb.value == 0)
			{
				ShowError(true,"Error!","Please Select Specialist Suburb",undefined,"txtSpSuburb");
				return(false);
			}
			if (IsEmpty(document.FrmSaveDoc.txtSpAddress.value) == true)
			{
				ShowError(true,"Error!","Please Enter Specialist Address",undefined,"txtSpAddress");
				return(false);
			}
			if (IsPhone(document.FrmSaveDoc.txtSpPhone.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Specialist Phone #",undefined,"txtSpPhone");
				return(false);
			}
			if (IsPhone(document.FrmSaveDoc.txtSpFax.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Specialist Fax #",undefined,"txtSpFax");
				return(false);
			}
			var Result = "";
			var returnID = 0;
			$.confirm({
				title: "Processing ...",
				content: "",
				icon: "fa fa-cog",
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
							"Parent": "SaveSpec",
							"ConsID": document.FrmSaveDoc.ConsID.value,
							"txtSpName": document.FrmSaveDoc.txtSpName.value,
							"txtSpPhone": document.FrmSaveDoc.txtSpPhone.value,
							"txtSpFax": document.FrmSaveDoc.txtSpFax.value,
							"txtSpSuburb": document.FrmSaveDoc.txtSpSuburb.value,
							"txtSpAddress": document.FrmSaveDoc.txtSpAddress.value
						}
						}).done(function (response) {
							Result 	 = response.Status;
							returnID = response.ReturnID;
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}).fail(function(){
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
								if (returnID == 9999999)
								{
									document.FrmSaveDoc.DocType.value = 2;
									$("#GpDetail").show();
									$("#SpDetail").hide();
									$(".modal-title").html("Save GP Details");
									$(".submit-detail").html("<i class=\"fa fa-stethoscope\"></i> &nbsp; Save GP");
								}
								else
								{
									$("#Modal-Save-Doc").modal("hide");
									$("#Row"+document.FrmSaveDoc.Index.value).css({"background-color":"#86eabc"});
								}
							}
						}
					}
				},
				onClose: function () {
				}
			});
		}
		else
		{
			if (IsEmpty(document.FrmSaveDoc.txtGpName.value) == true)
			{
				ShowError(true,"Error!","Please Enter GP Name",undefined,"txtGpName");
				return(false);
			}
			if (document.FrmSaveDoc.txtGpSuburb.value == 0)
			{
				ShowError(true,"Error!","Please Select GP Suburb",undefined,"txtGpSuburb");
				return(false);
			}
			if (IsEmpty(document.FrmSaveDoc.txtGpAddress.value) == true)
			{
				ShowError(true,"Error!","Please Enter GP Address",undefined,"txtGpAddress");
				return(false);
			}
			if (IsPhone(document.FrmSaveDoc.txtGpPhone.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter GP Phone #",undefined,"txtGpPhone");
				return(false);
			}
			if (IsPhone(document.FrmSaveDoc.txtGpFax.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter GP Fax #",undefined,"txtGpFax");
				return(false);
			}
			var Result = "";
			$.confirm({
				title: "Processing ...",
				content: "",
				icon: "fa fa-cog",
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
							"Parent": "SaveGP",
							"ConsID": document.FrmSaveDoc.ConsID.value,
							"txtGPName": document.FrmSaveDoc.txtGpName.value,
							"txtGPPhone": document.FrmSaveDoc.txtGpPhone.value,
							"txtGPFax": document.FrmSaveDoc.txtGpFax.value,
							"txtGPSuburb": document.FrmSaveDoc.txtGpSuburb.value,
							"txtGPAddress": document.FrmSaveDoc.txtGpAddress.value
						}
						}).done(function (response) {
							Result = response.Status;
							self.setTitle(response.Status);
							self.setContent(response.Message);
						}).fail(function(){
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
								$("#Modal-Save-Doc").modal("hide");
								$("#Row"+document.FrmSaveDoc.Index.value).css({"background-color":"#86eabc"});
							}
						}
					}
				},
				onClose: function () {
				}
			});
		}
	}
</script>
<script type="text/javascript">
	<?php
		$DocList = array(
			array("Name" => "Sp"),
			array("Name" => "Gp")
		);
		for ($i = 0; $i < count($DocList); $i++)
		{
	?>
	$(document).ready(function () {
		if ($("#cbo<?php echo($DocList[$i]["Name"]);?>Name").length)
		{
			$("#cbo<?php echo($DocList[$i]["Name"]);?>Name").select2({
				minimumInputLength: 2,
				tags: true,
				createTag: function(params) {
					return {
						id: -1,
						text: params.term
					}
				},
				ajax: {
					url: "../ajax",
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (params) {
						return {
							Parent: "GetReferralDoc",
							q: params.term,
							DocType: 0,
							Suburb: document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Suburb.value
						};
					},
					processResults: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.text,
									id: item.id
								}
							})
						};
					}
				}
			});
			$("#cbo<?php echo($DocList[$i]["Name"]);?>Name").on("select2:select", function (e) {
				if ($("#cbo<?php echo($DocList[$i]["Name"]);?>Name").val() <= 0)
				{
					if ($("#cbo<?php echo($DocList[$i]["Name"]);?>Name").val() == -1)
						document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Name.value = $("#cbo<?php echo($DocList[$i]["Name"]);?>Name").select2('data')[0].text;
					else
						document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Name.value = "";
					document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Phone.value = "";
					document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Fax.value = "";
					document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Address.value = "";
				}
				else
				{
					$.ajax({
						url: "../ajax",
						dataType: "JSON",
						method: "POST",
						timeout: 3000,
						data: {
							"Parent":"GetReferralDocDetail",
							"DocID": $("#cbo<?php echo($DocList[$i]["Name"]);?>Name").val()
						},
						beforeSend:function(){
						},
						success:function(response) {
							console.log(response);
							if (response.Error != undefined)
							{
								ShowError(true,"Error!","Unable To Find Doctor Detail. Please Try Again ...",undefined,"");
								return(false);
							}
							else
							{
								document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Name.value = response.Name;
								document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Phone.value = response.Phone;
								document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Fax.value = response.Fax;
								var newOption = new Option(response.SuburbText,response.Suburb,true,true);
								$("#txt<?php echo($DocList[$i]["Name"]);?>Suburb").append(newOption).trigger("change");
								document.FrmSaveDoc.txt<?php echo($DocList[$i]["Name"]);?>Address.value = response.Address;
							}
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							console.log("Error in Ajax");
						}
					});
				}
			});
		}
	});
	<?php
		}
	?>
</script>