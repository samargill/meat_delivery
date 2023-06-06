<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (isset($_POST['PkgID']))
	{
		$Response = "";
		$PkgID = $_POST['PkgID'];
		$CountryID = $_POST['CountryID'];
		$Response .= <<<EOD
		<div class="col-md-12">
			<table id="MyDataTable" class="table table-striped table-hover table-responsive" style="width:100%;">
				<thead class="table-bordered">
					<tr style="background:#001f3f;">
						<th width="5%"  style="text-align:left;color: #fff;">Sr #</th>
EOD;		
						$HeadText = "Cost Per SMS";
						if ($PkgID == 6)
						{
		$Response .= <<<EOD
						<th width="20%" style="text-align:left;color: #fff;">Transaction Amount</th>
EOD;
						}
						if ($PkgID == 7)
						{
							$HeadText = "Cost Per Feature";
		$Response .= <<<EOD
						<th width="20%"  style="text-align:left;color: #fff;">Package Feature</th>
EOD;
						}
						if ($PkgID == 6 || $PkgID == 7)
						{
							$Width1 = $Width2 = $Width3 = "25%";
						}
						elseif ($PkgID == 8)
						{
							$Width1 = "31%";
							$Width2 = $Width3 = "32%";
						}	
		$Response .= <<<EOD
						<th width="{$Width1}" style="text-align:center;color: #fff;">{$HeadText}</th>
						<th width="{$Width2}" style="text-align:center;color: #fff;">Sms Send Option</th>
						<th width="{$Width3}" style="text-align:center;color: #fff;">Sms Receive Option</th>
					</tr>
				</thead>
				<tbody class="table-bordered">
EOD;
				$Index = $Flag = 0;
				$SimCharges = sprintf("%0.2f", 0.00);
				$Query = "SELECT P.pkgid, P.pkgname,".
					" PR.pkg_field_id, PR.pkg_field_value, PR.minsmsqty, PF.pkg_field_text, AC.countryname,".
					" (SELECT (minsmsqty - 1) FROM package_rate WHERE minsmsqty > PR.minsmsqty LIMIT 1 ) AS MinSmsQty".
					" FROM package P".
					" INNER JOIN package_rate PR ON P.pkgid = PR.pkgid".
					" INNER JOIN package_field PF ON PR.pkg_field_id = PF.pkg_field_id".
					" INNER JOIN address_country AC ON PR.countryid = AC.countryid".
					" WHERE P.status = 1".
					" AND P.pkgid =".$PkgID." AND PR.countryid =".$CountryID." AND PR.pkg_field_id != 13 AND PR.status = 1".
					" ORDER BY PR.pkg_field_value DESC";
				$rstRow = mysqli_query($Conn,$Query);
				while ($objRow  = mysqli_fetch_object($rstRow))
				{
					$FeatPrice	= 0.00;
					$BgClr		= ($Index % 2 == 0) ? "#fceee8" : "#fae6de"; 
					$MsgSend	= "fa fa-check";
					$MsgRecv	= "fa fa-times";
					$RecvClr	= "red";
					$SendSmsPrice = $objRow->pkg_field_value;
					if ($PkgID == 6)
					{
						$MinSmsQty = $objRow->minsmsqty;
						$MaxSmsQty = $objRow->MinSmsQty != null? $objRow->MinSmsQty: 20000;
						$SmsQty	   = $MinSmsQty." - ".$MaxSmsQty;
					}
					if ($PkgID == 7)
					{
						if ($Index == 0 && $Flag == 0)
						{	
							$Flag++;
							continue;
						}
						// Check Sim Expiry if User Already Exist
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
						$MsgRecv = "fa fa-check";
						$RecvClr = "green";
						$PkgFieldText = $objRow->pkg_field_text;
					}
					$Index++;
		$Response .= <<<EOD
					<tr id="Row{$Index}" style="background: {$BgClr} ;">
						<td align="left">{$Index}</td>
EOD;
					if ($PkgID == 6)
					{
		$Response .= <<<EOD
						<td align="left">{$SmsQty}</td>
EOD;
					}
					if ($PkgID == 7)
					{
						$SimDays = sprintf("%0.2f",GetValue("pkg_field_value","package_rate","pkg_field_id = 10"));
						$SimDays = explode(".",$SimDays);
						$SimDays = $SimDays[0];
						if ($Index == 1)
						{
							$SendSmsPrice = $SendSmsPrice." / ".$SimDays." days";
						}
		$Response .= <<<EOD
						<td align="left"  >{$PkgFieldText}</td>
EOD;				
					}		
		$Response .= <<<EOD
						<td align="center">{$SendSmsPrice}</td>
						<td align="center"><i class="{$MsgSend}" style="color: green;"></i></td>
						<td align="center"><i class="{$MsgRecv}" style="color: {$RecvClr}"></i></td>
					</tr>
EOD;
				}
				$UserCurPkg = GetValue("pkgtype", "client", "clientid = ".$_SESSION[SessionID."ClientID"]);
				if ($UserCurPkg == $PkgID)
				{
					$BtnName 	= "Topup";
					$BtnStatus	= "";
				}
				else
				{	
					$BtnName   = "Buy Credits";
					$BtnStatus = "hidden";
					$TabName   = $_POST['TabName'];
					if ($UserCurPkg != 7 && $UserCurPkg != 9 && $TabName == "Dedicated")
					{
						$BtnStatus	= "";
						$BtnName   = "Upgrade Dedicated Package";
					}	
					if ($UserCurPkg == 9)
					{
						$BtnStatus	= "";
					}
				}
		$Response .= <<<EOD
				</tbody>
			</table>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<button type="button" class="btn btn-primary btn-block" onclick="TakePayment({$PkgID},{$SimCharges});" {$BtnStatus}>{$BtnName}</button>
			</div>
		</div>
EOD;		
	echo($Response);
	die();
	}
?>