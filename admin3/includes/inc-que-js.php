
<script>
	// Load Controls
	var ObjInterval = null;
	function LoadFirstQuestion()
	{
		clearInterval(ObjInterval);
		GoNext(1,$("#FirstQueID").val(),0,0,-1);
	}
	var QueIndex = 0;
	var FamilyID = <?php echo $FamilyID; ?>;
	var Gender   = <?php echo($Gender); ?>;
	var DOB      = '<?php echo($DOB); ?>';
	var ShowForm = false;
	var Rejected = false;
	// Retry Values
	var RetryType    = "GoNext";
	var RetryIndex   = 0;
	var RetryQueID   = 0;
	var RetryQueType = 0;
	var RetryAnsID   = 0;
	var RetryTerms   = 0;
	GoNext(0,0,0,0,-1);
	// Validate & Load Next Question
	function GoNext(Index,QueID,QueType,AnsID,Terms)
	{
		//alert(QueID);
		//return(false);
		var GoNext = false;
		ShowForm = false;
		Rejected = false;
		// Retry Values
		RetryIndex   = Index;
		RetryQueID   = QueID;
		RetryQueType = QueType;
		RetryAnsID   = AnsID;
		RetryTerms   = Terms;
		if (AnsID == undefined)
		{
			var Value = document.FrmBook.elements["rdoQue"+Index+"[]"].value.split("-");
			AnsID = Value[1];
		}
		if (Index == 0)
			GoNext = true;
		else if (document.FrmBook.elements["Que-"+Index+"-Ans"].value == AnsID 
				|| document.FrmBook.elements["Que-"+Index+"-Ans"].value == 0)
		{
			GoNext = true;
		}
		
		if (Index < QueIndex)
		{
			for (i = Index + 1; i <= QueIndex; i++)
			{
				$("#Que-"+i).remove();
			}
			if ($("#Que-Reject").length > 0)
			{
				$("#Que-Reject").remove();
			}
			QueIndex = Index;
		}
		if (GoNext == true || true)
		{
			QueIndex++;
			$.ajax({
				url: "../ajaxs/ajax-que",
				type: "POST",
				dataType: "HTML",
				timeout: 3000,
				data:{
					"NextQuestion":"",
					"QueID":QueID,
					"AnsID":AnsID,
					"QueIndex":QueIndex,
					"FamilyID":FamilyID,
					"Gender":Gender,
					"DOB":DOB,
					"Terms":Terms
				},
				beforeSend:function() {
					RetryType = "GoNext";
				},
				success:function(response) {
					//console.log(response);
					if (response.indexOf("<<<Finish>>>") >= 0)
					{
						ShowForm = true;
						response = ReplaceChar(response,"<<<Finish>>>","");
					}
					else if (response.indexOf("<<<Reject>>>") >= 0)
					{
						Rejected = true;
						response = ReplaceChar(response,"<<<Reject>>>","");
					}
					if ($("#Que-Reject").length > 0)
					{
						$("#Que-Reject").remove();
					}
					if (response.length > 1)
					{
						$("#Que-Box").append(response);
						if (response.indexOf("<select") >= 0)
						{
							$("#rdoQue"+QueIndex).select2();
						}
						
						if (Rejected == false && QueIndex > 1 && ShowForm == false)
						{
							ScrollToContent("Que-"+QueIndex);
						}
					}
					if (ShowForm)
					{
						document.FrmBook.SubmitForm.value = "True";
					}
					if (Rejected)
					{
						document.FrmBook.SubmitForm.value = "False";
					}
					console.log("QueIndex = "+QueIndex+" | SubmitForm = "+document.FrmBook.SubmitForm.value+" | ShowForm = "+ShowForm+" | Terms = "+Terms);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
				},
				complete: function() {
					if (Index == 0)
					{
						ObjInterval = setInterval(LoadFirstQuestion,200);
					}
				}
			});
		}
	}
</script>
