<?php
	$PagePath = "../../";
	$PageMenu = "Web Pages";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (!isset($_REQUEST['BlogCatID']))
	{
		CheckRight("Add","Redirect");
		$PageTitle = "Add Blog Category";
		$BlogCatID = 0;
		$BtnSave = "Save Category";
	}
	else
	{
		CheckRight("View","Redirect");
		$PageTitle = "Edit BLog";
		$BlogID = $_REQUEST['BlogCatID'];
		$BtnSave = "Update Category";
	}
	if (isset($_POST["btnSave"]))
	{
		$Add = false;
		$ErrPos = 0;
		$Query = "SELECT blogcatid FROM blogcategory".
			" WHERE blogcatname = '".TrimText($_REQUEST["txtCategory"],1)."'";
		if ($BlogCatID > 0)
		{
			$Query .= " AND blogcatid <> ".$BlogCatID;
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow))
		{
			if ($BlogCatID == 0)
				header("Location: blog-cat-add?Err=103");
			else
				header("Location: blog-cat-add?Err=102&BlogCatID=".$_REQUEST['BlogCatID']);
			exit;
		}
		if ($BlogCatID == 0)
		{
			$Add = true;
			$BlogCatID = GetMax("blogcategory","blogcatid");
			$Query = "INSERT INTO blogcategory".
				" (blogcatid, blogcatname, status)".
				" VALUES (".$BlogCatID.", '".TrimText($_REQUEST["txtCategory"],1)."',".sprintf("%d",$_REQUEST['cboStatus']).")";
		}
		else
		{
			$Query = "UPDATE blogcategory SET".
				" blogcatname   = '".TrimText($_REQUEST["txtCategory"],1)."'".
				", status       =  ".sprintf("%d",$_REQUEST['cboStatus']).
				"  WHERE blogcatid =  ".$BlogCatID;
		}
		if (!mysqli_query($Conn,$Query))
		{
			if ($Add == true)
				$Err = 101;
			else
				$Err = 102;
			$ErrPos = 1;
			$_SESSION['MysqlErr'] = mysqli_error($Conn);
		}
		else
		{
			if ($Add == true)
				$Err = 1;
			else
				$Err = 2;
		}
		$Url = "blog-cat-add?Err=".$Err;
		if ($Add == false)
		{
			$Url .= "&BlogCatID=".$BlogCatID;
		}
		if ($ErrPos > 0)
		{
			$Url .= "&ErrPos=".$ErrPos;
		}
		else
		{
			unset($_SESSION['PageVars']);
		}
		header("Location: ".$Url);
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
			if (IsEmpty(document.Form.txtCategory.value) == true) 
			{
				ShowError(true,"Error!","Please Enter Category Name ",undefined,"txtCategory");
				return(false);
			}
		}
	</script>
</head>
<body>
<div class="wrapper">
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper" style="margin-left: 0px;">
		<!-- Content Header (Page header) -->
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
		<!-- Page Error -->
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
						$Message = "Blog Category Added Successfully ...";
						break;
					case 2:
						$Message = "Blog Category Updated Successfully ...";
						break;
					case 101:
					case 102:
						$Message = "Unable To ".($_REQUEST['Err'] == 101 ? "Add" : "Edit")." Blog - Fatal Error ...";
						if (isset($_SESSION["MysqlErr"]))
						{
							$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
							unset($_SESSION["MysqlErr"]);
						}
						break;
					case 103:
						$Message = "Same Blog Category Already Exist ...";
						break;
					case 104:
						$Message = "Same Blog Name Already Exist ...";
						break;
				}
				if ($_REQUEST['Err'] < 100)
				{
					$MessageHead = "Note:";
					$MessageBG   = "success";
					$MessageIcon = "fa-check";
				}
		?>
		<div style="padding-left: 15px; padding-right: 15px;">
			<div class="alert alert-<?php echo($MessageBG);?> alert-dismissible">
				<h5><i class="icon fas <?php echo($MessageIcon);?>"></i><?php echo($MessageHead);?></h5>
				<?php echo($Message);?>
			</div>
		</div>
		<?php
			}
		?>
		<!-- Main Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="card card-outline card-primary">
					<?php
						$txtCategory = "";
						$cboStatus = 1;
						$Query = "SELECT blogcatname, status".
							" FROM blogcategory WHERE blogcatid = ".$BlogCatID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow = mysqli_fetch_object($rstRow);
							$txtCategory = $objRow->blogcatname;
							$cboStatus      = $objRow->status;
						}
					?> 
					<form name="Form" role="form" action="blog-cat-add" method="post" onsubmit="return Verify();" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Category Name :</label>
										<input type="text" name="txtCategory" value="<?php echo($txtCategory);?>" class="form-control" maxlength="100">
									</div>
									<div class="form-group">
										<label for="cboStatus">Status :</label><br>
										<select name="cboStatus" id="cboStatus" CLASS="form-control select2" style="width: 100%;">
											<option value="1" <?php if ($cboStatus == 1) echo("Selected");?>>Enable</option>
											<option value="2" <?php if ($cboStatus == 2) echo("Selected");?>>Disable</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<?php
								if ($BlogCatID > 0)
								{
							?>
							<input type="hidden" name="BlogCatID" id="BlogCatID" value="<?php echo($BlogCatID);?>">
							<?php
								}
							?>
							<button type="submit" name="btnSave" class="btn btn-primary"><?php echo($BtnSave);?></button>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script>
	$(function () {
		$(".select2").select2();
	});
</script>
</body>
</html>