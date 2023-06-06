var ShowLoading = "<div id=\"ShowLoading\" class=\"text-center\">" +
	"<img src=\"" + Path + "images/loader.gif\"><br \>Loading ... Please Wait</div>";

var ShowReload = "<div class=\"text-center text-danger\">" +
	"Click Retry To Try Loading Again ...</div>";

var PopupLoader = null;

function ShowLoader() {
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
				action: function () {
					this.setTitle("");
					this.setContent(ShowLoading);
					this.buttons.confirm.hide();
					LoadCars(0,12,0);
				}
			}
		},
		onOpenBefore: function () {
			this.buttons.confirm.hide();
		}
	});
}

function LoaderFailed() {
	PopupLoader.setTitle("Loading Failed ...");
	PopupLoader.setContent(ShowReload);
	PopupLoader.buttons.confirm.show();
}

var isMobile = false;

function CheckMobile() {
	if ($(window).width() <= 768) {
		isMobile = true;
	} else {
		isMobile = false;
	}
}
CheckMobile();

function ScrollToContent(ContentID) {
	var TopMinus = 50;
	if (isMobile) {
		TopMinus = 50;
	}
	var ScrLoc = $("#" + ContentID).offset().top - TopMinus;
	//alert("ScrollToContent = "+ContentID+" = "+ScrLoc);
	$('html, body').animate({
		scrollTop: ScrLoc
	}, 'slow');
}


function ShowError(Err, MsgTitle, MsgText, ScrollTo, FocusTo) {
	var MsgIcon = "";
	var MsgColor = "";
	if (Err == false) {
		MsgIcon = "check-circle";
		MsgColor = "red";
	} else {
		MsgIcon = "exclamation-triangle";
		MsgColor = "red";
	}
	$.alert({
		title: MsgTitle,
		icon: "fa fa-" + MsgIcon,
		content: MsgText,
		type: MsgColor,
		animation: "scale",
		closeAnimation: "scale",
		scrollToPreviousElement: false,
		scrollToPreviousElementAnimate: false,
		columnClass: "col-md-6 col-md-offset-3",
		zIndex: '1060',
		buttons: {
			confirm: {
				text: "OK",
				keys: ['enter'],
				action: function () {
					if (ScrollTo != undefined) {
						ScrollToContent(ScrollTo);
					}
					if (FocusTo != undefined) {
						$("#" + FocusTo).focus();
					}
				}
			}
		}
	});
}

// ===== Scroll To Top ==== 
$(window).scroll(function (event) {
	if ($(this).scrollTop() >= 50) {
		$('#Scroll-To-Top').fadeIn(200);
	} else {
		$('#Scroll-To-Top').fadeOut(200);
	}
});
$('#Scroll-To-Top').click(function () {
	$('body,html').animate({
		scrollTop: 0
	}, 800);
});