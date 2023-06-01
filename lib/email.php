<?php
	function GetEmail($EmailType,$CartID)
	{
		$WebLink        = WebsiteUrl;
		$WebsiteAddress = WebsiteAddress1." ".WebsiteAddress2;
		$WebsitePhone   = WebsitePhone;
		$WebsiteEmail   = AdminEmail;
		$GLOBALS["SubTotal"] = 0.00;
		$Patient = array();
		$PatientLabel = "Patient";
		$OrderPrefix = "";
		if ($EmailType == "Consultation" || $EmailType == "Tele Consultation")
		{
			$OrderPrefix = "CONS";
			$Query = "SELECT consdate As CartDate, patientid, familyid, medicaretype, amount".
				" FROM consultation WHERE consid = ".$CartID;
			$rstCart = mysqli_query($GLOBALS["Conn"],$Query);
			$objCart = mysqli_fetch_object($rstCart);
		}
		elseif ($EmailType == "Prescription")
		{
			$OrderPrefix = "PRES";
			$Query = "SELECT presdate As CartDate, patientid, familyid, speid, pharmacyid, medicaretype,".
				" dlvaddressid, dlvaddress, deliverycharges, repeatcount, repeatcharges, amount".
				" FROM prescription WHERE presid = ".$CartID;
			$rstCart = mysqli_query($GLOBALS["Conn"],$Query);
			$objCart = mysqli_fetch_object($rstCart);
			list($Suburb,$PostCode,$Status) = GetAddress($objCart->dlvaddressid);
			$Patient["Address"] = $objCart->dlvaddress.", ".$Suburb." ".$PostCode;
			$SpeName = GetValue("spename","speciality","speid = ".$objCart->speid);
			$Query = "SELECT pharmacyname, addressid, address FROM pharmacy WHERE pharmacyid = ".$objCart->pharmacyid;
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				$Pharmacy = $objRow->pharmacyname;
				list($Suburb,$PostCode,$Status) = GetAddress($objRow->addressid);
				$Pharmacy .= ", ".$objRow->address.", ".$Suburb." ".$PostCode;
			}
		}
		elseif ($EmailType == "Certificate")
		{
			$OrderPrefix = "CERT";
			$Query = "SELECT certid, certtype, certdate As CartDate, patientid, familyid, medicaretype, amount".
				" FROM certificate WHERE certid = ".$CartID;
			$rstCart = mysqli_query($GLOBALS["Conn"],$Query);
			$objCart = mysqli_fetch_object($rstCart);
			if ($objCart->certtype == 1)
			{
				$PatientLabel = "Carer";
			}
		}
		elseif ($EmailType == "Referral")
		{
			$OrderPrefix = "REFE";
			$Query = "SELECT refetype, refedate As CartDate, patientid, familyid, medicaretype, amount".
				" FROM referral WHERE refeid = ".$CartID;
			$rstCart = mysqli_query($GLOBALS["Conn"],$Query);
			$objCart = mysqli_fetch_object($rstCart);
		}
		$CartDate = ShowDate($objCart->CartDate,1);
		// Patient Detail
		$Patient = GetPatientDetail($objCart->patientid,$objCart->familyid);
		$TextColor = "0, 60, 82";
		$Email = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" name="viewport">
	<title>Thank You Email</title>
	<style>
		.ReadMsgBody {
			width: 100%;
			background-color: #ffffff;
		}
		.ExternalClass {
			width: 100%;
			background-color: #ffffff;
		}
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
			line-height: 100%;
		}
		html {
			width: 100%;
		}
		body {
			-webkit-text-size-adjust: none;
			-ms-text-size-adjust: none;
			margin: 0;
			padding: 0;
		}
		table {
			border-spacing: 0;
			table-layout: fixed;
			margin: 0 auto;
		}
		table table table {
			table-layout: auto;
		}
		img {
			display: block ! important;
			overflow: hidden ! important;
		}
		table td {
			border-collapse: collapse;
		}
		.yshortcuts a {
			border-bottom: none ! important;
		}
		a {
			color: #FFFFFF;
			text-decoration: none;
		}
		.textbutton a {
			font-family: 'Roboto', sans-serif !importat;
			color: #ffffff ! important;
		}
		.footer-link a {
			color: #979797 ! important;
		}
		.tpl-content {
			padding:0px ! important;
		}
		/* Responsive */
		@media only screen and (max-width: 680px) {
			body {
				width: auto ! important;
			}
			table[class="table680"] {
				width: 440px ! important;
			}
			table[class="table-inner"] {
				width: 82% ! important;
				text-align: center ! important;
			}
			table[class="table1"] {
				width: 100% ! important;
				text-align: center ! important;
			}
			table[class="table2"] {
				width: 100% ! important;
				text-align: center ! important;
				margin-bottom:35px ! important;
			}		
			table[class="table3"] {
				width: 100% ! important;
				text-align: center ! important;
				margin-bottom:25px ! important;
			}
			td[class="td_width"] {
				 width:440px !important;
				 text-align:center ! important;
				 display:block !important;
				 margin:auto;
				 border:none ! important;
			}	  
			td[class="td_600"] {
				 width:100% !important;
				 text-align:center ! important;
				 display:block !important;
				 margin:auto;
				 border:none ! important;
			}	
			td[class="text-center"] {
				text-align:center ! important;
			}
			td[class="padding_hide"] {
				padding:0px ! important;
				text-align:center ! important;
			}
			td[class="padding"] {
				padding-left:25px ! important;
				padding-right:25px ! important;
				text-align:center ! important;
			}
			td[class="td_hide"] {
				width: 0% !important;
				height: 0px ! important;
				display:none ! important;
			}
			/* image */
			img[class="img1"] {
				width: 100% ! important;
				height: 100% ! important;
			}
		}
		@media only screen and (max-width: 479px) {
			body {
				width: auto ! important;
			}
			table[class="table680"] {
				width: 302px ! important;
			}
			table[class="table-inner"] {
				width: 82% ! important;
				text-align: center ! important;
			}
			table[class="table1"] {
				width: 100% ! important;
				text-align: center ! important;
			}    
			table[class="table2"] {
				width: 100% ! important;
				text-align: center ! important;
				margin-bottom:35px ! important;
			}     
			table[class="table3"] {
				width: 100% ! important;
				text-align: center ! important;
				margin-bottom:25px ! important;
			} 
			td[class="td_width"] {
				 width:302px !important;
				 text-align:center ! important;
				 display:block !important;
				 margin:auto;
				 border:none ! important;
			}
			td[class="td_600"] {
				 width:100% !important;
				 text-align:center ! important;
				 display:block !important;
				 margin:auto;
				 border:none ! important;
				 padding:0px;
			}	
			td[class="text-center"] {
				text-align:center ! important;
			}
			td[class="padding_hide"] {
				padding:0px ! important;
			}	
			td[class="padding"] {
				padding-left:25px ! important;
				padding-right:25px ! important;
				text-align:center ! important;
			}	
			td[class="td_hide"] {
				width: 0% !important;	
				height: 0px ! important;
				display:none ! important;
			}
			/* image */
			img[class="img1"] {
				width: 100% ! important;
				height: 100% ! important;
			}
		}
	</style>
</head>
<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">
<table data-bgcolor="background" style="background-color: rgb(33, 33, 33); opacity: 1; position: relative; z-index: 0;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#212121" align="center">
	<tr>
		<td>
		<table class="table680" data-bg="bg-photo2" data-bgcolor="body4" style="border-bottom:2px dashed #DE006F; border-top:2px dashed #DE006F;background-color: #fffff; background-position: 50% 100%; background-size: cover; max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center">
			<tr><td height="30"></td></tr>
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="" align="center">
					<tr>
						<td data-color="module4_text1" data-size="module4_text1" mc:edit="ab19" style="color: rgb(255, 255, 255); font-family: 'Roboto', sans-serif; font-size: 26px; font-weight: 700; letter-spacing: 2px; line-height: 36px;" valign="top" align="center">
							<a href="{$WebLink}" target="_blank">
								<img src="{$WebLink}/images/logo-email.png" width="300px">
							</a>
						</td>
					</tr>
					<tr>
						<td data-color="module4_text1" data-size="module4_text1" mc:edit="ab19" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 26px; font-weight: 700; letter-spacing: 2px; line-height: 36px;" valign="top" align="center">
							Your Order # {$OrderPrefix}-{$CartID}
						</td>
					</tr>
					<tr>
						<td data-color="module4_text2" data-size="module4_text2" mc:edit="ab20" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;padding-top:3px;" valign="top" align="center">
							Placed on {$CartDate}
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table class="table680" data-bgcolor="body3" style="background-color: rgb(255, 255, 255); max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center">
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr><td height="30"></td></tr>
					<tr>
						<td class="text-center" data-color="module3_text1" data-link-color="module3_text1" data-link-style="text-decoration:none; color:#FFFFFF;" data-size="module3_text1" mc:edit="ab12" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: 2px; line-height: 32px;" valign="top" align="center">
							Thank You For Your Order
						</td>
					</tr>
					<tr>
						<td class="text-center" data-color="module3_text2" data-link-color="module3_text2" data-link-style="text-decoration:none; color:#FFFFFF;" data-size="module3_text2" mc:edit="ab13" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px; padding-top: 3px;" valign="top" align="center">
							Your order has been received and is now being processed. A summary of your order is shown below for your reference. For complete details please see the email attachment.
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table class="table680" data-bgcolor="body5" style="background-color: rgb(255, 255, 255); max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center">
			<tr><td height="30"></td></tr>
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td class="td_600" valign="top">
						<table class="table2" width="315" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td>
								<table class="table1" width="285" cellspacing="0" cellpadding="0" border="0" align="left">
									<tr>
										<td valign="top">
										<table class="table1" width="230" cellspacing="0" cellpadding="0" border="0" align="left">
											<tr>
												<td class="text-center" data-color="module5_text1" data-link-color="module5_text1" data-link-style="text-decoration:none; color:#F63C51;" data-size="module5_text1" mc:edit="ab21" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 500;" valign="top" align="left">
													{$PatientLabel} Name:
												</td>
											</tr>
											<tr>
												<td class="text-center" data-color="module5_text2" data-size="module5_text2" mc:edit="ab22" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
													{$Patient["Name"]}
												</td>
											</tr>
										</table>
										</td>
									</tr>
									<tr>
										<td valign="top">
										<table class="table1" width="230" cellspacing="0" cellpadding="0" border="0" align="left">
											<tr>
												<td class="text-center" data-color="module5_text1" data-link-color="module5_text1" data-link-style="text-decoration:none; color:#F63C51;" data-size="module5_text1" mc:edit="ab21" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 500;" valign="top" align="left">
													Gender :
												</td>
											</tr>
											<tr>
												<td class="text-center" data-color="module5_text2" data-size="module5_text2" mc:edit="ab22" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
													{$Patient["Gender"]}
												</td>
											</tr>
										</table>
										</td>
									</tr>
									<tr>
										<td valign="top">
										<table class="table1" width="230" cellspacing="0" cellpadding="0" border="0" align="left">
											<tr>
												<td class="text-center" data-color="module5_text1" data-link-color="module5_text1" data-link-style="text-decoration:none; color:#F63C51;" data-size="module5_text1" mc:edit="ab23" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 500;" valign="top" align="left">
													Date of Birth :
												</td>
											</tr>
											<tr>
												<td class="text-center" data-color="module5_text2" data-size="module5_text2" mc:edit="ab24" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
													{$Patient["DOB"]}
												</td>
											</tr>
										</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<td class="td_600" valign="top">
EOD;
		if ($EmailType == "Prescription")
		{
			$DeliveryType = $DeliveryDetail = "";
			if ($objCart->pharmacyid > 0)
			{
				$DeliveryType = "Deliver To Pharmacy :";
				$Query = "SELECT pharmacyname, addressid, address".
					" FROM pharmacy WHERE pharmacyid = ".$objCart->pharmacyid;
				$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
				if (mysqli_num_rows($rstRow) > 0)
				{
					$objRow = mysqli_fetch_object($rstRow);
					$DeliveryDetail = $objRow->pharmacyname;
					list($Suburb,$PostCode,$Status) = GetAddress($objRow->addressid);
					$DeliveryDetail .= ", ".$objRow->address.", ".$Suburb." ".$PostCode;
				}
			}
			elseif ($objCart->dlvaddressid > 0)
			{
				$DeliveryType = "Deliver To Address :";
				list($Suburb,$PostCode,$Status) = GetAddress($objCart->dlvaddressid);
				$DeliveryDetail = $objCart->dlvaddress.", ".UCString($Suburb)." ".$PostCode;
			}
		$Email .= <<<EOD

						<table class="table1" width="285" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td valign="top">
								<table class="table1" width="230" cellspacing="0" cellpadding="0" border="0" align="left">
									<tr>
										<td class="text-center" data-color="module5_text1" data-link-color="module5_text1" data-link-style="text-decoration:none; color:#F63C51;" data-size="module5_text1" mc:edit="ab23" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 500;" valign="top" align="left">
											{$DeliveryType}
										</td>
									</tr>
									<tr>
										<td class="text-center" data-color="module5_text2" data-size="module5_text2" mc:edit="ab24" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
											{$DeliveryDetail}
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
EOD;
		}
		$Email .= <<<EOD

						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>	  
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table class="table680" data-bgcolor="body6" style="background-color: #ffffff; max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#F0F0F0" align="center">
			<tr><td height="30"></td></tr>
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td data-color="module6_text1" data-size="module6_text1" mc:edit="ab25" style="color: rgb({$TextColor}); font-family: 'Roboto',  sans-serif; font-size: 24px; font-weight: 700; letter-spacing: 2px; line-height: 26px; padding-top: 0px;" align="center">
							{$EmailType} Details
						</td>
					</tr>
				</table>
				</td>
			</tr> 
			<tr><td height="30"></td></tr>
			<tr>
				<td style="padding-bottom: 15px;" valign="top">
				<table class="table-inner" data-bgcolor="line3" style="background-color: rgb(222, 0, 111);" width="600" height="1" cellspacing="0" cellpadding="0" border="0" bgcolor="#C9C9C9" align="center">
					<tr>
						<td style="line-height: 0px; font-size: 0px;"> &nbsp; </td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td class="td_600" valign="top">
						<table class="table1" width="300" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td class="text-center" data-color="module6_text3" data-size="module6_text3" mc:edit="ab27" style="color: rgb({$TextColor}); font-family: 'Roboto',  sans-serif; font-size: 15px; font-weight: 500; letter-spacing: 1px; line-height: 26px;" align="left">
									Items In Your Order
								</td>
							</tr>
						</table>
						</td>
						<td class="td_600" valign="top">
						<table class="table1" width="250" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td class="padding_hide" data-color="module6_text3" data-size="module6_text3" mc:edit="ab28" style="color: rgb({$TextColor}); font-family: 'Roboto',  sans-serif; font-size: 15px; font-weight: 500; letter-spacing: 1px; line-height: 26px;" align="right">
									Price
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
EOD;
		if ($EmailType == "Consultation" || $EmailType == "Tele Consultation")
		{
			$CartField = "consid";
			$CartTable = "consultationfee";
		}
		elseif ($EmailType == "Prescription")
		{
			$CartField = "presid";
			$CartTable = "prescriptionfee";
		}
		elseif ($EmailType == "Certificate")
		{
			$CartField = "certid";
			$CartTable = "certificatefee";
		}
		elseif ($EmailType == "Referral")
		{
			$CartField = "refeid";
			$CartTable = "referralfee";
		}
		$Query = "SELECT CF.feeid, SF.feename, CF.amount, SF.sorting".
			" FROM ".$CartTable." CF INNER JOIN servicefees SF ON CF.feeid = SF.feeid".
			" WHERE CF.".$CartField." = ".$CartID.
			" ORDER BY CF.feeid";
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		while ($objRow = mysqli_fetch_object($rstRow))
		{
			$FeeName = $objRow->feename;
			if ($objCart->medicaretype == 1 && $objRow->sorting == 0)
				$FeePrice = "Bulk Billed";
			else
				$FeePrice = sprintf("%0.2f",$objRow->amount);
			$Email .= GetOrderDetail($FeeName,$FeePrice);
			$GLOBALS['SubTotal'] += $objRow->amount;
		}
		if ($EmailType == "Prescription")
		{
			$Query = "SELECT medid, sizeid FROM prescriptiondetail WHERE bookid = 'PRES-".$CartID."'";
			$rstPro = mysqli_query($GLOBALS["Conn"],$Query);
			while ($objPro = mysqli_fetch_object($rstPro))
			{
				$objMedSize = null;
				$MedName = GetMedName(false,$objPro,$objMedSize,true);
				$Email .= GetOrderDetail($MedName,"");
			}
			if ($objCart->repeatcount > 0)
			{
				$MedName = "Prescription Extra Charges For ".$objCart->repeatcount." Repeats";
				$Email .= GetOrderDetail($MedName,$objCart->repeatcharges);
				$GLOBALS['SubTotal'] += $objCart->repeatcharges;
			}
		}
		$GLOBALS['SubTotal'] = sprintf("%0.2f",$GLOBALS['SubTotal']);
		$Email .= <<<EOD
			<tr><td height="15"></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table class="table680" data-bgcolor="body6" style="background-color: #ffffff; max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center">
			<tr>
				<td>
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td>
						<table class="table1" width="280" cellspacing="0" cellpadding="0" border="0" align="right">
							<tr>
								<td>
								<table class="" width="240" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td>
										<table class="" width="130" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td data-color="module11_text1" data-size="module11_text1" mc:edit="ab41" style="color:rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="right">
													Subtotal :
												</td>
											</tr>
EOD;
		if ($EmailType == "Prescription")
		{
		$Email .= <<<EOD
											<tr>
												<td data-color="module11_text1" data-size="module11_text1" mc:edit="ab41" style="color:rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="right">
													Delivery Charges :
												</td>
											</tr>
EOD;
		}
		$Email .= <<<EOD
										</table>
										</td>
										<td>
										<table width="110" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td data-color="module11_text1" data-size="module11_text1" mc:edit="ab44" style="color:rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px; padding-left: 35px;" valign="top" align="right">
													A\${$GLOBALS["SubTotal"]}
												</td>
											</tr>
EOD;
		if ($EmailType == "Prescription")
		{
		$Email .= <<<EOD
											<tr>
												<td data-color="module11_text1" data-size="module11_text1" mc:edit="ab44" style="color:rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px; padding-left: 35px;" valign="top" align="right">
													A\${$objCart->deliverycharges}
												</td>
											</tr>
EOD;
		}
		$Email .= <<<EOD
										</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
							<tr><td height="15"></td></tr>
							<tr>
								<td valign="top">
								<table class="table1" data-bgcolor="line4" style="background-color:rgb(222, 0, 111);" width="240" height="1" cellspacing="0" cellpadding="0" border="0" bgcolor="#C9C9C9" align="right">
									<tr>
										<td style="line-height: 0px; font-size: 0px;"> &nbsp; </td>
									</tr>
								</table>
								</td>
							</tr>
							<tr><td height="15"></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table class="table1" width="280" cellspacing="0" cellpadding="0" border="0" align="right">
							<tr>
								<td>
								<table class="" width="240" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td>
										<table class="" width="130" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td data-color="module11_text2" data-size="module11_text2" mc:edit="ab47" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 500; line-height: 26px;" valign="top" align="right">
													Grand total :
												</td>
											</tr>
										</table>
										</td>
										<td>
										<table class="" width="110" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td data-color="module11_text2" data-size="module11_text2" mc:edit="ab48" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 500; line-height: 26px; padding-left: 35px;" valign="top" align="right">
													A\${$objCart->amount}
												</td>
											</tr>
										</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table class="table680" data-bg="bg-photo5" data-bgcolor="body19" style="background-color:#de006f; background-position: 50% 100%; background-size: cover; max-width: 800px;" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#363636" align="center">
			<tr><td height="30"></td></tr>
			<tr>
				<td valign="top">
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td class="td_600" valign="top">
						<table class="table1" width="200" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td>
								<table class="table2" width="185" cellspacing="0" cellpadding="0" border="0" align="left">
									<tr>
										<td class="text-center" data-color="module20_text2" data-size="module20_text2" mc:edit="ab86" style="color: rgb(255, 255, 255); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
											{$WebsiteAddress}
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<td class="td_600" valign="top">
						<table class="table1" width="240" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td valign="top">
								<table class="table2" width="180" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td class="text-center" data-color="module20_text2" data-size="module20_text2" mc:edit="ab87" style="color: rgb(255, 255, 255); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
											<a href="mailto:{$WebsiteEmail}">{$WebsiteEmail}</a>
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<td class="td_600" valign="top">
						<table class="table1" width="150" cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>
								<td class="text-center" data-color="module20_text2" data-size="module20_text2" mc:edit="ab88" style="color: rgb(255, 255, 255); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 400; line-height: 26px;" valign="top" align="left">
									<a href="callto:{$WebsitePhone}">{$WebsitePhone}</a>
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
EOD;
		return($Email);
	}

	function GetOrderDetail($Desc,$Price)
	{
		$TextColor = "0, 60, 82";
		$Email = <<<EOD

			<tr><td height="10"></td></tr>
			<tr>
				<td>
				<table class="table-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td class="td_600" valign="top">
						<table class="table1" width="600" style="background-color: rgb(255, 255, 255); border: 1px; border-color: rgb(222, 0, 111); border-style: solid;" cellspacing="0" cellpadding="0" border="0" bgcolor="#FBF9F9" align="left">
							<tr><td height="23"></td></tr>
							<tr>
								<td valign="top">
								<table class="table1" width="560" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td class="td_600" valign="top">
										<table class="table1" width="450" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td class="padding" data-color="module7_text1" data-link-color="module7_text1" data-link-style="text-decoration:none; color:#595959;" data-size="module7_text_1" mc:edit="ab29" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 500; letter-spacing: 1px; line-height: 26px;" valign="top" align="left">
													{$Desc}
												</td>
											</tr>
										</table>
										</td>
										<td class="td_600" valign="top">
										<table class="table1" width="110" cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td class="text-center" data-color="module7_text3" data-size="module7_text3" mc:edit="ab31" style="color: rgb({$TextColor}); font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 26px;" valign="top" align="right">
													{$Price}
												</td>
											</tr>
										</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							<tr><td height="23"></td></tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
EOD;
		return($Email);
	}

	function GetSignupPatientEmail($FirstName,$EmailCode)
	{
		/* Admin Email */
		$EmailCode = constant("WebsiteUrl")."/signup-verify?VerifyCode=".$EmailCode;
		$WebsiteTitle  = constant("WebsiteTitle");
		$Website       = constant("Website");
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
		<table align="center" cellpadding="0" cellspacing="0" style="border: 2px dashed #DE006F;" width="650">
			<tbody>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px dashed #DE006F; padding: 15px;" width="100%">
						<tbody>
							<tr align="center">
								<td>
									<a href="{$Website}" target="_blank">
										<img alt="" border="0" src="{$WebsiteUrl}/images/logo-email.png" width="300px" />
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
								<td style="font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #000000; font-size: 16px;">
									Hi {$FirstName},<BR><BR>
									Thanks for creating account with <a href="{$Website}" target="_BLANK">{$Website} ( {$WebsiteTitle} )</a>.<BR><BR>
									<br>
									Please click on the link below to confirm that we got the right email address.
									<br><br>
									<a href="{$EmailCode}">Verify Email</a>
									<br><br>
									Or copy and paste the link below.
									<br><br>{$EmailCode}<br><br>
									You can use this account to:
									<ul>
										<li>Book Video Consultation With Doctor</li>
										<li>Get Medicine Prescription</li>
										<li>Get Medical Certificate</li>
										<li>Get Specialist Referrals</li>
									</ul>
									You can also download our mobile app (Android / iOS) to request above services more easily.
									To download our mobile app just visit our website <a href="{$Website}" target="_BLANK">{$Website}</a><BR><BR>
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
				<td style="background: #DE006F; border-top: 2px dashed #dddddd; text-align: center; font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #ffffff; padding: 12px; font-size: 12px; font-weight: normal;">
					Copyright {$Website} {$CopyrightYear}
				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
EOD;
		return($EmailBody);
	}

	function GetSignupAdminEmail($FirstName,$Suburb,$Mobile,$Email,$MysqlErr)
	{
		if ($MysqlErr != "")
			$EmailBody = "New Patient Signup Failed. Contact Patient ASAP"."<BR><BR>";
		else
			$EmailBody = "New Patient Signup Created Successfully in Admin Panel";
		$EmailBody = "".
			"<HTML>".
				"<BODY>".
					"New Patient Signup From Website<BR><BR>".
					"User Name : ".$FirstName."<BR><BR>".
					"Suburb : ".$Suburb."<BR><BR>".
					"Mobile : ".$Mobile."<BR><BR>".
					"Email : ".$Email."<BR><BR>".
					"".$EmailBody.
					"<Error>".
				"</BODY>".
			"</HTML>";
		return($EmailBody);
	}

	function GetForgotPasswordEmail($PatientID,$Email)
	{

		$PatientName = GetValue("firstname","patientfamily","patientid = ".$PatientID." AND familyid = 1");
		$PwdCode = mt_rand(1000000000,9999999999);
		$Query = "UPDATE patient SET ".
			"  pwdresetcode = '".$PwdCode."'".
			", pwdresettime = NOW()".
			"  WHERE patientid = ".$PatientID;
		@mysqli_query($GLOBALS["Conn"],$Query);
		$WebsiteTitle  = constant("WebsiteTitle");
		$Website       = constant("Website");
		$WebsiteUrl    = constant("WebsiteUrl");
		$WebsitePhone  = constant("WebsitePhone");
		$CopyrightYear = constant("CopyrightYear");
		$ResetLink = $WebsiteUrl."/reset-password?Email=".urlencode($Email)."&Code=".$PwdCode;
		$EmailBody = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<title>Email/html</title>
</head>
<body>
	<meta http-equiv="Content-Type" content="text/html; charset=u=tf-8" />
	<div>
		<table align="center" cellpadding="0" cellspacing="0" style="border: 2px dashed #DE006F;" width="650">
			<tbody>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px dashed #DE006F; padding: 15px;" width="100%">
						<tbody>
							<tr align="center">
								<td>
									<a href="{$Website}" target="_blank">
										<img alt="" border="0" src="{$WebsiteUrl}/images/logo-email.png" width="300px" />
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
									Hi {$PatientName},<BR><BR>
									You recently requested to reset your password.
									<br><br>
									Please click the link below to change password.
									<br><br>
									<A href="{$ResetLink}">Click Here To Reset Password</A>
									<br><br>
									Or copy and paste the link below.
									<br><br>
									{$ResetLink}
									<br><br>
									If you didn't requested a password reset, someone may have been 
									trying to access your account without your permission. As long as 
									you do not click the link contained in this email, no action will be 
									taken and your account will remain secure.<BR><BR>
									Kind Regards,<BR>
									{$WebsiteTitle}'s Team
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="background: #DE006F; border-top: 2px dashed #dddddd; text-align: center; font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #ffffff; padding: 12px; font-size: 14px; font-weight: normal;">
					Copyright {$Website} {$CopyrightYear}
				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
EOD;
		return($EmailBody);
	}

	function GetEmailTemplate($EmailID,$OrderID)
	{
		$OrderParts   = explode("-",$OrderID);
		$EmailSubject = constant("WebsiteTitle");
		$ReSchedLink  = "";
		if ($EmailID == 301)
		{
			$EmailSubject .= "";
		}
		elseif ($EmailID == 302)
		{
			$EmailSubject .= "";
		}
		elseif ($EmailID == 303)
		{
			$EmailSubject .= " - Video Call Failed ".$OrderID;
		}
		elseif ($EmailID == 304)
		{
			$EmailSubject .= " - Video Call Failed ".$OrderID;
		}
		elseif ($EmailID == 305)
		{
			$EmailSubject .= " - Prescription Update ".$OrderID;
		}
		elseif ($EmailID == 306)
		{
			$EmailSubject .= " - Prescription Update ".$OrderID;
		}
		elseif ($EmailID == 307)
		{
			$EmailSubject .= " - Booking Refund ".$OrderID;
		}
		// Get Patient Detail From Booking
		if ($OrderParts[0] == "CONS")
		{
			$Query = "SELECT patientid, familyid, videomode, videocontact, filecode".
				" FROM consultation WHERE consid = ".$OrderParts[1];
		}
		elseif ($OrderParts[0] == "PRES")
		{
			$Query = "SELECT patientid, familyid, 0 As videomode, '' As videocontact, filecode".
				" FROM prescription WHERE presid = ".$OrderParts[1];
		}
		elseif ($OrderParts[0] == "CERT")
		{
			$Query = "SELECT patientid, familyid, videomode, videocontact, filecode".
				" FROM certificate  WHERE certid = ".$OrderParts[1];
		}
		elseif ($OrderParts[0] == "REFE")
		{
			$Query = "SELECT patientid, familyid, 0 As videomode, '' As videocontact, filecode".
				" FROM referral     WHERE refeid = ".$OrderParts[1];
		}
		$rstCart = mysqli_query($GLOBALS["Conn"],$Query);
		$objCart = mysqli_fetch_object($rstCart);
		if ($objCart->videomode > 0)
		{
			$CallMode = GetValue("consmodename","consultationmode","consmodeid = ".$objCart->videomode);
			$CallMode = $CallMode." ".$objCart->videocontact;
		}
		else
		{
			$CallMode = "";
		}
		if ($EmailID == 303 || $EmailID == 304)
		{
			$Query = "UPDATE bookingcalllog".
				" SET status = 2 WHERE bookid = '".$OrderID."' AND status = 0";
			mysqli_query($GLOBALS["Conn"],$Query);
			$Query = "INSERT INTO bookingcalllog".
				" (bookid, failureid, logtime, doctorid)".
				" VALUES ('".$OrderID."', ".$EmailID.", NOW(), ".$_SESSION[SessionID].")";
			mysqli_query($GLOBALS["Conn"],$Query);
			$LogID = mysqli_insert_id($GLOBALS["Conn"]);
			$ReSchedLink = constant("WebsiteUrl")."/reschedule-booking?".UCString($OrderParts[0])."=".$objCart->filecode."*".$LogID;
			$ReSchedLink = "<a href=\"".$ReSchedLink."\" target=\"_blank\">Reschedule Your Appointment</a>";
		}
		// Get Email Template
		$EmailBody = GetValue("config_value","websettings","config_id = ".$EmailID);
		// Get Patient Name
		$Query = "SELECT firstname, gender, dateofbirth".
			" FROM patientfamily".
			" WHERE patientid = ".$objCart->patientid." AND familyid = ".$objCart->familyid;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$PatientName   = $objRow->firstname;
		}
		// Get Patient Email
		$PatientEmail = "";
		$Query = "SELECT email FROM patient WHERE patientid = ".$objCart->patientid;
		$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			$objRow = mysqli_fetch_object($rstRow);
			$PatientEmail = $objRow->email;
		}
		$EmailVariab = ["[PatientName]","[WebsiteUrl]","[Website]","[CompanyName]","[CallMode]","[ReSchedLink]","[OrderID]"];
		$EmailValues = [$PatientName,Website,Website,WebsiteTitle,$CallMode,$ReSchedLink,$OrderID];
		$EmailBody   = str_replace($EmailVariab,$EmailValues,$EmailBody);
		return(array($PatientEmail,$EmailSubject,$EmailBody));
	}

	function GetInvestorSignupEmail($FirstName,$EmailCode)
	{
		/* Admin Email */
		$EmailCode = constant("WebsiteUrl")."/investor/register-verify?VerifyCode=".$EmailCode;
		$WebsiteTitle  = constant("WebsiteTitle");
		$Website       = constant("Website");
		$WebsiteUrl    = constant("WebsiteUrl");
		$WebsitePhone  = constant("WebsitePhone");
		$CopyrightYear = constant("CopyrightYear");
		$EmailBody = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=u=tf-8" />
	<title>Email/html</title>
</head>
<body>
<div>
	<table align="center" cellpadding="0" cellspacing="0" style="border: 2px dashed #DE006F;" width="650">
		<tbody>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px dashed #DE006F; padding: 15px;" width="100%">
					<tbody>
						<tr align="center">
							<td>
								<a href="{$Website}" target="_blank">
									<img alt="" border="0" src="{$WebsiteUrl}/images/logo-email.png" width="300px" />
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
								<td style="font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #000000; font-size: 16px;">
									Hi {$FirstName},<br><br>
									We have recieved your details and now we are reviewing it. One of our investment expert will contact you soon to discuss in detail.
									<br>
									Please click on the link below to confirm that we got the right email address.
									<br><br>
									<a href="{$EmailCode}">Verify Email</a>
									<br><br>
									Or copy and paste the link below.
									<br><br>{$EmailCode}<br><br>
									Thank you for creating an account with <a href="{$Website}" target="_BLANK">{$Website} ( {$WebsiteTitle} )</a>.<br><br>
									Kind Regards,<br>
									{$WebsiteTitle}'s Team<br>
									Phone : {$WebsitePhone}<br>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="background: #DE006F; border-top: 2px dashed #dddddd; text-align: center; font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #ffffff; padding: 12px; font-size: 12px; font-weight: normal;">
					Copyright {$Website} {$CopyrightYear}
				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
EOD;
		return($EmailBody);
	}

	function GetInvestorVerifyEmail($FirstName)
	{
		/* Admin Email */
		$EmailLink = constant("WebsiteUrl")."/investor/login";
		$WebsiteTitle  = constant("WebsiteTitle");
		$Website       = constant("Website");
		$WebsiteUrl    = constant("WebsiteUrl");
		$WebsitePhone  = constant("WebsitePhone");
		$CopyrightYear = constant("CopyrightYear");
		$EmailBody = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=u=tf-8" />
	<title>Email/html</title>
</head>
<body>
<div>
	<table align="center" cellpadding="0" cellspacing="0" style="border: 2px dashed #DE006F;" width="650">
		<tbody>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px dashed #DE006F; padding: 15px;" width="100%">
					<tbody>
						<tr align="center">
							<td>
								<a href="{$Website}" target="_blank">
									<img alt="" border="0" src="{$WebsiteUrl}/images/logo-email.png" width="300px" />
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
							<td style="font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #000000; font-size: 16px;">
								Hi {$FirstName},<BR><BR>
								Your Investor Portal is created and ready for you. Your documents and investment information is also uploaded.<br>
								<br>
								Please click on the link below to login to your Prime Medic Investment Portal.
								<br><br>
								<a href="{$EmailLink}">Click Here To Login To Your Investor Portal</a>
								<br><br>
								On behalf of all the team at {$WebsiteTitle} we thank you for your investment and support,<br>
								and please reach out if you have any questions by contacting your Investment Manager or our office.<br><br>
								Kind Regards,<br>
								{$WebsiteTitle}'s Team<br>
								Phone : {$WebsitePhone}
							</td>
						</tr>
					</tbody>
				</table>
				</td>
			</tr>
			<tr>
				<td style="background: #DE006F; border-top: 2px dashed #dddddd; text-align: center; font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif; color: #ffffff; padding: 12px; font-size: 12px; font-weight: normal;">
					Copyright {$Website} {$CopyrightYear}
				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
EOD;
		return($EmailBody);
	}

	function GetInvestorAdminEmail($ClientName,$ContactName,$Mobile,$Phone,$Email,$MysqlErr)
	{
		if ($MysqlErr != "")
			$EmailBody = "New Investor Signup Failed. Contact Investor ASAP"."<BR><BR>";
		else
			$EmailBody = "New Investor Signup Created Successfully in Admin Panel";
		$EmailBody = "".
			"<HTML>".
				"<BODY>".
					"New Investor Signup From Website<BR><BR>".
					"Client Name : ".$ClientName."<BR><BR>".
					"Contact Name : ".$ContactName."<BR><BR>".
					"Phone : ".$Phone."<BR><BR>".
					"Mobile : ".$Mobile."<BR><BR>".
					"Email : ".$Email."<BR><BR>".
					"".$EmailBody.
					"<Error>".
				"</BODY>".
			"</HTML>";
		return($EmailBody);
	}
?>