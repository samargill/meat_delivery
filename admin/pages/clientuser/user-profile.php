<?php
	$PageID = array(4,1,0);
	$PagePath = "../../";
	$PageMenu = "User Info";
	$PageTitle= "My Profile";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	$PageTitle = "My Profile";
	$BtnSave = "Save Contact";
	if (isset($_POST["btnSave"]))
	{
		$Query = "UPDATE clientuser SET".
			"  name   = '".TrimText($_REQUEST['txtUserName'],1)."'".
			", mobile = '".TrimText($_REQUEST['txtMobile'],1)."'".
			"  WHERE clientid = ".$_SESSION[SessionID."ClientID"];
		mysqli_query($Conn,$Query);
		header("Location: user-profile?updated");
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
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
						<h1 class="m-0 text-dark"><?php echo($PageTitle);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active"><?php echo($PageMenu);?></li>
							<li class="breadcrumb-item active"><?php echo($PageTitle);?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Page Error -->
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" action="" method="post" role="form" accept-charset="utf-8">
						<div class="card-body">
							<?php
								if (isset($_REQUEST['updated']))
								{
									$MessageHead = "Done:";
									$MessageBG = "callout-info";
									$MessageIcon = "fa-info-circle";
									$Message = "Your Profile information Updated Successfully";
							?>
							<div class="pad margin no-print">
								<div class="callout <?php echo($MessageBG);?>" style="margin-bottom: 0!important; background-color: #1cc443 !important;">
									<h4><i class="fa <?php echo($MessageIcon);?>"></i> <?php echo($MessageHead);?></h4>
									<span style="font-size:16px;"><?php echo($Message);?></span>
								</div>
							</div>
							<?php
								}
								else
								{
							?>
							<div class="row">
								<div class="col-md-6">
									<?php
										$Query  = "SELECT name, mobile, email".
										" FROM clientuser".
										" WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
										$rstRow = mysqli_query($Conn,$Query);
										$objRow = mysqli_fetch_object($rstRow);
									?>
									<div class="input-group mb-3">
										<div class="input-group-prepend color-danger">
											<span class="input-group-text bg-info"><i class="fas fa-user"></i></span>
										</div>
										<input type="text" name="txtUserName" id="txtUserName" value="<?php echo($objRow->name);?>" class="form-control" maxlength="70">
									</div>
									<div class="input-group mb-3">
										<div class="input-group-prepend color-danger">
											<span class="input-group-text bg-info"><i class="fas fa-phone"></i></span>
										</div>
										<input type="text" name="txtMobile" id="txtMobile" value="<?php echo($objRow->mobile);?>" class="form-control" maxlength="11">
									</div>
									<div class="input-group mb-3">
										<div class="input-group-prepend color-danger">
											<span class="input-group-text bg-info"><i class="fas fa-envelope"></i></span>
										</div>
										<input type="text" name="txtEmail" id="txtEmail" value="<?php echo($objRow->email);?>" class="form-control" readonly>
									</div>
								</div>
							</div>
							<?php
								}
							?>
						</div>
						<div class="card-footer">
							<button type="submit" name="btnSave" class="btn btn-info" onclick="return Verify();"><?php echo($BtnSave);?></button>
							<button type="button" name="btnPassword" data-toggle="modal" data-target="#Modal-Password" class="btn btn-info">Change Password</button>
							<button type="button" name="btnEmail" disabled data-toggle="modal" data-target="#Modal-Email" class="btn btn-info">Change Email</button>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<!-- Change Password Modal -->
<div id="Modal-Password" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content" id="pass-content">
			<form name="FrmPass" id="FrmPass" action="" method="post" autocomplete="off">
				<div class="modal-header">
					<h4 id="Modal-Education-Title" class="modal-title">Change password</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="PageReload();">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Old Password</label>
								<input type="password" id="txtPassword" name="txtPassword" class="form-control">
							</div>
							<div class="form-group">
								<label>New Password</label>
								<input type="password" id="txtNewPass" name="txtNewPass" class="form-control">
							</div>
							<div class="form-group">
								<label>Confirm Password</label>
								<input type="password" id="txtCNewPass"  name="txtCNewPass" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="BtnChangePass" class="btn btn-primary" onclick="return VerifyPass();">Save Password</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="PageReload();">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script type="text/javascript">
	function Verify()
	{
		if (IsEmpty(document.Form.txtUserName.value,false) == true) 
		{
			ShowError(true,"Error!","Please Enter Full Name",undefined,"txtUserName");
			return(false);
		}
		if (IsClientMobile(document.Form.txtMobile.value,false) == false) 
		{
			ShowError(true,"Error!","Please Enter Valid Mobile Number",undefined,"txtMobile");
			return(false);
		}
		if (IsEmail(document.Form.txtEmail.value,false) == false)
		{
			ShowError(true,"Error!","Please Enter Valid Email.",undefined,"txtEmail");
			return(false);  
		}
	}
	function VerifyPass() 
	{
		if(IsEmpty(document.FrmPass.txtPassword.value) == true)
		{
			ShowError(true,"Error!","Please Enter Valid Old Password",undefined,"txtPassword");
			return(false);  
		}
		if(IsPassword(document.FrmPass.txtNewPass.value,false) == false)
		{
			ShowError(true,"Error!","New Password Must have atleast"+
				"<br><br><ul><li>8 Characters </li><li>1 UpperCase Letter</li><li>1 LowerCase Letter</li>"+
				"<li>1 digit</li></ul>",undefined,"txtNewPass");
			return(false);  
		}
		if (document.FrmPass.txtNewPass.value != document.FrmPass.txtCNewPass.value)
		{
			ShowError(true,"Error!","Password & Confirm Password didn't Match.",undefined,"txtCNewPass");
			return(false);
		}
		$.ajax({
			url: 'get-password.php',
			method: 'post',
			data: {
				"txtOldPass": document.FrmPass.txtPassword.value,
				"txtNewPass": document.FrmPass.txtNewPass.value,
				"BtnPressed": "BtnChangePass"
			},
			success: function(response) {
				var Title,Message,Icon; 
				if (response == "Done")
				{
					Title   = "Done";
					Message = "Password Changed Successfully ...";
					Icon    = "check-circle";
				}
				else if (response == "WrongProfile")
				{
					Title   = "Invalid:";
					Message = "Invalid Profile Details !";
					Icon    = "times-circle";
				}
				else if (response == "OldWrong")
				{
					Title   = "Invalid:";
					Message = "Invalid Current Password !";
					Icon    = "times-circle";
				}
				$.confirm({
					title: Title,
					content: Message,
					icon: "fa fa-"+Icon,
					animation: "scale",
					closeAnimation: "scale",
					opacity: 0.5,
					buttons: {
						"confirm": {
							text: "Ok",
							action: function() {
								if (response == "Done")
								{
									PageReload();
								}
							}
						}
					}
				});
			}
		});
	}
	function PageReload()
	{
		$("#Modal-Password").modal("hide");
		$('#Modal-Password').on('hidden.bs.modal', function () {
			$(this).find("input,textarea,select").val('').end();
		});
		$("#Modal-Email").modal("hide");
		$('#Modal-Email').on('hidden.bs.modal', function () {
			$(this).find("input,textarea,select").val('').end();
		});
	}
	function VerifyEmail()
	{
		if (IsEmail(document.FrmEmail.txtEmail.value,false) == false)
		{
			ShowError(true,"Error!","Please Enter Valid Email.",undefined,"txtEmail");
			return(false);  
		}
		$.ajax({
			url: 'get-email.php',
			method: 'post',
			data: {
				"txtEmail": document.FrmEmail.txtEmail.value,
				"BtnPressedEmail": "BtnChangeEmail"
			},
			success: function(response) {
				var Title,Message,Icon; 
				if (response == "Done")
				{
					Title   = "Done";
					Message = "Your are just one step away from activating your New Email Address"+"<br>"+"We have just sent verification code on your email to verify your email address"+"<br>"+"Please Copy and paste code here for completing verification process";
					Icon    = "check-circle";
				}
				else if (response == "Email-Exist")
				{
					Title   = "Email-Exist:";
					Message = "Email Already Have Account !";
					Icon    = "times-circle";
				}
				$.confirm({
					title: Title,
					content: Message,
					icon: "fa fa-"+Icon,
					animation: "scale",
					closeAnimation: "scale",
					opacity: 0.5,
					buttons: {
						"confirm": {
							text: "Ok",
							action: function() {
								if (response == "Done")
								{
									$('#FrmEmail').css('display','none');
									$('#FrmCode').css('visibility','visible');
								}
							}
						}
					}
				});
			}
		});
	}
	function VerifyCode()
	{
		if (IsEmpty(document.FrmCode.txtCode.value,false) == true) 
		{
			ShowError(true,"Error!","Please Enter valid verification code",undefined,"txtCode");
			return(false);
		}
		$.ajax({
			url: 'get-email.php',
			method: 'post',
			data: {
				"txtCode": document.FrmCode.txtCode.value,
				"BtnPressedCode": "BtnCode"
			},
			success: function(response) {
				var Title,Message,Icon; 
				if (response == "Done")
				{
					Title   = "Done";
					Message = "Your email is verified successfully & now you can login to your account with new Email and use our services";
					Icon    = "check-circle";
				}
				else if (response == "ErrorCode")
				{
					Title   = "Invalid:";
					Message = "Verification code is not valid !";
					Icon    = "times-circle";
				}
				$.confirm({
					title: Title,
					content: Message,
					icon: "fa fa-"+Icon,
					animation: "scale",
					closeAnimation: "scale",
					opacity: 0.5,
					buttons: {
						"confirm": {
							text: "Ok",
							action: function() {
								if (response == "Done")
								{
									window.location = "user-profile.php";
								}
							}
						}
					}
				});
			}
		});
	}
</script>
</body>
</html>