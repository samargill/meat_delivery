<?php
	$PageID = array(array(5,1,0),array(5,2,0));
	$PagePath = "../../";
	$PageTitle= "Add User";
	$PageMenu = "User Management";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (!isset($_REQUEST['UserID']))
	{
		CheckRight("Add","Redirect");
		$PageTitle = "Add User Detail";
		$UserID = 0;
		$BtnSave = "Save User";
	}
	else
	{
		CheckRight("View","Redirect");
		$PageTitle = "Edit User Detail";
		$UserID = $_REQUEST['UserID'];
		$BtnSave = "Update User";
	}
	if (isset($_REQUEST["btnSave"]))
	{
		$ErrPos = 0;
		$_SESSION['PageVars'] = $_REQUEST;
		$Query = "SELECT userid FROM clientuser".
			" WHERE clientid = ".$_SESSION[SessionID."ClientID"]." AND email = '".$_REQUEST['txtEmail']."'";
		if ($UserID > 0)
		{
			$Query .= " AND userid <> ".$UserID;
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow))
		{
			$Url = "useradd?Err=103";
			if ($UserID > 0)
			{
				$Url .= "&UserID=".$UserID;
			}
			header("Location: ".$Url);
			exit;
		}
		$Add = false;
		if ($UserID == 0)
		{
			$Add = true;
			$UserID = GetMax("clientuser","userid");
			$Query = "INSERT INTO clientuser".
				" (userid, usertype, clientid, name, mobile, email, password, adddate, verifydate, status)".
				" VALUES (".$UserID.", ".$_REQUEST['cboClientType'].", ".$_SESSION[SessionID."ClientID"].",".
				" '".TrimText($_REQUEST['txtName'],1)."', '".$_REQUEST['txtMobile']."',".
				" '".$_REQUEST['txtEmail']."', '".password_hash($_REQUEST['txtPassword'],PASSWORD_DEFAULT)."', NOW(), NOW(), 1)";
		}
		else
		{
			$Query = "UPDATE clientuser SET ";
			if ($UserID != 1)
			{
			$Query .= "".
				" usertype = ".$_REQUEST['cboClientType'].",";
			}
			$Query .= "".
				"  name   = '".$_REQUEST['txtName']."'".
				", mobile = '".$_REQUEST['txtMobile']."'".
				", email  = '".$_REQUEST['txtEmail']."'";
			if (strlen($_REQUEST['txtPassword']) > 0)
			{
				$Query .= "".
				", password = '".password_hash($_REQUEST['txtPassword'],PASSWORD_DEFAULT)."'";
			}
			$Query .= "".
				", lastedit     = NOW()".
				"  WHERE userid = ".$UserID." AND clientid = ".$_SESSION[SessionID."ClientID"];
		}
		// echo("<br><br>".$Query); die;
		if (!mysqli_query($Conn,$Query))
		{
			if ($Add == true)
				$Err = 101;
			else
				$Err = 102;
			$ErrPos = 1;
			$_SESSION['MysqlErr'] = mysqli_error($Conn);
		}
		else
		{
			if ($Add == true)
				$Err = 1;
			else
				$Err = 2;
		}
		$Url = "useradd?Err=".$Err;
		if ($Add == false)
		{
			$Url .= "&UserID=".$UserID;
		}
		if ($ErrPos > 0)
		{
			$Url .= "&ErrPos=".$ErrPos;
		}
		else
		{
			unset($_SESSION['PageVars']);
		}
		header("Location: ".$Url);
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">
		function Verify()
		{
			<?php CheckRight("Edit","ShowError");?>
			if (IsEmpty(document.Form.txtName.value) == true)
			{
				ShowError(true,"Error!","Please Enter User's First Name !<br><br>",undefined,"txtName");
				return(false);
			}
			if (IsEmail(document.Form.txtEmail.value,false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid Email / User Name !",undefined,"txtEmail");
				return(false);
			}
			if (document.Form.txtMobile.value.substr(0,1) != "+")
			{
				ShowError(true,"Error!","Please Enter Valid 11 Digit Mobile # Starting With + Sign",undefined,"txtMobile");
				return(false);
			}
			if (IsText(document.Form.txtMobile.value,"+0123456789",false) == false)
			{
				ShowError(true,"Error!","Please Enter Valid 11 Digit Mobile # ...<ul><li>With Country Code +61</li><li>No Spaces</li></ul>",undefined,"txtMobile");
				return(false);
			}
			if (document.Form.txtMobile.value.length != 12)
			{
				ShowError(true,"Error!","Please Enter Valid 11 Digit Mobile # Without Spaces ...",undefined,"txtMobile");
				return(false);
			}
			if (document.Form.txtMobile.value.substr(1,2) != "61")
			{
				ShowError(true,"Error!","Please Enter Valid 11 Digit Mobile # ...<ul><li>With Country Code +61</li><li>No Spaces</li></ul>",undefined,"txtMobile");
				return(false);
			}
			if (document.Form.txtMobile.value.charAt(3) != "4")
			{
				ShowError(true,"Error!","Please Enter Valid 11 Digit Mobile # ...<ul><li>With Country Code +61</li><li>No Spaces</li></ul>",undefined,"txtMobile");
				return(false);
			}
			if (document.Form.txtPassword.value != "")
			{
				if (IsEmpty(document.Form.txtPassword.value) == true || document.Form.txtPassword.value.length < 8)
				{
					ShowError(true,"Error!","Please Enter Valid Password At Least 8 Character Long !",undefined,"txtPassword");
					return(false);
				}
				if (document.Form.txtPassword.value != document.Form.txtCPassword.value)
				{
					ShowError(true,"Error!","Password & Confirm Password Didn't Match !",undefined,"txtCPassword");
					return(false);
				}
			}
			return(true);
		}
	</script>
</head>
<body>
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper" style="margin-left: 0px;">
		<!-- Page Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1><?php echo($PageTitle);?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu);?></a></li>
							<li class="breadcrumb-item active"><?php echo($PageTitle);?></li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" action="useradd" method="post">
						<div class="card-body">
							<!-- Page Error -->
							<?php
								$cboClientType = 0;
								$txtName = $txtMobile = $txtEmail = "";
								if (isset($_REQUEST['Err']))
								{
									$Message = "";
									$MessageBG = "callout-danger lead";
									$MessageHead = "Error:";
									$BGColor = "#dc3545";
									$MessageIcon = "fa-exclamation-circle";
									switch ($_REQUEST['Err'])
									{
										case 1:
											$Message = "User Added Successfully ...";
											break;
										case 2:
											$Message = "User Updated Successfully ...";
											break;
										case 101:
										case 102:
											$Message = "Unable To ".($_REQUEST['Err'] == 101 ? "Add" : "Edit")." User - Fatal Error ...";
											if (isset($_SESSION["MysqlErr"]))
											{
												$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
												unset($_SESSION["MysqlErr"]);
											}
											break;
										case 103:
											$Message = "Same User Name Already Exist ...";
											break;
									}
									if ($_REQUEST['Err'] < 100)
									{
										$MessageHead = "Note:";
										$MessageBG = "callout-primary";
										$BGColor = "#62bfa8";
										$MessageIcon = "fa-info-circle";
									}
							?>
							<div class="pad margin no-print pb-3">
								<div class="callout <?php echo($MessageBG);?>" style="margin-bottom: 0!important; background-color: <?php echo($BGColor);?> !important;">
									<h4><i class="fa <?php echo($MessageIcon);?>"></i> <?php echo($MessageHead);?></h4>
									<span style="font-size:16px;"><?php echo($Message);?></span>
								</div>
							</div>
							<?php
								}
								if (isset($_SESSION['PageVars']))
								{
									$cboClientType = $_SESSION['PageVars']['cboClientType'];
									$txtName  	   = $_SESSION['PageVars']['txtName'];
									$txtEmail      = $_SESSION['PageVars']['txtEmail'];
									$txtPassword   = $_SESSION['PageVars']['txtPassword'];
									$txtMobile     = $_SESSION['PageVars']['txtMobile'];
									unset($_SESSION["PageVars"]);
								}
								else
								{
									$Query = "SELECT name, email, password, mobile, usertype".
										" FROM clientuser WHERE userid = ".$UserID." AND clientid = ".$_SESSION[SessionID."ClientID"];
									$rstData = mysqli_query($Conn,$Query);
									if (mysqli_num_rows($rstData) > 0)
									{
										$objRow = mysqli_fetch_object($rstData);
										$cboClientType   = $objRow->usertype;
										$txtName    	 = $objRow->name;
										$txtMobile       = $objRow->mobile;
										$txtEmail        = $objRow->email;
										$txtPassword     = $objRow->password;
									}
								}
							?>
							<div class="row">
								<div class="col-md-6">
									<?php
										$QueryType = " WHERE clienttypeid > 1 ";
										$cboClientTypeDis = "";
										if ($UserID == 1)
										{
											$QueryType = " WHERE clienttypeid IN (0,1) ";
											$cboClientTypeDis = "readonly";
										}
									?>
									<div class="form-group">
										<label>Type (*)</label>
										<?php
											DBCombo("cboClientType","clientusertype","clienttypeid","clienttypename",$QueryType."ORDER BY clienttypeid",$cboClientType,"","form-control select2","style=\"width: 100%;\" ".$cboClientTypeDis);
										?>
									</div>
									<div class="form-group">
										<label>First Name (*)</label>
										<input type="text" name="txtName" id="txtName" value="<?php echo($txtName);?>" maxlength="50" class="form-control">
									</div>
									<div class="form-group">
										<label>User Name [ Email ] (*)</label>
										<input type="text" name="txtEmail" id="txtEmail" value="<?php echo($txtEmail);?>" maxlength="100" class="form-control">
									</div>
									<div class="form-group">
										<label>Mobile #</label>
										<input type="tel" name="txtMobile" id="txtMobile" value="<?php echo($txtMobile);?>" maxlength="12" class="form-control">
									</div>
									<div class="form-group">
										<label>Password (*)</label>
										<input type="password" name="txtPassword" id="txtPassword" value="" maxlength="30" class="form-control">
									</div>
									<div class="form-group">
										<label>Re-Type Password (*)</label>
										<input type="password" name="txtCPassword" id="txtCPassword" value="" maxlength="30" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<?php
								if ($UserID > 0)
								{
							?>
							<input type="hidden" name="UserID" value="<?php echo $UserID; ?>">
							<?php
								}
							?>
							<button type="submit" name="btnSave" class="btn btn-primary" onclick="return Verify();">
								<?php echo($BtnSave);?>
							</button>
						</div>
					</form>
				</div>	
			</div><!-- /.box-primary -->
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function() {
		//Init Select2
		$(".select2").select2();
	});
</script>
<?php
	$cboArea_Status = "All";
	$cboArea_Multiple = "false";
?>
</body>
</html>