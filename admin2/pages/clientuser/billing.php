<?php
	$PageID = array(4,2,0);
	$PagePath  = "../../";
	$PageMenu  = "User Info";
	$PageTitle = "Subscription Details";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
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
				<div class="card card-primary card-outline card-tabs">
					<div class="card-header p-0 pt-1 border-bottom-0">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
								<?php
									$PkgID 	 = GetValue("pkgtype","client","clientid=".$_SESSION[SessionID."ClientID"]);
									$PkgName = GetValue("pkgname","package","pkgid=".$PkgID);
									if ($PkgID == 6 || $PkgID == 7 || $PkgID == 8)
									{
										$PkgName = $PkgName." SMS";
									}
								?>	
									<div class="form-group">
										<label>Package Name :</label><br>
										<input type="text" value="<?php echo($PkgName);?>" class="form-control" readonly>
									</div>
								<?php
									if ($PkgID == 6 || $PkgID == 7 || $PkgID == 8 || $PkgID == 9)
									{
										// Total Send SMS
										$SmsSent = 0;
										$SmsQueID = GetValue("SQ.smsqueid","clientmobile CM INNER JOIN smsque SQ ON SQ.mobileid = CM.mobileid","SQ.clientid=".$_SESSION[SessionID."ClientID"]);
										if ($SmsQueID != "")
										{
											$QuerySend = "SELECT COUNT(*) As Sent".
												" FROM smsquelist".
												" WHERE smsqueid = ".$SmsQueID." AND smssent IS NOT NULL";
											$rstRow  = mysqli_query($Conn,$QuerySend);
											$objRow  = mysqli_fetch_object($rstRow);
											$SmsSent = $objRow->Sent;
										}
										// Total Received SMS 
										if ($PkgID == 7)
										{
											$QueryRecv= "SELECT COUNT(SmsR.smsid) As TtlSms".
												" FROM smsreclist SmsR".
												" INNER JOIN clienthavemob CHM ON SmsR.mobileid = CHM.mobileid".
												" WHERE CHM.clientid = ".$_SESSION[SessionID."ClientID"];
											$rstRecv  = mysqli_query($Conn,$QueryRecv);
											$objRecv  = mysqli_fetch_object($rstRecv);
											$SmsRecv  = $objRecv->TtlSms;
										}
										$TtlTopUp = sprintf("%0.2f",GetValue("SUM(COD.balance) As TtlAmount","clientorderdetail AS COD INNER JOIN clientorder AS CD ON CD.orderid = COD.orderid","CD.clientid=".$_SESSION[SessionID."ClientID"]));
								?>			
									<div class="form-group">
										<label>Total Send Sms :</label><br>
										<input type="text" value="<?php echo($SmsSent);?>" class="form-control" readonly>
									</div>
									<?php	
										if ($PkgID == 7)
										{
									?>
									<div class="form-group">
										<label>Total Received Sms :</label>
										<input type="text" value="<?php echo($SmsRecv);?>" class="form-control" readonly>
									</div>
									<?php	
										}
										if ($PkgID != 9)
										{
									?>
									<div class="form-group">
										<label>Remaining Balance :</label>
										<input type="text" value="<?php echo($TtlTopUp);?>" class="form-control" readonly>
									</div>
								<?php
										}
									}
								?>	
								</div>
								<div class="col-md-6">
									<?php	
										$SimRate = $SimExp = $PkgExp = $SmsRecvRate = 0;   
										$Query = "SELECT COD.simrate, COD.smssendrate, COD.smsrecvrate, COD.simexpiry, C.pkgexpiry".
										" FROM clientorderdetail COD".
										" INNER JOIN clientorder CO ON CO.orderid = COD.orderid".
										" INNER JOIN client C ON CO.clientid = C.clientid".
										" WHERE CO.clientid = ".$_SESSION[SessionID."ClientID"]." AND pkgid =".$PkgID;
										$rstRow  = mysqli_query($Conn,$Query);
										if (mysqli_num_rows($rstRow) > 0)
										{
											$objRow  = mysqli_fetch_object($rstRow);
											$SimRate = $objRow->simrate;
											$SimExp  = $objRow->simexpiry;
											$PkgExp  = $objRow->pkgexpiry;
											$SmsSendRate = $objRow->smssendrate;
											$SmsRecvRate = $objRow->smsrecvrate;
										}
										if ($PkgID == 7)
										{
									?>	
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Sim Rate :</label>
												<input type="text" value="<?php echo($SimRate);?>" class="form-control" readonly>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Sim Expiry :</label>
												<input type="text" value="<?php echo(date('d-m-Y',strtotime($SimExp)));?>" class="form-control" readonly>
											</div>
										</div>	
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Sms Send Rate :</label>
												<input type="text" value="<?php echo($SmsSendRate);?>" class="form-control" readonly>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Sms Received Rate :</label>
												<input type="text" value="<?php echo($SmsRecvRate);?>" class="form-control" readonly>
											</div>
										</div>
									</div>
									<?php
										}
										if ($PkgID != 9)
										{
											if ($PkgID > 0 && $PkgID < 5)
											{
												$PkgExp = GetValue("pkgexpiry","client","clientid=".$_SESSION[SessionID."ClientID"]);
											}
									?>	
									<div class="form-group">
										<label>Package Expiry :</label>
										<input type="text" value="<?php echo(date('d-m-Y',strtotime($PkgExp)));?>" class="form-control" readonly>
									</div>
									<?php
										}
									?>
								</div>
							</div>
							<?php
								if ($PkgID == 6 || $PkgID == 8 || $PkgID == 9)
								{
							?>	
							<div class="row ">
								<div class="col-lg-3 col-md-6 col-sm-12">
									<div class="form-group">
										<a href="#" class="btn btn-danger btn-block" style="text-decoration: none;" onclick="GoToDedicatedPkg();">
											Upgrade To Dedicated Package
										</a>
									</div>
								</div>		
							</div>
							<?php
								}
							?>
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
<script>
	function GoToDedicatedPkg()
	{
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure you want to upgrade your package',
			// type: 'red',
			icon: "fa fa-question-circle",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: 'col-md-6 col-md-offset-3',
			buttons: {
				"Ok": {
					text: 'OK',
					btnClass: 'btn-blue',
					action: function()
					{
						window.location.href = "<?php echo($PagePath);?>pages/clientuser/new-package-details?Tab=Dedicated";
					}
				},
				"cancel": {
					text: "No",
					btnClass: "btn-danger",
					keys: ['escape'],
					action: function() {
					}
				},
			}
		});
	}
</script>
</body>
</html>