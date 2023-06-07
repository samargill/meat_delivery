<?php
	$PageID    = array(4,1,0);
	$PagePath  = "../../";
	$PageTitle = "Conversation";
	$PageMenu  = "Conversation";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<style type="text/css">
		html
		{
		  scrollbar-width: none;
		}
		.ChatBox-Sms::-webkit-scrollbar 
		{
			display: none;
		}
		.ChatBox-Sms::-moz-scrollbar-x
		{ 
			display: none;
		}
		.direct-chat
		{
			background-image: url("<?php echo($PagePath)?>dist/img/wtsapp.jpg");
		}
		#Send
		{
			position: fixed;
			bottom: 8px;
			width: 95%;	
		}
	</style>
</head>
<body>
<div class="container-fluid">
	<!-- Page Content -->
	<?php
		$Msg = "";
		if (!isset($_REQUEST['SmsID']))
		{
			$Msg = "Invalid Chat Detail [1]";
		}
		else
		{
			list($SmsType,$SmsID) = explode("-",$_REQUEST['SmsID']);
			if (!($SmsType == "S" || $SmsType == "R"))
			{
				$Msg = "Invalid Chat Detail [2]";
			}
			else
			{
				if ($SmsType == "R")
				{
					// $Query = "SELECT SmsR.clientmobid, SmsR.mobile".
					// 	" FROM smsreclist SmsR".
					// 	" INNER JOIN clientmobile CM ON SmsR.clientmobid = CM.clientmobid".
					// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsR.smsid = ".$SmsID;
					$Query = "SELECT SmsR.mobileid, SmsR.mobile".
						" FROM smsreclist SmsR".
						" INNER JOIN clienthavemob CHM ON SmsR.mobileid = CHM.mobileid".
						" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsR.smsid = ".$SmsID;
				}
				else
				{
					// $Query = "SELECT SmsQ.clientmobid, SmsL.mobile".
					// 	" FROM smsque SmsQ".
					// 	" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
					// 	" INNER JOIN clientmobile CM ON SmsQ.clientmobid = CM.clientmobid".
					// 	" WHERE CM.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsL.smsid = ".$SmsID;
					$Query = "SELECT SmsQ.mobileid, SmsL.mobile".
						" FROM smsque SmsQ".
						" INNER JOIN smsquelist SmsL ON SmsQ.smsqueid = SmsL.smsqueid".
						// " INNER JOIN clientmobile CM ON SmsQ.clientmobid = CM.clientmobid".
						" WHERE SmsQ.clientid = ".$_SESSION[SessionID."ClientID"]." AND SmsL.smsid = ".$SmsID;
				}
				$rstRow = mysqli_query($Conn,$Query);
				if (mysqli_num_rows($rstRow) == 0)
				{
					$Msg = "Invalid Chat Detail [2]";
				}
			}
		}
	?>
	<?php
		if ($Msg != "")
		{
	?>
	<div class="pad margin no-print">
		<div class="callout callout-danger" style="margin-bottom: 0!important;">
			<h4><i class="fa fa-info-circle"></i> Error</h4>
			<span style="font-size:16px;"><?php echo($Msg);?></span>
		</div>
	</div>
	<?php
		}
		else
		{
			$objRow = mysqli_fetch_object($rstRow);
			$ClientMobID = $objRow->mobileid;
			$SenderMobile = $objRow->mobile;
			$SenderName  = GetValue("fullname","clientcontact","mobile = ".$SenderMobile);
			// Mark As Seen
			$Query  = "UPDATE smsreclist SET seen = 1".
				" WHERE seen = 0 AND mobileid = ".$ClientMobID." AND mobile = ".$SenderMobile;
			mysqli_query($Conn,$Query);
	?>
	<div class="row">
		<div class="col-12 pl-0">
			<div class="box box-primary direct-chat direct-chat-primary">
				<div class="box-header with-border" style="background-color: #075e54; z-index: 2; position:fixed; top:0; width:100%;">
					<h1 class="box-title" style="font-size: 30px; color: white; word-spacing: 5px;">
						&nbsp;Chat with
						<?php 
							if ($SenderName == "")
								echo("(".$SenderMobile.")");
							else
								echo(ucwords($SenderName));
						?>
					</h1>
				</div>
				<div class="box-body" id="Chat_Coversation_Box" style="margin-top:70px; margin-left: 10px; margin-right: 10px;">
					<div class="ChatBox-Sms" id="ChatBox-Sms" style="height:auto;">
					</div>
				</div>
				<div class="box-footer">
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
	<?php
		}
	?>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<?php
	if ($Msg == "")
	{
?>
<script type="text/javascript">
	var LastResponse = '';
	LoadChat();
	setInterval(function() { LoadChat(); }, 5000);
	function ScrollToBottom()
	{
		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
	}
	function LoadChat()
	{
		// alert("Tiger");
		$.ajax({
			url: "<?php echo($PagePath);?>pages/ajaxs/sms-chat-reload",
			dataType: "HTML",
			method: "POST",
			timeout: 5000,
			data: {
				"ClientMobID": <?php echo($ClientMobID);?>,
				"Mobile": <?php echo($SenderMobile);?>
			}
			}).done(function (response) 
			{
				$("#ChatBox-Sms").html(response);
				if (LastResponse && response !== LastResponse) 
				{
					var audio = new Audio('notify-sound.mp3')
					audio.play();
				}
				LastResponse = response;
				ScrollToBottom();
			}).fail(function(jqXHR,exception) 
			{
				alert(jqXHR.responseText);
		});
	}
	function SendSms()
	{
		if (IsEmpty(document.Form.txtMessage.value) == true) 
		{
			ShowError(true,"Error!","Please Enter Text Message",undefined,"txtMessage");
			return(false);
		}
		//alert("ClientMobID :"+<?php //echo($ClientMobID);?>+"\nMobile:"+<?php //echo($SenderMobile);?>);
		$.ajax({
			url: "<?php echo($PagePath);?>pages/ajaxs/sms-chat-send",
			dataType: "JSON",
			method: "POST",
			timeout: 5000,
			data: {
				"ClientMobID": <?php echo($ClientMobID);?>,
				"Mobile": <?php echo($SenderMobile);?>,
				"Message": $("#txtMessage").val()
			}
			}).done(function (response) 
			{
				// alert(response.Message);
				if (response.Status == "Done")
				{
					// alert("test");
					$("#txtMessage").val("");
					LoadChat();
				}
			}).fail(function(jqXHR,exception) 
			{
				alert("Failed To Send SMS. Please Try Again ..."+jqXHR.responseText);
		});
	}
</script>
<?php
	}
?>
</body>
</html>