<?php
	$PageID = array(array(7,0,0));
	$PagePath = "../../";
	$PageMenu = "User Management";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	
	if (isset($_REQUEST['cboClientType']))
		$cboClientType = $_REQUEST['cboClientType'];
	else
		$cboClientType = 0;
	if (isset($_REQUEST['cboSearch']))
		$cboSearch = $_REQUEST['cboSearch'];
	else
		$cboSearch = 0;
	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";
	if (isset($_REQUEST['ChkDate']))
		$ChkDate = 1;
	else
		$ChkDate = 0;
	if (isset($_REQUEST['txtStartDate']))
		$txtStartDate = $_REQUEST['txtStartDate'];
	else
		$txtStartDate = date("Y-m-d 00:00:00");
	if (isset($_REQUEST['txtCloseDate']))
		$txtCloseDate = $_REQUEST['txtCloseDate'];
	else
		$txtCloseDate = date("Y-m-d 23:59:59");
	if (isset($_REQUEST['Delete']))
	{
		if (isset($_SESSION[SessionID."ClientID"]))
		{
			$Query = "DELETE FROM clientuser".
				" WHERE userid = ".$_REQUEST['UserID']." AND clentid = ".$_SESSION[SessionID."ClientID"];
		}
		else
		{
			$Query = "DELETE FROM adminlogin".
				" WHERE adminid = ".$_REQUEST['UserID']." AND addby = ".$_SESSION[SessionID."Admin"];
			// echo("<br><br>".$Query); die;	
		}
		@mysqli_query($Conn,$Query);
		if (mysqli_errno($Conn) > 0)
		{
			$_SESSION["MysqlErr"] = mysqli_error($Conn);
			header("Location: userview.php?Err=101");
			exit;
		}
		header("Location: userview.php?Err=3");
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
		function SubmitForm()
		{
			document.Form.submit();
		}
		function EditRights(UserID)
		{
			var Win = Popup("userrights?UserID="+UserID,"KS_PrimeMedic_Edit",740,1024,100,100);
			Win.focus();
		}
		function EditUser(UserID)
		{
			var Win = Popup("useradd?UserID="+UserID,"KS_PrimeMedic_Edit",740,1024,100,100);
			Win.focus();
		}
		function DeleteUser(UserID)
		{
			if (UserID == 1)
			{
				ShowError(true,"Error!","You can't Delete or change Type of this user!",undefined,undefined);
				return(false);
			}
			else
			{
				<?php CheckRight("Delete","ShowError");?>
				$.confirm({
					title: "Confirm!",
					content: "Are You Sure You Want To Delete This User ?",
					icon: "fa fa-trash",
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
								window.location = "userview?Delete&UserID="+UserID;
								return(false);
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
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
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
					case 2:
						$Message = "User Updated Successfully ...";
						break;
					case 3:
						$Message = "User Deleted Successfully ...";
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
		?>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<form name="Form" role="form" action="userview" method="post">
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<?php
											$ComboData = array();
											$ComboData[0] = "- Show All -";
											$ComboData[1] = "Name";
										?>
										<label>Search By :</label>
										<select name="cboSearch" CLASS="form-control select2" style="width: 100%;">
											<?php
												for ($i = 0; $i < count($ComboData); $i++)
												{
													if ($cboSearch == $i)
														$ComboSelect = "SELECTED";
													else
														$ComboSelect = "";
											?>
											<option value="<?php echo($i);?>" <?php echo($cboSearch);?> <?php echo($ComboSelect);?>><?php echo($ComboData[$i]);?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group">
										<label>Search Text :</label>
										<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>User Type :</label>
										<?php
											DBCombo("cboClientType","admintype","admintypeid","admintypename",
												"WHERE 1 ORDER BY admintypeid",$cboClientType,"--- Show All ---","form-control select2",
												"onchange=\"\" style=\"width: 100%;\"");
											
										?>
									</div>
									<div class="form-group">
										<label>Last Login By :</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-clock-o"></i>&nbsp;&nbsp;
													<input type="checkbox" name="ChkDate" <?php if ($ChkDate > 0) echo("CHECKED");?>>
												</span>
											</div>
											<input type="text" name="cboDate" id="cboDate" readonly class="form-control pull-right" style="background-color:#FFFFFF;">
											<input type="hidden" name="txtStartDate" value="<?php echo($txtStartDate);?>">
											<input type="hidden" name="txtCloseDate" value="<?php echo($txtCloseDate);?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<button type="submit" name="btnSearch" class="btn btn-primary">Search User</button>
								</div>
							</div>
							<table id="MyDataTable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th width="5%"  style="text-align:left;"  >Sr #</th>
										<th width="15%" style="text-align:left;"  >Type</th>
										<th width="25%" style="text-align:left;"  >Client Name</th>
										<th width="20%" style="text-align:left;"  >User Name</th>
										<th width="10%" style="text-align:left;"  >Mobile</th>
										<th width="10%" style="text-align:left;"  >Last Login</th>
										<th width="7%"  style="text-align:left;"  >Status</th>
										<th width="8%"  style="text-align:center; min-width: 100px;">-</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$Index = 0;
									if (isset($_SESSION[SessionID."ClientID"]))
									{
										$Name = "name";
										$User = "user";
										$AdminTypeID = "clienttypeid";
										$AdminTypeName = "clienttypename";
										$AdminID = "clientid";
										$Table = "clientuser";
										$JoinTable = "clientusertype";
										$SessionID = $_SESSION[SessionID."ClientID"];
									}
									else
									{
										$Name = "firstname";
										$User = "admin";
										$AdminTypeID = "admintypeid";
										$AdminTypeName = "admintypename";
										$AdminID = "adminid";
										$Table = "adminlogin";
										$JoinTable = "admintype";
										$SessionID = $_SESSION[SessionID];
									}
									$QuerySelect= "SELECT C.{$User}id As UserID, C.{$Name} As UserName,".
										" C.email, C.mobile, C.lastlogin, CT.{$AdminTypeName} As UserTypeName,".
										" TIMESTAMPDIFF(MINUTE,C.lastactive,NOW()) As TimeDiff";
									$FromTable	= " FROM {$Table} C";
									$JoinTable	= " INNER JOIN {$JoinTable} CT ON C.{$User}type = CT.{$AdminTypeID}";
									$QueryWhere = " WHERE (C.{$AdminID} = {$SessionID}";
									if (isset($_SESSION[SessionID."Admin"]))
									{
										$QueryWhere .= " OR addby = {$SessionID})";
									}
									else
									{
										$QueryWhere .= ")";	
									}									
									$Query = "".$QuerySelect." ".$FromTable." ".$JoinTable." ".$QueryWhere."";
									if (strlen($txtSearch) > 0)
									{
										if ($cboSearch == 1)
										{
											$Query .= " AND (C.".$Name." LIKE '%".$txtSearch."%')";
										}
									}
									if ($cboClientType > 0)
									{
										$Query .= " AND C.".$User."type = ".$cboClientType."";
									}
									if ($ChkDate > 0)
									{
										$Query .= " AND C.lastlogin BETWEEN '".$txtStartDate."' AND '".$txtCloseDate."'";
									}
									$Query .= " ORDER BY C.".$AdminID." ASC";
									$rstRow = mysqli_query($Conn,$Query);
									$Index = 0;
									while ($objRow = mysqli_fetch_object($rstRow))
									{
										if ($objRow->TimeDiff < 15 && $objRow->TimeDiff != "")
										{
											$LoginStatus =	"<i class=\"fa fa-check-circle text-success\"></i>";
											$LoginTitle	 = "Online";
										}
										elseif ($objRow->TimeDiff >= 15 && $objRow->TimeDiff < 30)
										{
											$LoginStatus =	"<i class=\"fa fa-exclamation-circle text-warning\" style=\"color:#f39c12;\"></i>";
											$LoginTitle	 = "In-Active";
										}
										else
										{
											$LoginStatus =	"<i class=\"fa fa-times-circle text-danger\"></i>";
											$LoginTitle	 = "Offline";
										}
								?>
									<tr>
										<td align="left"  ><?php echo(++$Index);?></td>
										<td align="left"  ><?php echo($objRow->UserTypeName);?></td>
										<td align="left"  ><?php echo(UCString(trim($objRow->UserName)));?></td>
										<td align="left"  ><?php echo($objRow->email);?></td>
										<td align="left"  ><?php echo($objRow->mobile);?></td>
										<td align="left"  ><?php echo(ShowDate($objRow->lastlogin,3));?></td>
										<td align="center"><span data-toggle="tooltip" data-container="body" title="<?php echo $LoginTitle; ?>"><?php echo $LoginStatus; ?></span></td>
										<td align="center">
											<div class="btn-group">
												<button type="button" onclick="EditRights(<?php echo($objRow->UserID);?>);" class="btn bg-purple btn-sm" data-toggle="tooltip" data-container="body" title="User Rights">
													<i class="fa fa-unlock"></i>
												</button>
												<button type="button" onclick="EditUser(<?php echo($objRow->UserID);?>);" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body" title="Edit">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" onclick="DeleteUser(<?php echo($objRow->UserID);?>);" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body" title="Delete">
													<i class="fa fa-trash"></i>
												</button>
											</div>
										</td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
					</form>
				</div>
			</div>	
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function () {
		// Initialize Select2 Elements
		$(".select2").select2();
		// Initialize DataTable
		$('#MyDataTable').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": true,
			"autoWidth": false,
			"iDisplayLength": 50,
			"scrollX": true
		});
	});
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"] = "left";
	$GLOBALS["DateRangePickerVAlign"] = "top";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>