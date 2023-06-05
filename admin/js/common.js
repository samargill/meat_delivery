
var Path = "../<?php echo($PagePath);?>";
var Path = "";
var ShowLoading = "<div id=\"ShowLoading\" class=\"text-center\">"+
	"<img src=\""+Path+"../../../images/loader.gif\"><br \>Loading ... Please Wait</div>";

var ShowReload = "<div class=\"text-center text-danger\">"+
	"Click Retry To Try Loading Again ...</div>";

var PopupLoader = null;

function ShowLoader()
{
	PopupLoader = $.alert({
		title: "",
		content: ShowLoading,
		type: "red",
		animation: "scale",
		closeAnimation: "scale",
		scrollToPreviousElement: false,
		scrollToPreviousElementAnimate: false,
		buttons: {
			confirm: {
				text: "Retry",
				keys: ['enter'],
				action: function() {
					PopupLoader.setTitle("");
					PopupLoader.setContent(ShowLoading);
					PopupLoader.buttons.confirm.hide();
					//ShowLoaderRetry();
				}
			}
		},
		onOpenBefore: function() {
			PopupLoader.buttons.confirm.hide();
		}
	});
}

function LoaderFailed()
{
	PopupLoader.setTitle("Loading Failed ...");
	PopupLoader.setContent(ShowReload);
	PopupLoader.buttons.confirm.show();
}

function ScrollToContent(ContentID)
{
	//alert("Scrolling To "+ContentID);
	var TopMinus = 0;
	$('html, body').animate({
		scrollTop: $("#"+ContentID).offset().top - TopMinus
	}, 'slow');
}

function ShowError(Err,MsgTitle,MsgText,ScrollTo,FocusTo)
{
	var MsgIcon  = "";
	var MsgColor = "";
	if (Err == false)
	{
		MsgIcon  = "rocket";
		MsgColor = "green";
	}
	else
	{
		MsgIcon  = "warning";
		MsgColor = "red";
	}
	$.alert({
		title: MsgTitle,
		icon: "fa fa-"+MsgIcon,
		content: MsgText,
		type: MsgColor,
		animation: "scale",
		closeAnimation: "scale",
		columnClass: 'col-md-6 col-md-offset-3',
		scrollToPreviousElement: false,
		scrollToPreviousElementAnimate: false,
		buttons: {
			"OK": {
				text: "OK",
				btnClass: "btn-blue",
				keys: ['enter'],
				action: function() {
				}
			}
		},
		onClose: function () {
			if (ScrollTo != undefined)
			{
				if (ScrollTo != "")
				{
					ScrollToContent(ScrollTo);
				}
			}
			if (FocusTo != undefined)
			{
				if (document.getElementById(FocusTo).tagName == "SELECT")
				{
					$("#"+FocusTo).select2('open');
				}
				else 
				{
					$("#"+FocusTo).focus();
				}
			}
		}
	});
}

function EditPatient(PatientID)
{
	var Win = Popup("../patient/patientedit?PatientID="+PatientID,"KS_PrimeMedic_EditPatient",740,1024,100,100);
	Win.focus();
}

function ViewPatientHistory(PatientID)
{
	var Win = Popup("../patient/patient-history?PatientID="+PatientID,"KS_PrimeMedic_EditPatient",740,1024,100,100);
	Win.focus();
}