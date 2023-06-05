<?php
	$PagePath = "";
	$PageMenu = "Admin";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/functions.php");

	if (isset($_SESSION[SessionID]))
	{
		header("Location: ".$PagePath."main");
		exit;
	}
	if (isset($_REQUEST['btnLogin']))
	{
		$Query = "SELECT adminid, admintype, firstname, lastname, password".
			" FROM adminlogin".
			" WHERE status = 1 AND email = '".addslashes($_POST['txtUserName'])."'";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			header("Location: ./?Err=1");
			exit;
		}
		$objRow = mysqli_fetch_object($rstRow);
		if (password_verify($_POST['txtPassword'],$objRow->password) == false)
		{
			header("Location: ./?Err=1");
			exit;
		}
		$Query = "UPDATE adminlogin SET".
			"  lastlogin = NOW()".
			", lastactive = NOW()".
			"  WHERE adminid = ".$objRow->adminid;
		mysqli_query($Conn,$Query);
		$Query = "INSERT INTO adminloginlog".
			" (logdate, ipaddress, adminid)".
			" VALUES (NOW(), '".$_SERVER['REMOTE_ADDR']."', ".$objRow->adminid.")";
		mysqli_query($Conn,$Query);
		$_SESSION[SessionID] 			= $objRow->adminid;
		$_SESSION[SessionID."Name"] 	= ucwords($objRow->firstname);
		$_SESSION[SessionID."FullName"] = ucwords($objRow->firstname." ".$objRow->lastname);
		$_SESSION[SessionID."Type"] 	= $objRow->admintype;
		$_SESSION[SessionID."Time"] 	= time();
		header("Location: main");
		exit;
	}
	if (isset($_REQUEST['Forgot']) && isset($_POST['txtUserName']))
	{
		$Email = $_REQUEST['txtUserName'];
		$Query = "SELECT adminid, mobile FROM adminlogin WHERE email = '".addslashes($Email)."'";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			header("Location: index?Err=3");
			exit;
		}
		$objRow  = mysqli_fetch_object($rstRow);
		$AdminID = $objRow->adminid;
		$Mobile  = $objRow->mobile;
		$ResetLink = GetResetLink($AdminID,"Admin",$Email);
		if ($Mobile != "")
		{
			ForgotPasswordAdminMobile($Mobile,$ResetLink);
		}
		ForgotPasswordAdminEmail($Email,$ResetLink);
		header("Location: index?Err=101");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
</head>
<body class="hold-transition login-page">
	<?php
		$ResetPass = "";
		if (isset($_REQUEST['Err']))
		{
			if ($_REQUEST['Err'] > 100)
			{
				if ($_REQUEST['Err'] == 101)
				{
					$ResetPass 	 = "Done";
					$Message 	 = "We have received your password reset request and an email is sent. Please check your".
						" email and follow the instructions to reset your password.";
					$MessageHead = "Note: Password Reset Email Sent";
					$MessageBG 	 = "callout-info";
					$MessageIcon = "fa-info-circle";
				}
				elseif ($_REQUEST['Err'] == 102)
				{
					$ResetPass 	 = "RenewError";
					$Message 	 = "Unfortunately, you have not changed your password in the last 90 days.<br><br>Please contact your Admin Officer to change the password for you.";
					$MessageBG 	 = "callout-danger lead";
					$MessageHead = "Error: Renew Password";
					$MessageIcon = "fa-exclamation-circle";
				}
	?>
	<div class="pad margin no-print" style="margin: 200px;">
		<div class="callout <?php echo($MessageBG);?>" style="margin-bottom: 0!important;">
			<h3><i class="fa <?php echo($MessageIcon);?>"></i> <?php echo($MessageHead);?></h3>
			<span style="font-size:18px;"><?php echo($Message);?></span>
		</div>
	</div>
	<?php
			}
		}
	?>
	<?php
		if ($ResetPass == "")
		{
	?>
	<div class="login-box">
		<div class="login-logo">
			<img src="dist/img/logo-lrg.png" width="350">
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg mb-3">Sign in to start your session</p>
				<p class="login-box-msg mb-3" style="color: red;">
					<?php
						if (isset($_REQUEST['Err']))
						{
							if ($_REQUEST['Err'] == 1)
							{
								echo("Invalid User Name or Password");
							}
						}
					?>
				</p>
				<form name="Form" method="post" action="index">
					<div class="input-group mb-3">
						<input type="email" name="txtUserName" id="txtUserName" class="form-control" placeholder="Email">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" name="txtPassword" id="txtPassword" class="form-control" placeholder="Password">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<button type="submit" name="btnLogin" class="btn btn-primary btn-block" onclick="return Verify();">Sign In</button>
						</div>
					</div>
				</form>
				<p class="mb-1 mt-3">
					<a href="forgot-password.html">I forgot my password</a>
				</p>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<?php
		}
	?>
	<?php
		include($PagePath."includes/inc-js.php");
	?>
	<script type="text/javascript">
		function Verify ()
		{
			if (IsEmail(document.Form.txtUserName.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Your Email As User Name",undefined,"txtUserName");
				return(false);
			}
			if (document.Form.txtPassword.value == "")
			{
				ShowError(true,"Error!","Please Enter Your Password",undefined,"txtPassword");
				return(false);
			}
		}
		function VerifyForgot()
		{
			if (IsEmail(document.Form.txtUserName.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Your Registered Email & Then Click Forgot Password",undefined,"txtUserName");
			}
			else
			{
				document.Form.action = "index?Forgot";
				document.Form.submit();
			}
		}
	</script>
</body>
</html>