	function Verify(Type,TextBox,Empty,ConfigID)
	{
		<?php CheckRight("Edit");?>
		if (Type == "Text")
		{
			if (IsEmpty(document.Form.elements[TextBox].value) == true && Empty == false)
			{
				ShowError(true,"Error!","Please Enter Valid Text ...","",TextBox)
				return(false);
			}
		}
		else if (Type == "Phone")
		{
			if (IsPhone(document.Form.elements[TextBox].value,Empty) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Phone ...","",TextBox)
				return(false);
			}
		}
		else if (Type == "Email")
		{
			if (IsEmail(document.Form.elements[TextBox].value,Empty) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Email ...","",TextBox)
				return(false);
			}
		}
		else if (Type == "Boolean")
		{
			if (document.Form.elements[TextBox].value != "0" && document.Form.elements[TextBox].value != "1")
			{
				ShowError(true,"Error!","Please Enter Value 1 For Yes or 0 For No ...","",TextBox)
				return(false);
			}
		}
		else if (Type == "Integer")
		{
			if (IsNumber(document.Form.elements[TextBox].value,Empty,false,0) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Numeric Whole Number Value ...","",TextBox)
				return(false);
			}
		}
		else if (Type == "Double")
		{
			if (IsNumber(document.Form.elements[TextBox].value,Empty,true,0) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Numeric Decimal Value ...","",TextBox)
				return(false);
			}
			else
			{
				document.Form.elements[TextBox].value = ShowFloat(document.Form.elements[TextBox].value,2);
			}
		}
		$.confirm({
			title: "Saving!",
			content: "Are You Sure You Want To Save This Setting ?",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			buttons: {
				"confirm": {
					text: "Yes",
					btnClass: "btn-blue",
					keys: ['enter'],
					action: function() {
						$.confirm({
							content: function () {
								var self = this;
								return $.ajax({
									url: '../ajax',
									dataType: 'JSON',
									method: 'POST',
									data: {"Parent":"SaveSetting","ConfigID":ConfigID,"ConfigValue":document.Form.elements[TextBox].value}
									}).done(function (response) {
										self.setTitle("Saved!");
										self.setContent(response.Message);
									}).fail(function(){
										self.setContent('Error completing operation. Please try again ...');
									});
							},
							buttons: {
								"OK": {
									text: "OK",
									btnClass: "btn-blue"
								}
							},
							onClose: function () {
							}
						});
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
				}
			}
		});
	}
