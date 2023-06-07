<script type="text/javascript">
	function VerifyFiles()
	{
		if (document.FrmAdd.cboInvestor.value == 0)
		{
			ShowError(true,"Error!","Please Select Investor !",undefined,"cboInvestor");
			return(false);
		}
		if (document.FrmAdd.cboDocuType.value == 0)
		{
			ShowError(true,"Error!","Please Select File Type !",undefined,"cboDocuType");
			return(false);
		}
		if (IsEmpty(document.FrmAdd.txtDesc.value) == true)
		{
			ShowError(true,"Error!","Please Enter Files Description !",undefined,"txtDesc");
			return(false);
		}
		var Files = document.getElementById('txtMyFile');
		var FileLength = Files.files.length;
		if (FileLength == 0)
		{
			ShowError(true,"Error!","Please Select at least one File to Uplaod !",undefined,"txtMyFile");
			return(false);
		}
		var Extension = "PDF";
		for (var i = 0; i < FileLength ; i++)
		{
			var Filename =  Files.files[i].name;
			if (Filename.lastIndexOf(".") > 0)
			{
				if (Filename.substr(Filename.lastIndexOf(".")).toUpperCase() != "." + Extension.toUpperCase())
				{
					ShowError(true,"Error!","Please Upload File Only in PDF !",undefined,"txtMyFile");
					return(false);
				}
			}
		}
		var FrmData = new FormData(document.FrmAdd);
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
					url: "../ajaxs/Investors-file-upload",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					async: false,
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
						Result = response.Status;
						self.setTitle(response.Status);
						self.setContent(response.Message);
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
							$("#Modal-AddFile").modal("hide");
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>