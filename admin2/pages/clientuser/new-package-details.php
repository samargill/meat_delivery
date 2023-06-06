<?php
	$PageID = array(6,0,0);
	$PagePath  = "../../";
	$PageMenu  = "Upgrade Package";
	$PageTitle = "Upgrade Packages Detail";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	include($PagePath."../lib/payment.php");
	include($PagePath."../lib/email.php");
	include($PagePath."../lib/PHPMailer-6.1.5/PHPMailer.php");
	include($PagePath."../lib/PHPMailer-6.1.5/SMTP.php");
	include($PagePath."../lib/PHPMailer-6.1.5/Exception.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	function GetSmsSendRate($TopUpAmount,$PkgID)
	{
		if ($PkgID == 6)
		{
			if ($TopUpAmount > 0 && $TopUpAmount <= 100)
			{
				$SmsSendRate = 0.100;
			}
			elseif ($TopUpAmount > 100 && $TopUpAmount <= 300)
			{
				$SmsSendRate = 0.080;
			}
			elseif ($TopUpAmount > 300 && $TopUpAmount <= 500)
			{
				$SmsSendRate = 0.070;
			}
			elseif ($TopUpAmount > 500 && $TopUpAmount <= 1000)
			{
				$SmsSendRate = 0.060;
			}
			elseif ($TopUpAmount > 1000 && $TopUpAmount <= 5000)
			{
				$SmsSendRate = 0.050;
			}
			elseif ($TopUpAmount > 5000)
			{
				$SmsSendRate = 0.040;
			}
		}
		elseif ($PkgID == 7)
		{
			$SmsSendRate = 0.060;
		}
		elseif ($PkgID == 8)
		{
			$SmsSendRate = 0.075;
		}
		return($SmsSendRate);
	}	
	if (isset($_REQUEST['CboCredit']))
		$CboCredit = $_REQUEST['CboCredit'];
	else
		$CboCredit = "";
	if (isset($_REQUEST['cboCountry']))
		$cboCountry = $_REQUEST['cboCountry'];
	else
		$cboCountry = "";
	$PkgID = 0;
	if (isset($_REQUEST['Tab']))
	{
		$Tab = $_REQUEST['Tab'];
		if ($Tab == "Shared")
		{
			$PkgID	 = 6;
			$PkgName = "Shared Sms Package Details";
			$SimCharges = sprintf("%0.2f", 0.00);
 		}
		elseif ($Tab == "Dedicated")
		{
			$PkgID	 = 7;
			$PkgName = "Dedicated Sms Package Details";
			// $SimCharges = GetValue("pkg_field_value","package_rate","pkg_field_id = 9");
			$Query  = " SELECT COD.simexpiry".
				" FROM clientorder CO".
				" INNER JOIN clientorderdetail COD ON CO.orderid = COD.orderid".
				" WHERE CO.clientid = ".$_SESSION[SessionID."ClientID"]." AND COD.simexpiry IS NOT NULL". 
				" ORDER BY COD.orderid DESC".
				" LIMIT 1";
			$rstExp = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstExp) > 0)
			{
				$objExp = mysqli_fetch_object($rstExp);
				$OldSimExp = $objExp->simexpiry;
				$Today 	   = mktime(0,0,0,date("m"),date("d"),date("Y"));
				$OldSimExp = strtotime($OldSimExp);
				if ($OldSimExp > $Today)
				{
					$SimCharges = sprintf("%0.2f", 0.00);
				}
				else
				{
					$SimCharges = GetValue("pkg_field_value","package_rate","pkg_field_id = 9");
				}	
			}
			else
			{
				$SimCharges = GetValue("pkg_field_value","package_rate","pkg_field_id = 9");
			}
		} 
		elseif ($Tab == "Pay-you-go")
		{
			$PkgID	 = 8;
			$PkgName = " Pay As You Go Package Details";
			$SimCharges = sprintf("%0.2f", 0.00);
		}
	}
	// else
	// {
	// 	$PkgName="Shared";		
	// 	$PkgID	= 6;
	// 	$SimCharges = sprintf("%0.2f", 0.00);
	// }
	if (isset($_POST['BtnPayment']))
	{
		$CheckOldPkg = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
		$TopUpAmount= sprintf("%0.2f",$_REQUEST['txtTopUp']);
		$SimRate	= sprintf("%0.2f",$_REQUEST['txtSimPrice']);
		$TtlAmount 	= sprintf("%0.2f",$_REQUEST['txtAmount']);
		$Query = "SELECT pkgid, pkgname".
			" FROM package".
			" WHERE pkgid =".$_REQUEST['PkgID'];
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			header("Location: new-package-details?Err=101");
			exit;
		}
		$objRow = mysqli_fetch_object($rstRow);
		// Clear Old Cart
		$Query = "DELETE FROM clientcart WHERE clientid = ".$_SESSION[SessionID."ClientID"];
		mysqli_query($Conn,$Query);
		// New Cart
		$Query = "INSERT INTO clientcart".
			" (cartdate, clientid, amount)".
			" VALUES (NOW(), ".$_SESSION[SessionID."ClientID"].", ".$TtlAmount.")";
		if (!mysqli_query($Conn,$Query))
		{
			header("Location: new-package-details?Err=102");
			exit;
		}
		$CartID = mysqli_insert_id($Conn);
		if ($CartID > 0)
		{
			if ($objRow->pkgid == 6 || $objRow->pkgid == 7 || $objRow->pkgid == 8)
			{
				$SmsSendRate = GetSmsSendRate($_REQUEST['txtTopUp'],$objRow->pkgid);
				$SmsSendLimit= ceil($_REQUEST['txtTopUp'] / $SmsSendRate);
			}
			$SmsRecvRate = GetValue("pkg_field_value","package_rate","pkgid = 7 AND pkg_field_id = 12");
			$Query = "INSERT INTO clientcartdetail".
				" (cartid, pkgid, topup, simrate, smssendrate, smsrecvrate, balance, smssendlimit)".
				" VALUES (".$CartID.", ".$objRow->pkgid.", ".$TopUpAmount.", ".$SimRate.", ".$SmsSendRate.", ".$SmsRecvRate.", ".$TopUpAmount.", ".$SmsSendLimit.")";
			mysqli_query($Conn,$Query);
		}
		if ($_POST['cboPayType'] == 0)
		{
			// Process Payment - Convert AUD To USD
			$PkgPrice = $TtlAmount * 1.34;
			$Response = SavePayment($CartID,$CartID,$_POST['txtCardNo'],
				$_POST['txtCardExpiry'],$_POST['txtCardCVC'],sprintf("%0.2f",$PkgPrice));
			$Json = json_decode($Response);
			if (isset($Json->APIError))
			{
				header("Location: new-package-details?Err=104");
				exit;
			}
			if ($Json->PayStatus == 1)
			{
				// Checkout
				$Data = array(
					"CartID" => $CartID,
					"PayRefNo" => $CartID,
					"PayTransNo" => $Json->PayTransID,
					"PayAuthID" => $Json->PayAuthID,
					"PayAmount" => sprintf("%0.2f",$Json->PayAmount),
					"PayCharges" => sprintf("%0.2f",0.00)
				);
				$Result = CheckoutCart($Data,1);
				if ($Result == "Done")
				{
					if (isset($_SESSION[SessionID]))
					{
						// In Case Of New Package Add Mobile device by befault
						$UserPkg = $objRow->pkgid;
						if ($UserPkg == 7)
						{
							if ($CheckOldPkg != 7)
							{
								// Get Mobile ID based on client ID for Removing previous reserve slot which is used in demo package 
								$DemoPkgMobID = GetValue("mobileid","clienthavemob","clientid= ".$_SESSION[SessionID."ClientID"]."");
								$Query = "UPDATE clientmobile CM SET".
									" usedslot = usedslot - 1".
									" WHERE mobileid =".$DemoPkgMobID;
								mysqli_query($Conn,$Query);
							}
							// Get Free Slot for Dedicated Sim
							$QuerySel = "SELECT CM.mobileid".
								" FROM clientmobile CM".
								" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
								" WHERE CHM.clientid = 1".
								" AND CM.maxslot = 1 AND CM.usedslot < CM.maxslot";
							$rstRowSel = mysqli_query($Conn,$QuerySel);
							if (mysqli_num_rows($rstRowSel) > 0)
							{
								$objRowSel = mysqli_fetch_object($rstRowSel);
								$ClientMobID = $objRowSel->mobileid;	
								$Query = "UPDATE clienthavemob SET".
									" mobileid = ".$ClientMobID."".
									" WHERE clientid= ".$_SESSION[SessionID."ClientID"]."";
								mysqli_query($Conn,$Query);
								$Query = "UPDATE smsque SET".
									" mobileid = ".$ClientMobID."".
									" WHERE clientid= ".$_SESSION[SessionID."ClientID"]."";
								mysqli_query($Conn,$Query);
								$Query = "UPDATE clientmobile SET".
									" usedslot = usedslot + 1".
									" WHERE mobileid =".$ClientMobID;
								mysqli_query($Conn,$Query);
							}
							else
							{
								if ($CheckOldPkg == 7)
								{
									header("Location: new-package-details?Err=3");
									exit;
								}
								else
								{
									$ClientID = $_SESSION[SessionID."ClientID"];
									$EmailAdmin = GetSimAlertAdminEmail($ClientID);
									if (DBUserName != "myitvh")
									{
										$PHPMailer = new PHPMailer();
										$PHPMailer->SendEmail(constant("FromEmail"),constant("ToEmail"),"Dedicated Package New Sim Request Alert",$EmailAdmin);
									}
									header("Location: new-package-details?Err=2");
									exit;
								}
							}
						}
						header("Location: new-package-details?Err=1");
						exit;
					}
				}
			}
			header("Location: new-package-details?Err=104");
			exit;
		}
		else
		{
			header("Location: ".$PagePath."../process-paypal?CartID=".$CartID.
				"&ProductID=".$objRow->pkgid."&ProductName=".$objRow->pkgname."&Amount=".$TtlAmount);
			exit;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">

		function LoadTab(TabID)
		{
			window.location = "new-package-details?Tab="+TabID;
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini" onload="ShowCountry(<?php echo($PkgID);?>);">
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
				<?php
					if (isset($_REQUEST['Err']))
					{
						$Message = "";
						$MessageBG = "danger";
						$MessageHead = "Error:";
						$MessageIcon = "fa-exclamation-triangle";
						switch ($_REQUEST['Err'])
						{
							case 1:
								$Message = "<h4 class='text-navy pt-3 pb-3'>Your Package Upgraded Successfully ...</h4>
									<img src='../../../images/logo.png' width='15%'><br>
									<h6 class='text-navy pb-1'>Thanks for choosing Bullkysms<h6>";
								break;
							case 2:
								$Message = GetValue("config_value","websettings","config_id = 306");
								break;
							case 3:
								$Message = "<h4 class='text-navy pt-3 pb-3'>Your balance updated Successfully ...</h4>
									<img src='../../../images/logo.png' width='15%'><br>
									<h6 class='text-navy pb-1'>Thanks for choosing Bullkysms<h6>";
								break;		
							case 101:
								$Message = "Unable To Perform Operation - Fatal Error ...";
								if (isset($_SESSION["MysqlErr"]))
								{
									$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
									unset($_SESSION["MysqlErr"]);
								}
								break;
							case 102:
								$Message = "Invalid Package Detail To Process. Please Try Again ...";
								break;
							case 103:
								$Message = "Unable To Process Payment. Please Try Again or Contact Our Support ...";
								break;
							case 104:
								$Message = "Unable To Process Payment via Card. Please Try Again or Contact Our Support ...";
								break;
						}
						if ($_REQUEST['Err'] < 100)
						{
							$MessageHead = "Note :";
							$MessageBG	 = "success";
							$MessageIcon = "fa-check";
						}
				?>
				<div style="">
					<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
						<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
						<span style="font-size:16px;"><?php echo($Message);?></span>
					</div>
				</div>
				<?php
					}
					else
					{
				?>
				<div class="card card-primary card-outline card-tabs">
					<div class="card-header p-0 pt-1 border-bottom-0">
						<ul class="nav nav-tabs" role="tablist">
							<?php
								$TabList = array(
									array("Name" => "Shared",   "Text" => "Shared Sms Credits", "Color" => "#dc3545"),
									array("Name" => "Dedicated", "Text" => "Dedicated Sms Credits", "Color" => "orange"),
									array("Name" => "Pay-you-go", "Text" => "Pay As You Go", "Color" => "lightcoral")
								);
								for ($i = 0; $i < count($TabList); $i++)
								{
									if ($Tab == $TabList[$i]["Name"])
									{
										$TabBgCls = "bg-navy";
									}
									else
									{
										if ($TabList[$i]["Name"] == "Shared")
											$TabBgCls = "bg-danger";
										if ($TabList[$i]["Name"] == "Dedicated")
											$TabBgCls = "bg-warning";
										if ($TabList[$i]["Name"] == "Pay-you-go")
											$TabBgCls = "bg-success";
									}
							?>
							<li class="nav-item pl-2">
								<a href="#<?php echo($TabList[$i]["Name"]);?>" class="nav-link <?php if ($Tab == $TabList[$i]["Name"]) echo("active");?> <?php echo($TabBgCls); ?>" style="font-weight: bold; border:0px solid transparent; color:#fff !important; font-size: 1.3rem;" onclick="LoadTab('<?php echo($TabList[$i]["Name"]);?>');">
									<?php echo($TabList[$i]["Text"]);?>
								</a>
							</li>
							<?php
								}
							?>
						</ul>
						<div class="card-body">
							<div class="tab-content">
								<?php
									if ($Tab == "Shared")
									{
								?>		
								<div id="<?php echo($Tab);?>" class="tab-pane active">
									<section>
										<form name="FrmShared" role="form" action="" method="post">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<h3 style="color:#dc3545;"><?php echo($PkgName);?></h3><br>
														<?php
															$UserCurPkg = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
															if ($UserCurPkg == $PkgID)
															{
																$Query  = "SELECT SUM(COD.balance) AS RemainingBal".
																	" FROM clientorderdetail COD".
																	" INNER JOIN clientorder CO ON COD.orderid = CO.orderid".
																	" WHERE COD.pkgexpiry > NOW() AND CO.clientid =".$_SESSION[SessionID."ClientID"];
																$rstRow = mysqli_query($Conn,$Query);
																if (mysqli_num_rows($rstRow) > 0)
																{
																	$objRow = mysqli_fetch_object($rstRow);
																	$RemainingBal = sprintf("%0.2f",$objRow->RemainingBal);
																}	 
														?>	
														<div class="form-group">
															<button type="button" class="btn btn-warning btn-md">
																<b>Your Current Balance :&nbsp;</b>
																<span class="fa fa-dollar-sign p-1" style="border-radius: 4px;color: #fff; background-color: #7b1616 !important;">
																	<?php echo($RemainingBal)?>
																</span>
															</button>
															<button type="button" class="btn btn-info btn-md" onclick="TakePayment(<?php echo($PkgID);?>,<?php echo($SimCharges)?>);"><i class="fa fa-wallet"></i> Topup Now</button>
														</div>
														<?php
															}
														?>
													</div>
												</div>	
												<div class="row">
													<div class="col-md-4 col-sm-12">
														<div class="form-group">
															<label>Country (*)</label>
															<?php
																DBCombo("cboCountry","address_country","countryid","countryname","WHERE countryid = 14",$cboCountry,$cboCountry,"form-control select2","onchange=\"SubmitForm();\" style=\"width: 100%;\"");
															?>
														</div>
													</div>
												</div>		
												<input type="hidden" name="txtTabName" id="txtTabName" value="<?php echo($Tab);?>">
												<div class="row" id="PkgTable" style="display: none;">
												</div>
											</div>
										</form>
									</section>
								</div>
								<?php
									}
									else if ($Tab == "Dedicated")
									{
								?>
								<div id="<?php echo($Tab);?>" class="tab-pane active">
									<section>
										<form name="FrmDedicated" role="form" action="" method="post">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<h3 style="color:orange;"><?php echo($PkgName);?></h3><br>
														<?php
															$UserCurPkg = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
															if ($UserCurPkg == $PkgID)
															{
																$Query  = "SELECT SUM(COD.balance) AS RemainingBal".
																	" FROM clientorderdetail COD".
																	" INNER JOIN clientorder CO ON COD.orderid = CO.orderid".
																	" WHERE COD.pkgexpiry > NOW() AND CO.clientid =".$_SESSION[SessionID."ClientID"];
																$rstRow = mysqli_query($Conn,$Query);
																if (mysqli_num_rows($rstRow) > 0)
																{
																	$objRow = mysqli_fetch_object($rstRow);
																	$RemainingBal = sprintf("%0.2f",$objRow->RemainingBal);
																}	 
														?>	
														<div class="form-group">
															<button type="button" class="btn btn-warning btn-md">
																<b>Your Current Balance :&nbsp;</b>
																<span class="p-1" style="border-radius: 4px;color: #fff; background-color: #7b1616 !important;">
																	<?php echo($RemainingBal)?>
																</span>
															</button>
															<button type="button" class="btn btn-info btn-md" onclick="TakePayment(<?php echo($PkgID);?>,<?php echo($SimCharges)?>);"><i class="fa fa-wallet"></i> Topup Now</button>
														</div>
														<?php
															}
														?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4 col-sm-12">
														<div class="form-group">
															<label>Country (*)</label>
															<?php
																DBCombo("cboCountry","address_country","countryid","countryname","WHERE countryid = 14",$cboCountry,$cboCountry,"form-control select2","onchange=\"SubmitForm();\" style=\"width: 100%;\"");
															?>
														</div>
													</div>
												</div>
												<input type="hidden" name="txtTabName" id="txtTabName" value="<?php echo($Tab);?>">
												<div class="row" id="PkgTable" style="display: none;">	
												</div>
											</div>
										</form>
									</section>
								</div>
								<?php
									}
									elseif ($Tab == "Pay-you-go")
									{
								?>
								<div id="<?php echo($Tab);?>" class="tab-pane active">
									<section>
										<form name="FrmPayYouGo" role="form" action="" method="post">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6 col-sm-12">
														<h3 style="color:lightcoral;"><?php echo($PkgName);?></h3><br>
														<?php
															$UserCurPkg = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
															if ($UserCurPkg == $PkgID)
															{
																$Query  = "SELECT SUM(COD.balance) AS RemainingBal".
																	" FROM clientorderdetail COD".
																	" INNER JOIN clientorder CO ON COD.orderid = CO.orderid".
																	" WHERE COD.pkgexpiry > NOW() AND CO.clientid =".$_SESSION[SessionID."ClientID"];
																$rstRow = mysqli_query($Conn,$Query);
																if (mysqli_num_rows($rstRow) > 0)
																{
																	$objRow = mysqli_fetch_object($rstRow);
																	$RemainingBal = sprintf("%0.2f",$objRow->RemainingBal);
																}	 
														?>	
														<div class="form-group">
															<button type="button" class="btn btn-warning btn-md">
																<b>Your Current Balance :&nbsp;</b>
																<span class="p-1" style="border-radius: 4px;color: #fff; background-color: #7b1616 !important;">
																	<?php echo($RemainingBal)?>
																</span>
															</button>
															<button type="button" class="btn btn-info btn-md" onclick="TakePayment(<?php echo($PkgID);?>,<?php echo($SimCharges)?>);"><i class="fa fa-wallet"></i> Topup Now</button>
														</div>
														<?php
															}
														?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4 col-sm-12">
														<div class="form-group">
															<label>Country (*)</label>
															<?php
																DBCombo("cboCountry","address_country","countryid","countryname","WHERE countryid = 14",$cboCountry,$cboCountry,"form-control select2","onchange=\"SubmitForm();\" style=\"width: 100%;\"");
															?>
														</div>
													</div>
												</div>
												<input type="hidden" name="txtTabName" id="txtTabName" value="<?php echo($Tab);?>">
												<div class="row" id="PkgTable" style="display: none;">
												</div>
											</div>
										</form>
									</section>
								</div>
								<?php
									}
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
					}
				?>		
			</div>	
		</section><!-- /.content -->
	</div>
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<!-- Payment Modal -->
<div id="Modal-Payment" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" id="pass-content">
			<form name="FrmPay" id="FrmPay" action="new-package-details" method="post" autocomplete="off">
				<div class="modal-header" style="background-color: #094751;">
					<h4 id="Modal-Payment-Title" class="modal-title text-white">Top Up Details</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true" class="text-white">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Buy Top up In Dollars(*)</label>
								<!-- <?php
									//DBCombo("CboCredit","package_rate","pkg_field_value","pkg_field_value","WHERE pkgid =".$PkgID,$CboCredit,"Buy Credit In Dollars","form-control select2","onchange=\"CreditAmount();\" style=\"width: 100%;\"");
								?> -->
								<input type="text" name="txtTopUp" id="txtTopUp" value="" class="form-control" onblur="CreditAmount();">
							</div>
							<?php
								if ($PkgID == 7)
								{
							?>
							<div class="form-group">
								<label>Sim Charges (+)</label>
								<input type="text" name="txtSimCharges" id="txtSimCharges" value=""  readonly class="form-control">
							</div>
							<?php
								}
							?>
							<input type="hidden" name="txtSimPrice" id="txtSimPrice" value="0">
							<div class="form-group">
								<label>Total Amount</label>
								<input type="text" name="txtAmount" id="txtAmount" value="" readonly class="form-control">
								<input type="hidden" name="PkgID" id="PkgID" value="">
							</div>
							<div class="form-group">
								<label>Payment By</label>
								<?php
									$ComboData = array();
									$ComboData[] = "Pay By Card";
									$ComboData[] = "Pay By Paypal";
									DBComboArray("cboPayType",$ComboData,0,0,"form-control select2","onchange=\"ChangePayType();\" style=\"width:100%\"");
								?>
							</div>
							<div id="PayByCard" class="row">
								<div class="col-md-6">
									<div id="RowCardNo" class="form-group">
										<label>Credit / Debit Card # :</label>
										<input type="text" name="txtCardNo" id="txtCardNo" value="" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div id="RowCardExpiry" class="form-group">
										<label>Card Expiry [ MM / YY ] :</label>
										<input type="text" name="txtCardExpiry" id="txtCardExpiry" value="" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div id="RowCardCVC" class="form-group">
										<label>Card CVC :</label>
										<input type="text" name="txtCardCVC" id="txtCardCVC" value="" class="form-control">
									</div>
								</div>
							</div>
							<div id="PayByPaypal" class="row">
								<div class="col-md-5">
									<div class="form-group">
										<img src="<?php echo($PagePath);?>../images/paypal.png">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="BtnPayment" id="BtnPayment" class="btn btn-primary" onclick="return VerifyPayment();">Process Payment</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script src="<?php echo($PagePath);?>plugins/input-mask/inputmask.js"></script>
<script src="<?php echo($PagePath);?>plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo($PagePath);?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo($PagePath);?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?php echo($PagePath);?>plugins/input-mask/jquery.inputmask.regex.extensions.js"></script>
<script language="javascript">
	// "use strict";
	$(function() {
		//Init Select2
		$(".select2").select2();
		// Payment Input Masks
		if ($("#txtTopUp").length > 0)
		{
			$("#txtTopUp").inputmask('Regex', { regex: "^[0-9]{1}[0-9]*\.[0-9]{3}$"});
		}
		if ($("#txtCardNo").length > 0)
		{
			$("#txtCardNo").inputmask("9999 9999 9999 9999");
		}
		if ($("#txtCardExpiry").length > 0)
		{
			$("#txtCardExpiry").inputmask("99 / 99");
		}
		if ($("#txtCardCVC").length > 0)
		{
			$("#txtCardCVC").inputmask("999");
		}
		$("#PayByPaypal").hide();
	});
	$("#txtTopUp").blur(function(){
		var txtTopUpVal = document.FrmPay.txtTopUp.value;
		if (txtTopUpVal.length == 1 && txtTopUpVal < 5)
		{
				ShowError(true,"Error!","Mininum Top up Amount is 5$","txtTopUp","txtTopUp");
				return(false);
		}
		if (txtTopUpVal.charAt(1) == ".")
		{
			if (txtTopUpVal.charAt(0) < 5)
			{
				ShowError(true,"Error!","Mininum Top up Amount is 5$","txtTopUp","txtTopUp");
				return(false);			
			}
		}
		var ChkLastIndex = txtTopUpVal.charAt(txtTopU
			.pVal.length - 1);
		if (ChkLastIndex == ".")
		{
			txtTopUpVal	= $('#txtTopUp').val().slice(0, -1);
			document.FrmPay.txtTopUp.value = txtTopUpVal;
		}
	});
	
	function ShowCountry(PkgID)
	{
		$("#PkgTable").css('display','none');
		var CountryID = 0;
		var FrmName	= "";
		var TabName = "";
		if (PkgID == 6)
		{
			CountryID = document.FrmShared.cboCountry.value;
			TabName = document.FrmShared.txtTabName.value;
			FrmName	  = "FrmShared";
		}
		else if (PkgID == 7)
		{
			CountryID = document.FrmDedicated.cboCountry.value;
			TabName = document.FrmDedicated.txtTabName.value;
			FrmName	  = "FrmDedicated"; 
		}
		else if (PkgID == 8)
		{
			CountryID = document.FrmPayYouGo.cboCountry.value;
			TabName = document.FrmPayYouGo.txtTabName.value;
			FrmName	  = "FrmPayYouGo"; 
		}
		if (CountryID > 0)
		{
			var FrmData = new FormData(document.FrmName);
			FrmData.append("CountryID", CountryID);
			FrmData.append("PkgID", PkgID);
			FrmData.append("TabName", TabName);
			var Result = "";
			$.ajax({
				url: "../ajaxs/get-package-detail",
				type: "POST",
				data: FrmData,
				dataType: "HTML",
				async: false,
				cache: false,
				contentType: false,
				processData: false
			}).done(function (response) {
				$("#PkgTable").html(response).show();
				$("#PkgTable").css('display','block');
			}).fail(function(jqXHR,exception) {
				alert("Error Completing Operation. Please Try Again ..."+jqXHR.responseText);
				return(false);
			});
		}
	}
	function ChangePayType()
	{
		if ($("#cboPayType").val() == 0)
		{
			$("#PayByCard").show();
			$("#PayByPaypal").hide();
			$("#BtnPayment").html("Process Payment");
		}
		else
		{
			$("#PayByCard").hide();
			$("#PayByPaypal").show();
			$("#BtnPayment").html("Proceed With Paypal");
		}
	}
	function CreditAmount()
	{
		var TopUpAmount = SimCharges = TtlAmount = 0;
		TopUpAmount = document.FrmPay.txtTopUp.value;
		if (TopUpAmount > 0)
		{
			SimCharges = document.FrmPay.txtSimPrice.value;
			$("#txtSimCharges").val(SimCharges);
			TtlAmount  = parseFloat(SimCharges) + parseFloat(TopUpAmount);
			$("#txtAmount").val(TtlAmount);
		}
	}
	function TakePayment(PkgID,SimCharges)
	{
		SimCharges = parseFloat(SimCharges).toFixed(2);
		$("#PkgID").val(PkgID);
		$("#txtSimPrice").val(SimCharges);
		$("#Modal-Payment").modal();
	}
	function VerifyPayment()
	{

		if (IsNumber(document.FrmPay.txtTopUp.value,false,true,1) == false)
		{
			ShowError(true,"Error!","Please Enter Your Top up Amount In Dollar","txtTopUp","txtTopUp");
			return(false);
		}
		if (document.FrmPay.cboPayType.value == 0)
		{
			var txtCardNo = ReplaceChar(ReplaceChar(document.FrmPay.txtCardNo.value," ",""),"_","");
			var txtCardNoMsg = "Please Enter Valid 13/14/15/16 Digit Card Number.";
			if (txtCardNo.length < 13)
			{
				ShowError(true,"Error!",txtCardNoMsg,"RowCardNo","txtCardNo");
				return(false);
			}
			if (parseInt(txtCardNo) == 0)
			{
				ShowError(true,"Error!",txtCardNoMsg,"RowCardNo","txtCardNo");
				return(false);
			}
			if (txtCardNo.substring(0,1) == "0")
			{
				ShowError(true,"Error!",txtCardNoMsg,"RowCardNo","txtCardNo");
				return(false);
			}
			var txtCardExpiry = ReplaceChar(document.FrmPay.txtCardExpiry.value," ","");
			if (txtCardExpiry.length != 5)
			{
				ShowError(true,"Error!","Please Enter Valid Card Expiry.","RowCardExpiry","txtCardExpiry");
				return(false);
			}
			if (txtCardExpiry == "00/00")
			{
				ShowError(true,"Error!","Please Enter Valid Card Expiry.","RowCardExpiry","txtCardExpiry");
				return(false);
			}
			if (txtCardExpiry.substring(0,2) == "00" || txtCardExpiry.substring(3,4) == "0")
			{
				ShowError(true,"Error!","Please Enter Valid Card Expiry.","RowCardExpiry","txtCardExpiry");
				return(false);
			}
			if (parseInt(txtCardExpiry.substring(0,2)) > 12)
			{
				ShowError(true,"Error!","Please Enter Valid Card Expiry.","RowCardExpiry","txtCardExpiry");
				return(false);
			}
			var txtCardCVC = ReplaceChar(document.FrmPay.txtCardCVC.value," ","");
			if (txtCardCVC.length < 3)
			{
				ShowError(true,"Error!","Please Enter Valid 3 or 4 Digit Card CVC - Written on The Back of The Card.","RowCardCVC","txtCardCVC");
				return(false);
			}
		}
		if (IsNumber(document.FrmPay.txtAmount.value,false,true,1) == false)
		{
			ShowError(true,"Error!","Payment Amount is Not Valid.","RowAmount","txtAmount");
			return(false);
		}
		return true;
	}
</script>
</body>
</html>