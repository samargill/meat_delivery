
<script>
	function ViewNotesLog()
	{
		if (document.Form.AdminType.value != 1)
		{
			ShowError(true,"Error!","Only An Admin Can Perform This Operation ...",undefined,"");
			return(false);
		}
		var BookType = "<?php echo($BookIDName);?>";
		BookType = BookType.substring(0,4).toUpperCase();
		var Url = "admin-notes-log?BookID="+BookType+"-"+document.Form.elements["<?php echo($BookIDName);?>"].value;
		var Win = Popup(Url,"KS_PrimeMedic_ViewNotesLog",740,1024,100,100);
		Win.focus();
	}
	function EditBookNotes()
	{
		if (![1, 2, 4].includes(parseInt(document.Form.AdminType.value)))
		{
			ShowError(true,"Error!","Only a Manager / Admin / Support Person can Perform This Operation ...",undefined,"");
			return(false);
		}
		if (document.Form.Status.value > 1)
		{
			ShowError(true,"Error!","You Cannot Add / Edit The Admin Notes ...<br><br>Booking is Rejected",undefined,"");
			return(false);
		}
		let OrderDays = parseInt(document.Form.elements["<?php echo(substr($BookIDName, 0, 4));?>Days"].value);
		if (OrderDays > 3000)
		{
			ShowError(true,"Error!","You Cannot Add / Edit The Admin Notes ...<br><br>Booking is Older Than 30 Days",undefined,"");
			return(false);
		}
		$("#Modal-Book-Notes").modal();
		$("#notes-title").html("Add Booking Notes");
		if (IsEmpty(document.Form.txtBookNotes.value) == false)
		{
			$("#notes-title").html("Edit Booking Notes");
		}
	}
	function SaveBookNotes()
	{
		if (IsEmpty(document.FrmBookNotes.txtBookNotes.value) == true)
		{
			ShowError(true,"Error!","Please Enter Valid Booking Notes ...",undefined,"txtBookNotes");
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
					url: "../ajaxs/save-admin-notes",
					dataType: "JSON",
					method: "POST",
					data: {
						"<?php echo($BookIDName);?>": document.Form.elements["<?php echo($BookIDName);?>"].value,
						"txtBookNotes": document.FrmBookNotes.txtBookNotes.value
						<?php
							if (isset($CertTable))
							{
								echo(", \"CertTable\": \"".$CertTable."\"");
							}
						?>
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
							$("#Modal-Book-Notes").modal("hide");
							document.Form.txtBookNotes.value = document.FrmBookNotes.txtBookNotes.value;
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
</script>
