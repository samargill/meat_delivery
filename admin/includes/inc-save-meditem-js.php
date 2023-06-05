
<script>
	function ChangeMedItem(BookID)
	{
		$("#Modal-Change-MedItem").modal();
		document.FrmChangeMedItem.BookID.value = BookID;
		$.ajax({
			url: "../ajaxs/get-meditemno",
			method: "POST",
			data: {
				"BookID":BookID
			},
			success:function(response) {
				$("#txtMedItemNo").val(response);
			},
			error: function(jqXHR,exception) {
				self.setTitle("Error!");
				self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
			}
		});
	}
	function SaveMedItem()
	{
		var Result = ResultData = "";
		var BookID = document.FrmChangeMedItem.BookID.value;
		var MedItemNo = $("#txtMedItemNo").val();
		MedItemNo = MedItemNo.split(',').map( value => value.trim() ).join(',');
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
					url: '../ajaxs/save-meditemno',
					dataType: 'JSON',
					method: 'POST',
					timeout: 3000,
					data: {
						"BookID": BookID,
						"MedItemNo": MedItemNo
					},
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
							$("#Modal-Change-MedItem").modal("hide");
							var html = `<a href="javascript:ChangeMedItem('${BookID}');" class="pull-left" 
											data-toggle="tooltip" data-container="body" title="Click Here To Edit Medicare Item #">
											<i class="fa fa-edit"></i>
										</a> ${MedItemNo}`;
							$("#"+BookID).html(html);
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
