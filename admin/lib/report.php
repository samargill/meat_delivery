<?php
	function GetReportHeader($State="NSW")
	{
		$CompanyName 	= WebsiteTitle;
		$CompanyPhone 	= WebsitePhone;
		$CompanyFax     = GetValue("config_value","websettings","config_id = 3");
		$CompanyEmail 	= WebsiteEmail;
		$CompanyWebsite = Website;
		$CompanyAddress = GetStateAddress($State);
		$FontSml = "style=\"font-family: Tahoma; font-size: 9px; font-weight: bold;\"";
		$Header = <<<EOD
		<tr>
			<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" rowspan="4"><img src="../../dist/img/logo-lrg.png" height="55px"></td>
					<td width="2%" rowspan="4"></td>
					<td width="68%" style="font-family: Times; font-size: 12px; font-weight: bold;">{$CompanyName}</td>
				</tr>
				<tr>
					<td {$FontSml}>{$CompanyAddress}</td>
				</tr>
				<tr>
					<td {$FontSml}>Ph : {$CompanyPhone} &nbsp; &nbsp; &nbsp; Fax : {$CompanyFax}</td>
				</tr>
				<tr>
					<td {$FontSml}>Email : {$CompanyEmail} &nbsp; &nbsp; &nbsp; Website : {$CompanyWebsite}</td>
				</tr>
				<tr>
					<td align="center" height="5px" style="font-size: 5px;"></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td align="center"><hr /></td>
		</tr>
EOD;
		return($Header);
	}
?>