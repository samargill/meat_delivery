<?php
	$PageID = array(7,0,0);
	$PagePath = "../../";
	$PageMenu = "Messenger";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.line-clamp 
		{
			display: -webkit-box;
			-webkit-line-clamp: 1;
			-webkit-box-orient: vertical;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		// Contacts Preloader
		#preloader {
		    width: 100%;
		    height: 100vh;
		    background-color: #fff;
		    position: fixed;
		    z-index: 9999;
		    top: -150px;
		}
		#preloader-circle {
		    position:relative;
		    width: 80px;
		    height: 80px;
		    top: 43%;
		    margin: 0 auto;
		}
		#preloader-circle span {
		    position:absolute;
		    border: 8px solid rgba(144, 30, 193, 0.8);
		    border-top: 8px solid transparent;
		    border-radius:999px;
		}

		#preloader-circle span:nth-child(1){
		    width:80px;
		    height:80px;
		    animation: spin-1 2s infinite linear;
		}
		#preloader-circle span:nth-child(2){
		    top: 20px;
		    left: 20px;
		    width:40px;
		    height:40px;
		    animation: spin-2 1s infinite linear;
		}
		@keyframes spin-1 {
		    0% {transform: rotate(360deg); opacity: 1;}
		    50% {transform: rotate(180deg); opacity: 0.5;}
		    100% {transform: rotate(0deg); opacity: 1;}
		}
		@keyframes spin-2 {
		    0% {transform: rotate(0deg); opacity: 0.5;}
		    50% {transform: rotate(180deg); opacity: 1;}
		    100% {transform: rotate(360deg); opacity: 0.5;}
		}
		.pointer {cursor: pointer;}
	</style>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Top Menu -->
	<?php
		include($PagePath."includes/header.php");
	?>
	<!-- Left Menu -->
	<?php
		include($PagePath."includes/left.php");
	?>
	<!-- Page Content -->
	<div class="content-wrapper">
		<!-- Page Header  Breadcrumb-->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><?php echo($PageMenu);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main Content -->
		<section class="content">
			<div class="row">
				<div class="col-md-3">
					<div class="card card-outline card-primary">
						<div class="card-header with-border">
							<h3 class="card-title"><b>Mobile</b></h3>
							<div class="card-tools">
								<button class="btn btn-box-tool" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body with-border">
							<div id="Chat-Mobile">
								<div class="row">
									<div class="col-md-12">
										<?php
											// DBCombo("cboDevice","clientmobile","clientmobid","CONCAT(mobilename,' - ',mobileno)",
											// 	"WHERE clientid = ".$_SESSION[SessionID."ClientID"],0,"",
											// 	"form-control select2","onchange=\"\" style=\"width: 100%;\"");
											DBCombo("cboDevice","clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid","CM.mobileid","CONCAT(CM.mobilename,' - ',CM.mobileno)",
												"WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"],0,"",
												"form-control select2","onchange=\"\" style=\"width: 100%;\"");
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card card-primary">
						<div class="card-header with-border">
							<h3 class="card-title"><b>Contacts</b></h3>
							<div class="card-tools">
								<button class="btn btn-box-tool" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<!--  Search box with icon -->
						<div class="card-body with-border">
							<div id="Chat-Contact-Search">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Search">
											<span class="input-group-btn">
												<button class="btn btn-primary btn-flat" type="button"><i class="fa fa-search"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="box-body">
							<div class="direct-chat-messages" id="Chat-Contacts" style="height: 414px; overflow-x: hidden; overflow-y: hidden;">
								<!-- Preloader -->
								<div id="Chat-Contacts-Loader"><div id="preloader-circle"><span></span><span></span></div></div>
							</div>
						</div>	
					</div>
				</div>
				<div class="col-md-9">
					<!-- Direct Chat -->
					<div class="card card-outline card-primary direct-chat direct-chat-primary">
						<div class="card-header card-border">
							<h3 class="card-title">
								<b>Chat With <span id="Chat-Person">........</span></b>
							</h3>
							<div class="card-tools pull-right">
								<!-- <span data-toggle="tooltip" title="3 New Messages" class="badge bg-light-blue">3</span> -->
								<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
								<button type="button" class="btn btn-tool" data-card-widget="remove">
									<i class="fa fa-times"></i>
								</button>
							</div>
						</div>
						<!-- Start of box-body -->
						<div class="card-body" id="Chat_Coversation_Box" style="margin-bottom: 20px;">
							<div class="direct-chat-messages" id="Chat-Messages" style="height:506px;">
							</div>
						</div>
						<div class="card-footer">
							<form name="Form" action="" method="post">
								<div class="input-group" id="Send">
									<input type="text" name="txtMessage" id="txtMessage" placeholder="Type Reply Message ..." class="form-control">
									<span class="input-group-btn">
										<button type="button" name="btnReply" id="btnReply" onclick="return SendSms();" class="btn btn-primary btn-flat">
											<i class="fa fa-send"></i>
										</button>
									</span>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<script src="<?php echo($PagePath);?>plugins/daterangepicker/moment.js"></script>
<script type="text/javascript">
	var CurChatIndex = 0;
	var CurChatMobile = "";
	var CurChatTime = '2020-01-01 00:00:00';
	$(function(){
		//Init Select2
		$(".select2").select2();
		LoadContacts();
	});
	function LoadContacts()
	{
		$.ajax({
			url: "<?php echo($PagePath);?>pages/ajaxs/contact-list",
			method: "POST",
			data: {
				"ClientMobID": $("#cboDevice").val()
			},
			beforesend: function(){
				$('#Chat-Contacts-Loader').Show();
			},
			complete: function(){
				$('#Chat-Contacts').css('overflow-y','visible');
				$('#Chat-Contacts-Loader').hide();	
			},
			success: function(data){
				$("#Chat-Contacts").html(data);
			}
		});
	}
	// setInterval(function() {
	// 	if (CurChatMobile > 0)
	// 	{
	// 		LoadChat();
	// 	}
	// }, 5000);
	function ScrollToBottom()
	{
		$("html, #Chat-Messages").animate({ scrollTop: $(document).height() }, 1000);
	}
	function ContactClick(Index,Mobile)
	{
		if (CurChatIndex != Index)
		{
			if (CurChatIndex > 0)
			{
				$('#Contact'+CurChatIndex).css('background-color','#ffffff');
			}
			$('#Contact'+Index).css('background-color','#c7c1f0');
			$('#Chat-Person').html($('#Contact'+Index+"Name").html());
			if (CurChatIndex != Index)
			{
				$("#Chat-Messages").html("");
				CurChatTime = '2020-01-01 00:00:00';
			}
		}
		CurChatIndex = Index;
		CurChatMobile = Mobile;
		LoadChat();
	}
	function LoadChat()
	{
		$.ajax({
			url: "<?php echo($PagePath);?>pages/ajaxs/sms-chat-reload",
			method: "POST",
			dataType: "HTML",
			async: false,
			data: {
				"ClientMobID": $("#cboDevice").val(),
				"Mobile": CurChatMobile,
				"ChatTime": CurChatTime
			},
			success: function (response) {
				CurChatTime = moment().format('YYYY-MM-DD HH:mm:ss');
				if (response !== "")
				{
					var audio = new Audio('notify-sound.mp3');
					audio.play();
					// $("#Chat-Messages").append(response);
					$("#Chat-Messages").html(response);
					ScrollToBottom();
				}
				$("#txtMessage").focus();
			}
		});
		$('#Chat-Messages').scroll(function() {
			if ($('#SmsOffset').val() != 0)
			{
				var DivTop	  = $('#Chat-Messages').scrollTop();
				var ResultTop = $('#LoadMoreSms').scrollTop();
				if ((ResultTop - DivTop) == 0)
				{
					var Count = $('#SmsOffset').val();
					//LoadChat(Mobile,Unseen,Index,Count,10);
				}
			}	
		});
	}
	function SendSms()
	{
		if (CurChatMobile == "")
		{
			ShowError(true,"Error!","Please Select Contact First ...",undefined,"txtMessage");
			return(false);
		}
		if (IsEmpty(document.Form.txtMessage.value) == true) 
		{
			ShowError(true,"Error!","Please Enter Text Message",undefined,"txtMessage");
			return(false);
		}
		$.ajax({
			url: "<?php echo($PagePath);?>pages/ajaxs/sms-chat-send",
			dataType: "JSON",
			method: "POST",
			async: false,
			data: {
				"ClientMobID": $("#cboDevice").val(),
				"Mobile": CurChatMobile,
				"Message": $("#txtMessage").val()
			},
			success: function (response) 
			{	
				// alert(response.Message);
				if (response.DocType == "Done")
				{
					$("#txtMessage").val("");
					LoadChat();
				}
				else
				{
					alert("Failed To Send SMS");
				}
			}
		});
	}
</script>
</body>
</html>