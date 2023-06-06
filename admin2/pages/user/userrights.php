<?php
	$PageID = array(array(5,2,0),array(10,2,0));
	$PagePath = "../../";
	$PageTitle = "User Rights View";
	$PageMenu = "User Management";
	$DebugMode = false;
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");

	if (isset($_SESSION[SessionID."ClientID"]))
	{
		$SessionID = $_SESSION[SessionID."ClientID"];
		$Table = "client";
	}
	else
	{
		$SessionID = $_SESSION[SessionID."Admin"];
		$Table = "admin";
	}

	if (isset($_REQUEST['btnSaveRights']))
	{
		if ($DebugMode)
		{
			echo("<pre>");
			print_r($_REQUEST['ChkRight']);
			echo("</pre>");
		}
		$Query = "DELETE FROM ".$Table."menurights WHERE adminid = ".$_REQUEST['UserID'];
		if ($DebugMode)
		{
			echo("<br><br>".$Query);
		}
		else
		{
			mysqli_query($Conn,$Query);
		}
		$MenuRight = "0000";
		$MainIDs = array();
		$MenuSave = false;
		for ($i = 0; $i < count($_REQUEST['ChkRight']); $i++)
		{
			list($MenuID,$SubID,$SubSubID,$Right) = explode("-",$_REQUEST['ChkRight'][$i]);
			$MenuData = $MenuID."-".$SubID."-".$SubSubID;
			$MenuRight[$Right] = "1";
			if ($i + 1 == count($_REQUEST['ChkRight']))
			{
				$MenuSave = true;
			}
			else
			{
				list($MenuID,$SubID,$SubSubID,$Right) = explode("-",$_REQUEST['ChkRight'][$i+1]);
				$NextMenuData = $MenuID."-".$SubID."-".$SubSubID;
				if ($NextMenuData != $MenuData)
				{
					list($MenuID,$SubID,$SubSubID,$Right) = explode("-",$_REQUEST['ChkRight'][$i]);
					$MenuSave = true;
				}
			}
			if ($MenuSave)
			{
				if ($SubID > 0)
				{
					if (in_array($MenuID,$MainIDs) == false)
					{
						$MainIDs[] = $MenuID;
						$Query = "INSERT INTO ".$Table."menurights".
							" (adminid, menuid, subid, subsubid, rights)".
							" VALUES (".$_REQUEST['UserID'].", ".$MenuID.", 0, 0, 0)";
						if ($DebugMode)
						{
							echo "<br><br>".$Query;
						}
						else
						{
							mysqli_query($Conn,$Query);
						}
					}
				}
				$Query = "INSERT INTO ".$Table."menurights".
					" (adminid, menuid, subid, subsubid, rights)".
					" VALUES (".$_REQUEST['UserID'].", ".$MenuID.",".$SubID.", ".$SubSubID.", ".bindec($MenuRight).")";
				if ($DebugMode)
				{
					echo "<br><br>".$Query;
				}
				else
				{
					mysqli_query($Conn,$Query);
				}
				$MenuSave = false;
				$MenuRight = "0000";
			}
		}
		header("Location: userrights?Err=3&UserID=".$_REQUEST['UserID']);
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
							<h1><?php echo($PageTitle)?></h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
								<li class="breadcrumb-item active"><?php echo($PageTitle)?></li>
							</ol>
						</div>
					</div>
				</div>
			</section>
			<!-- Main Content -->
			<section class="content">
				<div class="container-fluid">
					<div class="card card-outline card-primary">
						<div class="card-body">
							<!-- Page Error -->
							<?php
								if (isset($_REQUEST['Err']))
								{
									$Message = "";
									$MessageBG = "callout-danger lead";
									$MessageHead = "Error:";
									$MessageIcon = "fa-exclamation-circle";
									switch ($_REQUEST['Err'])
									{
										case 3:
											$Message = "User Rights Updated Successfully ...";
											break;
										case 101:
											$Message = "Unable To Delete User - Fatal Error ...";
											if (isset($_SESSION["MysqlErr"]))
											{
												$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
												unset($_SESSION["MysqlErr"]);
											}
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
							<form name="Form" action="userrights" method="post">
								<table id="MyDataTable" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th width="8%"  style="text-align:left;"  >Sr #</th>
											<th width="72%" style="text-align:left;"  >Menu Link</th>
											<th width="5%"  style="text-align:center;">View</th>
											<th width="5%"  style="text-align:center;">Add</th>
											<th width="5%"  style="text-align:center;">Edit</th>
											<th width="5%"  style="text-align:center;">Delete</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$RightName = array("Delete","Edit","Add","View");
										$Query = "SELECT M.menuid, M.subid, M.subsubid, M.menuname,".
											" M.menulink, M.menuright, MRS.rights".
											" FROM ".$Table."menu M".
											" INNER JOIN ".$Table."menurights MRM".
											" ON M.menuid = MRM.menuid AND M.subid = MRM.subid AND M.subsubid = MRM.subsubid".
											" AND MRM.adminid = ".$SessionID.
											" LEFT OUTER JOIN ".$Table."menurights MRS".
											" ON M.menuid = MRS.menuid AND M.subid = MRS.subid AND M.subsubid = MRS.subsubid".
											" AND MRS.adminid = ".$_REQUEST["UserID"].
											" WHERE M.menustatus = 0".
											" ORDER BY M.menuid, M.subid, M.subsubid";
										$rstRow = mysqli_query($Conn,$Query);
										$Index = 0;
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$MenuID = $objRow->menuid."-".$objRow->subid."-".$objRow->subsubid;
											$Rights = "    ";
											$RightsSet = "0000";
											if ($objRow->menulink != "")
											{
												$Rights = str_pad(decbin($objRow->menuright),4,"0",STR_PAD_LEFT);
												$RightsSet = str_pad(decbin($objRow->rights),4,"0",STR_PAD_LEFT);
											}
									?>
										<tr>
											<td align="left"  ><?php echo(++$Index);?></td>
											<td align="left"  >
												<?php
													$StyleClass = "font-weight-bold text-danger";
													if ($objRow->subid > 0)
													{
														$StyleClass = "font-weight-normal text-navy ml-4";
													}
												?>	
												<span class="<?php echo($StyleClass);?>" >
													<?php echo($objRow->menuname);?>
												</span>
											</td>
											<?php

												for ($i = 3; $i >= 0; $i--)
												{
													$ChkRight = "";
													if ($Rights[$i] == 1)
													{
														$ChkRight = $RightName[$i];
													}
											?>
											<td align="center">
												<?php
													if ($ChkRight != "")
													{
												?>
												<input type="checkbox" name="ChkRight[]" value="<?php echo($MenuID."-".$i);?>" title="<?php echo($ChkRight);?>" <?php if ($RightsSet[$i] == 1) echo("CHECKED");?> value="0">
												<?php
													}
												?>
											</td>
											<?php
												}
											?>
										</tr>
									<?php
										}
									?>
									</tbody>
								</table>
								<div class="card-footer">
									<div class="col-md-4">
										<input type="hidden" name="UserID" value="<?php echo($_REQUEST['UserID']);?>">
										<input type="hidden" name="txtRights" value="">
										<button type="submit" name="btnSaveRights" id="btnSaveRights" class="btn btn-primary" onclick="return Verify();">Save User Rights</button>
									</div>
								</div>
							</form>
						<?php
							}
						?>
						</div>
					</div>	
				</div>
			</section><!-- /.content -->
		</div><!-- /.content-wrapper -->
	</div><!-- ./wrapper -->
	<?php
		include($PagePath."includes/inc-js.php");
	?>
	<!-- Page Script -->
	<script>
		$(function () {
		});
	</script>
</body>
</html>