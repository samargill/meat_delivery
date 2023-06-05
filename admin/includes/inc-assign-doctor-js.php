
<script>
	var DocTable;
	$(function () {
		DocTable = $('#Doc-DataTable').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": true,
			"ordering": false,
			"info": true,
			"bInfo" : false,
			"autoWidth": false
		});
	});
	function AssignDoc(BookID)
	{
		$("#Modal-Assign-Doc").modal();
		document.FrmAssignDoc.BookID.value = BookID;
		var TrData = TrStatus = "";
		var Index = 0;
		$.ajax({
			url: "../ajaxs/load-assign-doctors",
			dataType: "JSON",
			method: "POST",
			data: {
			},
			beforeSend:function(){
			},
			success:function(response) {
				DocTable.clear();
				var RowID = 0;
				$.each(response.Data, function (key,data) {
					if (data.DocStatus == 1)
						TrStatus = "<i class=\"fa fa-check-circle-o text-success\"></i> Online";
					else
						TrStatus = "<i class=\"fa fa-times-circle-o text-danger\"></i> Offline";
					TrButton = "<button type=\"button\" class=\"btn btn-success\" onclick=\"return SaveAssignDoc("+(RowID++)+","+data.DocID+");\">Assign</button>";
					DocTable.row.add([
						++Index,
						data.DocName,
						TrStatus,
						data.DocBook,
						TrButton
						]).draw(false);
				});
			},
			error: function(jqXHR,exception) {
				self.setTitle("Error!");
				self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
			}
		});
	}
	function SaveAssignDoc(RowID,DocID)
	{
		var BookID = document.FrmAssignDoc.BookID.value;
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
					url: "../ajaxs/save-assign-booking",
					dataType: "JSON",
					method: "POST",
					data: {
						"BookID": BookID,
						"DocID": DocID
					}
					}).done(function (response) {
						Result = response.Status;
						self.setTitle(response.Status);
						self.setContent(response.Message);
						if (Result == "Done")
						{
							if (DocID == 0)
								$("#SpanDr-"+BookID).html("");
							else
								$("#SpanDr-"+BookID).html(DocTable.cell(RowID,1).data());
							$("#Modal-Assign-Doc").modal("hide");
						}
					}).fail(function (jqXHR,textStatus,errorThrown) {
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
