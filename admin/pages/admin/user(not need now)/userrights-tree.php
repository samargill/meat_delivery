<?php
	$PageID = array(5,2,0);
	$PagePath = "../../";
	$PageMenu = "User Management";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	CheckRight("Edit");

	if (isset($_COOKIE['Room_cboSearch']))
	{
		$cboSearch = $_COOKIE['Room_cboSearch'];
		$txtSearch = $_COOKIE['Room_txtSearch'];
		setcookie("Room_cboSearch","",time() - 24 * 60 * 60 * 2);
		setcookie("Room_txtSearch","",time() - 24 * 60 * 60 * 2);
	}
	else
	{
		if (isset($_REQUEST['cboSearch']))
			$cboSearch = $_REQUEST['cboSearch'];
		else
			$cboSearch = 0;
		if (isset($_REQUEST['txtSearch']))
			$txtSearch = $_REQUEST['txtSearch'];
		else
			$txtSearch = "";
		if (isset($_REQUEST['cboAdminType']))
			$cboAdminType = $_REQUEST['cboAdminType'];
		else
			$cboAdminType = 0;
	}
	if (isset($_REQUEST['btnSaveRights']))
	{
		$Temp = explode(",",$_REQUEST['txtRights']);
		$Rights = array();
		echo("<pre>");
		print_r($Temp);
		echo("</pre>");
		for ($i = 0; $i < count($Temp); $i++)
		{
			$Values = explode("-",$Temp[$i]);
			if (count($Values) > 3)
			{
				$Rights[] = $Temp[$i];
			}
		}
		unset($Temp);
		echo("<pre>");
		print_r($Rights);
		echo("</pre>");
		$Query = "DELETE FROM menurights WHERE adminid = ".$_REQUEST['AdminID'];
		mysqli_query($Conn,$Query);
		$MainIDs = array();
		list($MenuID,$SubID,$SubSubID,$MenuRight) = explode("-","0-0-0-0");
		$SetRight = "0000";
		$i = 0;
		while ($i < count($Rights))
		{
		}
		for ($i = 1; $i < count($Rights); $i++)
		{
			$SetRight[$MenuRight] = 1;

			list($MenuID,$SubID,$SubSubID,$MenuRight) = explode("-",$Rights[$i]);
			$Query = "SELECT mainid FROM menu".
				" WHERE mainid = 0 AND subid = 0 AND subsubid = 0 AND menulink <> ''";
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				
			}
			if (in_array($MenuID,$MainIDs) == false)
			{
				$MainIDs[] = $MenuID;
				$Query = "INSERT INTO menuright".
					" (adminid, menuid, subid, subsubid, rights)".
					" VALUES (".$_REQUEST['AdminID'].", ".$MenuID.", 0, 0, 0)";
				echo "<br><br>".$Query;
			}
			$Query = "INSERT INTO menuright".
				" (adminid, menuid, subid, subsubid, rights)".
				" VALUES (".$_REQUEST['AdminID'].", ".$MenuID.",".$SubID.", ".$SubSubID.", ".$MenuRight.")";
			echo "<br><br>".$Query;
		}
		die;
		header("Location: userview.php?Err=3");
		exit;
	}
	$Json = "".
		"[";
	$Query = "SELECT M.menuid, M.subid, M.subsubid, M.menuname, M.menulink, M.menuright,".
		" CASE WHEN MRS.adminid IS NOT NULL THEN 'true' ELSE 'false' END As CheckStatus".
		" FROM menu M".
		" INNER JOIN menurights MRM".
		" ON M.menuid = MRM.menuid AND M.subid = MRM.subid AND M.subsubid = MRM.subsubid".
		" AND MRM.adminid = ".$_SESSION[constant("SessionID")].
		" LEFT OUTER JOIN menurights MRS".
		" ON M.menuid = MRS.menuid AND M.subid = MRS.subid AND M.subsubid = MRS.subsubid".
		" AND MRS.adminid = ".$_REQUEST["AdminID"].
		" WHERE M.menustatus = 0".
		" AND M.subid = 0 AND M.subsubid = 0".
		" ORDER BY M.menuid, M.subid, M.subsubid";
	$rstRow = mysqli_query($Conn,$Query);
	$i = 0;
	while ($objRow = mysqli_fetch_object($rstRow))
	{
		if (++$i > 1)
		{
			$Json .= ",";
		}
		$MenuID = $objRow->menuid."-".$objRow->subid."-".$objRow->subsubid;
	$Json .= "".
			"{".
				"\"id\": \"".$MenuID."\",".
				"\"text\": \"".$objRow->menuname."\",".
				"\"status\": ".$objRow->CheckStatus;
		if ($objRow->menulink != "")
		{
			$Json .= GetRights($MenuID,$objRow->menuright);
		}
		else if ($objRow->menulink == "")
		{
	$Json .= ",".
				"\"children\":".
				"[";
			$Query = "SELECT M.menuid, M.subid, M.subsubid, M.menuname, M.menulink, M.menuright,".
				" CASE WHEN MRS.adminid IS NOT NULL THEN 'true' ELSE 'false' END As CheckStatus".
				" FROM menu M".
				" INNER JOIN menurights MRM".
				" ON M.menuid = MRM.menuid AND M.subid = MRM.subid AND M.subsubid = MRM.subsubid".
				" AND MRM.adminid = ".$_SESSION[constant("SessionID")].
				" LEFT OUTER JOIN menurights MRS".
				" ON M.menuid = MRS.menuid AND M.subid = MRS.subid AND M.subsubid = MRS.subsubid".
				" AND MRS.adminid = ".$_REQUEST["AdminID"].
				" WHERE M.menuid = ".$objRow->menuid." AND M.menustatus = 0".
				" AND M.subid > 0 AND M.subsubid = 0".
				" ORDER BY M.menuid, M.subid, M.subsubid";
			$rstPro = mysqli_query($Conn,$Query);
			$j = 0;
			while ($objPro = mysqli_fetch_object($rstPro))
			{
				$MenuID = $objPro->menuid."-".$objPro->subid."-".$objPro->subsubid;
				if (++$j > 1)
				{
					$Json .= ",";
				}
	$Json .= "".
					"{".
						"\"id\": \"".$MenuID."\",".
						"\"text\": \"".$objPro->menuname."\",".
						"\"status\": false".
						GetRights($MenuID,$objPro->menuright).
					"}";
			}
	$Json .= "".
				"]";
		}
	$Json .= "".
			"}";
	}
	$Json .= "".
		"]";
	function GetRights($MenuID,$Rights)
	{
		$Json = "";
		$Rights = str_pad(decbin($Rights),4,"0",STR_PAD_LEFT);
		$Json .= ",".
			"\"children\":".
			"[";
		if ($Rights == 0)
		{
			$Json .= "".
				"{".
					"\"id\": \"".$MenuID."-0\",".
					"\"text\": \"User Can View\",".
					"\"status\": false".
				"}";
		}
		else
		{
			$ComaFlag = false;
			for ($i = 3; $i >= 0; $i--)
			{
				if (intval(substr($Rights,$i,1)) > 0)
				{
					if ($i == 3)
						$MenuName = "User Can View";
					else if ($i == 2)
						$MenuName = "User Can Add";
					else if ($i == 1)
						$MenuName = "User Can Edit";
					else if ($i == 0)
						$MenuName = "User Can Delete";
					if ($ComaFlag == true)
					{
						$Json .= ",";
					}
					$Json .= "".
						"{".
							"\"id\": \"".$MenuID."-".$i."\",".
							"\"text\": \"".$MenuName."\",".
							"\"status\": false".
						"}";
					$ComaFlag = true;
				}
			}
		}
		$Json .= "]";
		return($Json);
	}
	//echo $Json; die;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo(WebsiteTitle);?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.5 -->
	<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="../../bootstrap/font-awesome/4.5.0/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="../../bootstrap/ionicons/2.0.1/ionicons.min.css">
	<!-- Bootstrap-Treeview -->
	<link rel="stylesheet" href="../../plugins/bootstrap-treeview/bootstrap-treeview.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="../../dist/css/AdminLTE.css">
	<!-- AdminLTE Skins -->
	<link rel="stylesheet" href="../../dist/css/skins/<?php echo(WebsiteSkin);?>.css">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="../../bootstrap/js/html5shiv.min.js"></script>
		<script src="../../bootstrap/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="../../js/functions.js"></script>
	<script language="javascript">
		function SubmitForm()
		{
			document.Form.submit();
		}
		function EditUser(AdminID)
		{
			window.location = "useredit.php?AdminID=" + AdminID;
		}
		function DeleteUser(AdminID)
		{
			if (AdminID == 1)
			{
				alert("You can't Delete or change Type of this user!")
				return false;
			}
			else
			{
				if (<?php echo($PageRight);?> != 3)
				{
					alert("You Are Not Authorized To Perform This Operation ...");
					return(false);
				}
				if (confirm("Are You Sure You Want To Delete This User ?"))
				{
					window.location = "userview.php?Delete&AdminID="+AdminID;
					return(false);
				}
			}
		}
	</script>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
	<div class="wrapper">
		<?php
			include("../../includes/header.php");
		?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php
			include("../../includes/left.php");
		?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>User</h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">User</li>
				</ol>
			</section>
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
			<!-- Main content -->
			<section class="content">
				<div class="box box-primary">
					<form name="Form" action="userrights.php" method="post">
						<div class="box-body">
							<div class="row">
								<div class="col-md-4">
									<div id="menu-tree"></div>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<div class="col-md-4">
								<input type="hidden" name="AdminID" value="<?php echo($_REQUEST['AdminID']);?>">
								<input type="hidden" name="txtRights" value="">
								<button type="submit" name="btnSaveRights" id="btnSaveRights" class="btn btn-primary">Save User Rights</button>
							</div>
						</div>
					</form>
				</div>
			</section><!-- /.content -->
		</div><!-- /.content-wrapper -->
		<?php
			include("../../includes/footer.php");
		?>
	</div><!-- ./wrapper -->
	<!-- jQuery 3.3.1 -->
	<script src="../../plugins/jQuery/jQuery.min.js"></script>
	<!-- Bootstrap 3.3.5 -->
	<script src="../../bootstrap/js/bootstrap.min.js"></script>
	<!-- Bootstrap-Treeview -->
	<script src="../../plugins/bootstrap-treeview/bootstrap-treeview.min.js"></script>
	<!-- SlimScroll -->
	<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="../../plugins/fastclick/fastclick.min.js"></script>
	<!-- AdminLTE App -->
	<script src="../../dist/js/app.js"></script>
	<!-- page script -->
	<script>
		$(function () {
			var MenuTree = $('#menu-tree').tree({
				primaryKey: 'id',
				uiLibrary: 'bootstrap',
				checkedField: 'status',
				dataSource: <?php echo($Json);?>,
				checkboxes: true
			});
			$('#btnSaveRights').on('click', function () {
				var checkedIds = MenuTree.getCheckedNodes();
				document.Form.txtRights.value = checkedIds;
			});
		});
	</script>
</body>
</html>