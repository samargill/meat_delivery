<?php
	$PageID = array(1,0,0);
	$PagePath = "../../";
	$PageMenu = "Clients";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."../lib/PHPMailer-6.1.5/PHPMailer.php");
	include($PagePath."../lib/PHPMailer-6.1.5/SMTP.php");
	include($PagePath."../lib/PHPMailer-6.1.5/Exception.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if (isset($_POST['BtnEmail']) && isset($_POST['ClientID']) && isset($_POST['EmailType']))
	{
		if ($_POST['BtnEmail'] == "SendEmail")
		{
			$ClientID = $_POST['ClientID'];
			$Query = "SELECT name, email, verifycode FROM clientuser".
				" WHERE clientid = ".$ClientID." AND usertype = 1";
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) == 0)
			{
				echo("Client Main User Not Found ...");
				die;
			}
			$objRow = mysqli_fetch_object($rstRow);
			$ClientName  = $objRow->name;
			$ClientEmail = $objRow->email;
			$EmailCode   = $objRow->verifycode;
			$Query = "SELECT emailsubject, emailtext".
				" FROM clientemailtype WHERE emailtypeid = ".$_POST['EmailType'];
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) == 0)
			{
				echo("Email Type ".$_POST['EmailType']." Not Found ...");
				die;
			}
			$objRow = mysqli_fetch_object($rstRow);
			$EmailSubject = $objRow->emailsubject;
			$EmailText = $objRow->emailtext;
			if ($EmailText == "")
			{
				echo("Email Type ".$_POST['EmailType']." is Empty ...");
				die;
			}
			if ($_POST['EmailType'] == 1)
			{
				$EmailCode = WebsiteUrl."/signup-verify?VerifyCode=".$EmailCode;
				$EmailText = str_replace("[VerifyLink]",$EmailCode,$EmailText);
			}
			$CopyYear  = GetValue("config_value","websettings","config_id = 102");
			$EmailText = str_replace("[Name]", $ClientName, $EmailText);
			$EmailText = str_replace("[CompanyName]", WebsiteTitle, $EmailText);
			$EmailText = str_replace("[Website]", Website, $EmailText);
			$EmailText = str_replace("[WebsiteUrl]", WebsiteUrl, $EmailText);
			$EmailText = str_replace("[CopyRight]", $CopyYear, $EmailText);
			echo($EmailText);
			if (DBUserName != "root")
			{
				$Query = "INSERT INTO clientemaillog".
					" (clientid, emaildate, emailtype)".
					" VALUES ('".$ClientID."', NOW(), ".$_REQUEST['EmailType'].")";
				mysqli_query($Conn,$Query);
				$PHPMailer = new PHPMailer();
				$PHPMailer->SendEmail("admin@bullkysms.com",$ClientEmail,$EmailSubject,$EmailText);
				echo("<br><br>Email Sent Successfully To ".$ClientEmail." ...");
			}
			die;
		}
	}


	if (isset($_REQUEST['txtSurveyEmail']) && isset($_REQUEST['ClientID']))
	{
		/* Client Survey Email */
		$SurveyCode    = bin2hex(random_bytes(8));
		$Name          = GetValue("name","clientuser","clientid = ".$_REQUEST['ClientID']);
		$ClientEmail   = GetValue("email","clientuser","clientid = ".$_REQUEST['ClientID']);
		$FromEmail 	   = constant("FromEmail");
		$EmailSubject  = "Survey About WebsiteTitle Services";
		$EmailCode     = constant("WebsiteUrl")."/survey.php?Code=".$SurveyCode."&ClientID=".$_REQUEST['ClientID'];
		$WebsiteTitle  = constant("WebsiteTitle");
		$Website       = constant("WebsiteUrl");
		$WebsiteUrl    = constant("WebsiteUrl");
		$WebsitePhone  = constant("WebsitePhone");
		$CopyrightYear = constant("CopyrightYear");
		$EmailBody = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<title>Email/html</title>
</head>
<body>
	<meta http-equiv="Content-Type" content="text/html; charset=u=tf-8" />
	<div>
		<table align="center" cellpadding="0" cellspacing="0" style="border: 2px dashed #872fd5;" width="650">
			<tbody>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" style="background: linear-gradient(to right, #901ec1 0%, #7450fe 51%, #901ec1 100%); border-bottom: 2px dashed #872fd5; padding: 15px;" width="100%">
						<tbody>
							<tr align="center">
								<td>
									<a href="{$Website}" target="_blank">
										<img alt="" border="0" src="{$WebsiteUrl}/images/logo-email.png" />
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="background:#ffffff; padding:15px;">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<td style="font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #003C52; font-size: 16px;">
									Hi {$Name},<BR><BR>
									Hope you are doing well!<BR><BR>
									Thanks for choosing <a href="{$Website}" target="_BLANK">( {$WebsiteTitle} )</a>.<BR><BR>
									<br>
									Your Suggestion Matters To Us. Please click on the link below for the feedback about our website.</b></p>
									<a href="{$EmailCode}" target="_blank">Take a Short Survey</a>
									<br><br><br>
									Kind Regards,<BR>
									{$WebsiteTitle}'s Team<BR>
									Phone : {$WebsitePhone}<BR>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="background: linear-gradient(to right, #901ec1 0%, #7450fe 51%, #901ec1 100%); border-top: 2px dashed #dddddd; text-align: center; font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #ffffff; padding: 12px; font-size: 12px; font-weight: normal;">
					Copyright {$Website} {$CopyrightYear}
				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
EOD;
		if (DBUserName == "myitvh")
		{
			$Query = "INSERT INTO surveycode".
				" (clientid, surveycode, senddate)".
				" VALUES (".$_REQUEST['ClientID'].", '".$SurveyCode."', NOW())";
			mysqli_query($Conn,$Query);
			echo($EmailBody);
			die();
		}
		if (DBUserName != "myitvh")
		{
			$Query = "INSERT INTO surveycode".
				" (clientid, surveycode, senddate)".
				" VALUES (".$_REQUEST['ClientID'].", '".$SurveyCode."', NOW())";
			mysqli_query($Conn,$Query);
			$PHPMailer = new PHPMailer();
			$PHPMailer->SendEmail($FromEmail,$ClientEmail,$EmailSubject,$EmailBody);
			echo("<br><br>Email Sent Successfully To ".$ClientEmail." ...");
		}
		die;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<style type="text/css">
		.group-addon-color
		{
			background-color: #3c8dbc !important;
		}
		.color-white
		{
			color: #fff;
		}
	</style>
	<script type="text/javascript">
		function SendEmail()
		{
			$Msg = "Are You Sure You Want To Send Email To This Client ?<br><br>"+$("#BtnSendEmail").val();
			$.confirm({
				title: "Confirm!",
				content: $Msg,
				icon: "fa fa-save",
				animation: "scale",
				closeAnimation: "scale",
				opacity: 0.5,
				columnClass: 'col-md-6 col-md-offset-3',
				buttons: {
					"confirm": {
						text: "Yes",
						btnClass: "btn-blue",
						keys: ['enter'],
						action: function() {
							document.FrmSendEmail.BtnEmail.value = "SendEmail";
							document.FrmSendEmail.submit();
						}
					},
					"cancel": {
						text: "No",
						btnClass: "btn-danger",
						keys: ['escape'],
					}
				}
			});
		}
		function GetSurveyEmail(UserID)
		{
			$Msg = "Are You Sure You Want To Send Survey Email To This Client ?<br><br>";
			$.confirm({
				title: "Confirm!",
				content: $Msg,
				icon: "fa fa-save",
				animation: "scale",
				closeAnimation: "scale",
				opacity: 0.5,
				columnClass: 'col-md-6 col-md-offset-3',
				buttons: {
					"confirm": {
						text: "Yes",
						btnClass: "btn-blue",
						keys: ['enter'],
						action: function() {
							document.FrmSurveyEmail.txtSurveyEmail.value = "SurveyEmail";
							document.FrmSurveyEmail.submit();
						}
					},
					"cancel": {
						text: "No",
						btnClass: "btn-danger",
						keys: ['escape'],
					}
				}
			});
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!-- Page Content -->
	<div class="content-wrapper" style="margin-left:0px;">
		<!-- Page Header -->
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
			<div class="container-fluid">
				<div class="card card-outline card-info">
					<div class="card-body">
						<?php
							$Query = "SELECT C.clientname, C.pkgtype, P.pkgname, C.pkgexpiry, C.maxdevice,".
								" C.maxcampaign, CU.mobile, CU.email, CU.verifydate, CU.lastlogin, C.countryid, C.useragent".
								" FROM client C".
								" INNER JOIN package P ON C.pkgtype = P.pkgid".
								" INNER JOIN clientuser CU ON C.clientid = CU.clientid AND CU.usertype = 1".
								" WHERE C.clientid =".$_REQUEST['ClientID'];
							$rstClient = mysqli_query($Conn,$Query);
							if (mysqli_num_rows($rstClient) > 0)
							{
								$objClient = mysqli_fetch_object($rstClient);
								$CountryName = GetValue("countryname","address_country","countryid = ".$objClient->countryid);
						?>
						<div class="row">
							<div class="col-sm-6">
								<strong>Client Name :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-user color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo($objClient->clientname);?>" data-mask readonly>
								</div>
								<strong>Mobile # :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-phone color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo($objClient->mobile);?>" data-mask readonly>
								</div>
								<strong>Package Name :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fas fa-box-open color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo($objClient->pkgname);?>" data-mask readonly>
								</div>
								<div class="form-group">
									<label>Signup via Device :</label>
									<textarea class="form-control" readonly rows="3"><?php echo($objClient->useragent);?></textarea>
								</div>
							</div>
							<div class="col-sm-6">
								<strong>Email :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-envelope color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo($objClient->email);?>" data-mask readonly>
								</div>
								<strong>Verify Date :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-check color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo(ShowDate($objClient->verifydate,1));?>" data-mask readonly>
								</div>
								<strong>Last Login :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-check color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo(ShowDate($objClient->lastlogin,1));?>" data-mask readonly>
								</div>
								<strong>Package Expiry :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-calendar color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo(ShowDate($objClient->pkgexpiry,0));?>" data-mask readonly>
								</div>
								<strong>Country :</strong>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-info">
											<i class="fa fa-globe color-white"></i>
										</button>
									</div>
									<input type="text" class="form-control" value="<?php echo($CountryName);?>" data-mask readonly>
								</div>

							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="card card-info">
									<div class="card-header">
									<h3 class="card-title">Total Devices</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
										</button>
									</div>
									</div>
									<div class="card-body">
										<table id="MyDataTable" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th width="10%" style="text-align:center;">Sr #</th>
													<th width="25%" style="text-align:left;"  >Mobile #</th>
													<th width="30%" style="text-align:left;"  >Mobile Name</th>
													<th width="20%" style="text-align:left;"  >Connect</th>
													<th width="15%" style="text-align:left;"  >Status</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$Index = 0;
													$DevStatus = "";
													$Query = "SELECT CM.mobileno, CM.mobilename, CM.token, CM.lastconnect".
														" FROM clientmobile CM".
														" INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
														" WHERE CHM.clientid =".$_REQUEST['ClientID'];
													$rstRow = mysqli_query($Conn,$Query);
													$TotalDevs = mysqli_num_rows($rstRow);
													while ($objRow = mysqli_fetch_object($rstRow))
													{
														$Index++;
														if ($objRow->token != "")
															$DevStatus = "Reg";	
														else
															$DevStatus = "Not Reg";
														if ($objRow->lastconnect == null)
															$LastConnect = "";
														else
															$LastConnect = substr($objRow->lastconnect,0,16);
												?>
												<tr>
													<td><?php echo($Index);?></td>
													<td><?php echo($objRow->mobileno);?></td>
													<td><?php echo($objRow->mobilename);?></td>
													<td><?php echo($LastConnect);?></td>
													<td><?php echo($DevStatus);?></td>
												</tr>
												<?php
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="card card-info">
									<div class="card-header">
									<h3 class="card-title">Total Campaign</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
										</button>
									</div>
									</div>
									<div class="card-body">
										<table id="MyDataTable" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th width="10%"  style="text-align:center;">Sr #</th>
													<th width="41%" style="text-align:left;"  >Campaign Name</th>
													<th width="49%" style="text-align:left;"  >Campaign Device</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$Index = 0;
													$Query = "SELECT CM.mobilename, CM.mobileno, SQ.smsquename".
														" FROM clientmobile CM".
														" INNER JOIN smsque SQ ON SQ.mobileid = CM.mobileid".
														" WHERE SQ.clientid =".$_REQUEST['ClientID'].
														" ORDER BY CM.mobileid DESC LIMIT 0,10";
													$rstRow = mysqli_query($Conn,$Query);
													if (mysqli_num_rows($rstRow) > 0)
													{
														while ($objRow = mysqli_fetch_object($rstRow))
														{
															$Index++;
												?>
												<tr>
													<td><?php echo($Index);?></td>
													<td><?php echo($objRow->smsquename);?></td>
													<td><?php echo($objRow->mobilename."&nbsp - &nbsp".$objRow->mobileno);?></td>
												</tr>
												<?php
														}
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
							<?php
								$PkgID = GetValue("pkgtype","client","clientid=".$_REQUEST['ClientID']);
								if ($PkgID >= 1 && $PkgID < 6)
								{
							?>	
						<div class="row">
							<div class="col-md-6">
								<div class="card card-info">
									<div class="card-header">
									<h3 class="card-title">Client Email Log</h3>

									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
										</button>
									</div>
									</div>
									<div class="card-body">
										<div class="box-body">
											<table id="MyDataTable" class="table table-bordered table-hover">
												<thead>
													<tr>
														<th width="14%" style="text-align:left;" >Sr #</th>
														<th width="44%" style="text-align:left;" >Send Date</th>
														<th width="42%" style="text-align:left;" >Email Type</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$Index = 0; 
														$Query  = "SELECT CEL.emaildate, CET.emailtypename".
															" FROM clientemaillog CEL".
															" INNER JOIN clientemailtype CET ON CEL.emailtype = CET.emailtypeid".
															" WHERE CEL.clientid =".$_REQUEST['ClientID'].
															" ORDER BY emaildate DESC";
														$rstRow = mysqli_query($Conn,$Query);
														while ($objRow = mysqli_fetch_object($rstRow))
														{
															$Index++;
													?>
													<tr>
														<td><?php echo($Index);?></td>
														<td><?php echo(ShowDate($objRow->emaildate,1));?></td>
														<td><?php echo($objRow->emailtypename);?></td>
													</tr>
													<?php
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<form name="FrmSendEmail" action="client-detail" method="post">
									<?php
										$EmailType = 6;
										if ($objClient->verifydate == NULL)
										{
											$EmailType = 1;
										}
										else
										{
											if ($TotalDevs == 0)
											{
												$EmailType = 2;
											}
											elseif ($TotalDevs == 1 && $DevStatus == "Not Reg")
											{
												$EmailType = 3;
											}
											else
											{
												$Query = "SELECT smsid FROM smsquelist WHERE smsqueid IN".
													" (SELECT smsqueid FROM smsque WHERE mobileid IN".
													" (SELECT CM.mobileid FROM clientmobile CM INNER JOIN clienthavemob CHM ON CM.mobileid = CHM.mobileid".
													" WHERE CHM.clientid = 1)) LIMIT 0,1";
												$rstRow = mysqli_query($Conn,$Query);
												if (mysqli_num_rows($rstRow) == 0)
												{
													$EmailType = 4;
												}
												else
												{
													$EmailType = 5;
												}
											}
										}
										if ($EmailType == 6)
											$BtnText = "Send Newsletter To Client";
										else
										{
											$BtnText = "Motivate Client To ".GetValue("emailtypename","clientemailtype","emailtypeid = ".$EmailType);
										}
									?>
									<input type="hidden" name="ClientID" value="<?php echo($_REQUEST['ClientID']);?>">
									<input type="hidden" name="EmailType" value="<?php echo($EmailType);?>">
									<input type="hidden" name="BtnEmail" value="">
									<button type="button" name="BtnSendEmail" id="BtnSendEmail" value="<?php echo($BtnText);?>" class="btn btn-danger" onclick="SendEmail();">
										<i class="fa fa-send"></i> &nbsp; <?php echo($BtnText);?>
									</button>
								</form>
							</div>
						</div>
							<?php
								}
							?>			
						<div class="row">
							<div class="col-md-6">
								<div class="card card-info">
									<div class="card-header">
									<h3 class="card-title">Client Survey Email Log</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
										</button>
									</div>
									</div>
									<div class="card-body">
										<div class="box-body">
											<table id="MyDataTable" class="table table-bordered table-hover">
												<thead>
													<tr>
														<th width="14%" style="text-align:left;" >Sr #</th>
														<th width="44%" style="text-align:left;" >Send Date</th>
														<th width="42%" style="text-align:left;" >Survey Status</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$Index = 0;
														$Query  = "SELECT senddate, receivedate".
															" FROM surveycode".
															" WHERE clientid =".$_REQUEST['ClientID'].
															" ORDER BY senddate DESC";	
														$rstRow = mysqli_query($Conn,$Query);
														while ($objRow = mysqli_fetch_object($rstRow))
														{
															$Index++;
													?>
													<tr>
														<td><?php echo($Index);?></td>
														<td><?php echo(ShowDate($objRow->senddate,1));?></td>
														<td><?php echo($objRow->receivedate);?></td>
													</tr>
													<?php
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">		
								<form name="FrmSurveyEmail" action="client-detail" method="post">
									<input type="hidden" name="ClientID" value="<?php echo($_REQUEST['ClientID']);?>">
									<input type="hidden" name="txtSurveyEmail" value="">
									<?php
										$ChkSurStatus = GetValue("receivedate","surveycode","receivedate IS NOT NULL AND clientid=".$_REQUEST['ClientID']);
										if ($ChkSurStatus == "" || $ChkSurStatus == NULL)
										{
									?>
									<button type="button" name="BtnSurveyEmail" class="btn btn-danger" onclick="GetSurveyEmail();">
										<i class="fa fa-send"></i> &nbsp; Send Survey Email
									</button>
									<?php
										} 
									?>
								</form>
							</div>
						</div>		
						<?php
							}
						?>
					</div>
				</div>
			</div>
		</section>						
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
</body>
</html>