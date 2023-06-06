<?php
	$PageID = array(6,0,0);
	$PagePath = "../../";
	$PageMenu = "User Info";
	$PageTitle= "Your Package Detail";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	include($PagePath."../lib/payment.php");

	if (isset($_POST['BtnPayment']))
	{
		$Query = "SELECT P.pkgid, P.pkgname, PR.pkg_field_value".
			" FROM package P".
			" INNER JOIN package_rate PR ON P.pkgid = PR.pkgid".
			" WHERE P.pkgid =".$_POST['PkgID']." AND PR.pkg_field_id = 1";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			header("Location: package-view?Err=101");
			exit;
		}
		$objRow = mysqli_fetch_object($rstRow);
		// Clear Old Cart
		$Query = "DELETE FROM clientcart WHERE clientid = ".$_SESSION[SessionID."ClientID"];
		mysqli_query($Conn,$Query);
		// New Cart
		$Query = "INSERT INTO clientcart".
			" (cartdate, clientid, amount)".
			" VALUES (NOW(), ".$_SESSION[SessionID."ClientID"].", ".sprintf("%0.2f",$objRow->pkg_field_value).")";
		if (!mysqli_query($Conn,$Query))
		{
			header("Location: package-view?Err=102");
			exit;
		}
		$CartID = mysqli_insert_id($Conn);
		if ($CartID > 0)
		{
			$Query = "INSERT INTO clientcartdetail".
				" (cartid, pkgid, topup)".
				" VALUES (".$CartID.", ".$objRow->pkgid.", ".sprintf("%0.2f",$objRow->pkg_field_value).")";
			mysqli_query($Conn,$Query);
		}
		if ($_POST['cboPayType'] == 0)
		{
			// Process Payment - Convert AUD To USD
			$PkgPrice = $objRow->pkg_field_value * 1.34;
			$Response = SavePayment($CartID,$CartID,$_POST['txtCardNo'],
				$_POST['txtCardExpiry'],$_POST['txtCardCVC'],sprintf("%0.2f",$PkgPrice));
			$Json = json_decode($Response);
			if (isset($Json->APIError))
			{
				header("Location: package-view?Err=104");
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
						header("Location: package-view?Err=1");
						exit;
					}
				}
			}
			header("Location: package-view?Err=104");
			exit;
		}
		else
		{
			header("Location: ".$PagePath."../process-paypal?CartID=".$CartID.
				"&ProductID=".$objRow->pkgid."&ProductName=".$objRow->pkgname."&Amount=".$objRow->pkg_field_value);
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
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-primary card-outline card-tabs">
					<div class="card-header p-0 pt-1 border-bottom-0">
						<div class="card-body">
							<!-- Page Error -->
							<?php
								if (isset($_REQUEST['Err']))
								{
									$Message = "";
									$MessageBG = "callout-danger lead";
									$MessageHead = "Error :";
									$MessageIcon = "fa-exclamation-triangle";
									switch ($_REQUEST['Err'])
									{
										case 1:
											$Message = "Package Upgraded Successfully ...";
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
										$MessageHead = "Note:";
										$MessageBG = "callout-info";
										$MessageIcon = "fa-info-circle";
									}	
							?>
							<div class="pad margin no-print">
								<div class="callout <?php echo($MessageBG);?>" style="margin-bottom: 0!important;">
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
								<!-- <div class="col-md-12"> -->
								<?php
									$Query = "SELECT pkgtype, pkgexpiry,".
										" CASE WHEN pkgexpiry IS NULL THEN 0 ELSE DATEDIFF(CURDATE(),pkgexpiry) END As ExpDays".
										" FROM client WHERE clientid = ".$_SESSION[SessionID."ClientID"];
									$rstRow = mysqli_query($Conn,$Query);
									if (mysqli_num_rows($rstRow) > 0)
									{
										$objRow = mysqli_fetch_object($rstRow);
										$PkgID = $objRow->pkgtype;
										$PkgExpiry = $objRow->pkgexpiry;
										$PkgExDays = $objRow->ExpDays;
									}
									$Query = "SELECT P.pkgid, P.pkgname, PR.pkg_field_value".
										" FROM package P".
										" INNER JOIN package_rate PR ON P.pkgid = PR.pkgid".
										" WHERE P.status = 0 AND P.ispkg = 1 AND PR.pkg_field_id = 1".
										" ORDER BY P.pkgid";
									$rstRow = mysqli_query($Conn,$Query);
									while ($objRow = mysqli_fetch_object($rstRow))
									{
										$BtnName = $objRow->pkgid > 1 ? "Get Access" : "Try Now !";
										if ($objRow->pkg_field_value == 0.00)
											$Price = "Free";
										else
											$Price = "$ ".number_format($objRow->pkg_field_value,2);
								?>
								<div class="col-md-3">
									<div class="card card-primary">
										<div class="card-body card-profile">
											<h2 class="text-center p-2 mb-3 bg-navy rounded"><?php echo($objRow->pkgname);?></h2>
											<h3 class="text-info text-center p-2"><?php echo($Price);?> / Month</h3>
											<?php
												if ($PkgID == $objRow->pkgid && $PkgID > 1)
												{
													$ExpText = "Expiring on";
													if ($PkgExDays < -10)
														$ExpStyle = "text-success";
													elseif ($PkgExDays > 0)
													{
														$ExpStyle = "text-danger";
														$ExpText = "Expired on";
													}
													else
														$ExpStyle = "text-danger";
											?>
											<h4 class="<?php echo($ExpStyle);?> text-center"><?php echo($ExpText);?> : <?php echo(ShowDate($PkgExpiry,0));?></h4>
											<?php
												}
											?>
											<ul class="list-group list-group-unbordered">
												<?php
													$Query = "SELECT feattext FROM package_feature".
														" WHERE pkgid = ".$objRow->pkgid."".
														" ORDER BY featid";
													$rstPro = mysqli_query($Conn,$Query);
													while ($objFea = mysqli_fetch_object($rstPro))
													{
												?>
												<li class="list-group-item">
													<i class="fa fa-check"></i> &nbsp; <?php echo($objFea->feattext);?>
												</li>
												<?php
													}
												?>
											</ul>
											<?php
												if ($PkgID == $objRow->pkgid)
												{
											?>
											<a href="#" class="btn btn-success btn-block"><b>Currently Active</b></a>
											<?php
												}
												elseif ($PkgID > $objRow->pkgid)
												{
											?>
											<a href="#" class="btn btn-danger btn-block"><b>Not Needed</b></a>
											<?php
												}
												else
												{
											?>
											<a href="#" class="btn btn-primary btn-block" onclick="TakePayment(<?php echo($objRow->pkgid);?>,<?php echo($objRow->pkg_field_value);?>);"><b>Apply Now</b></a>
											<?php
												}
											?>
										</div>
									</div>
								</div>
								<?php
									}
								?>
							</div>
							<?php
								}
							?>
						</div>
					</div>
				</div>			
			</div>
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<!-- Payment Modal -->
<div id="Modal-Payment" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" id="pass-content">
			<form name="FrmPay" id="FrmPay" action="package-view" method="post" autocomplete="off">
				<div class="modal-header" style="background-color: #094751;">
					<h4 id="Modal-Payment-Title" class="modal-title text-white">Upgrade Package</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true" class="text-white">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Amount</label>
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
<script>
	$(function() {
		//Init Select2
		$(".select2").select2();
		// Payment Input Masks
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
	});
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
	function TakePayment(PkgID,PkgPrice)
	{
		$("#PkgID").val(PkgID);
		$("#txtAmount").val(PkgPrice);
		$("#Modal-Payment").modal();
	}
	function VerifyPayment()
	{
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