<?php
	$PagePath = "../../";
	$PageMenu = "Web Pages";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	if (!isset($_REQUEST['BlogID']))
	{
		CheckRight("Add","Redirect");
		$PageTitle = "Add Blog";
		$BlogID = 0;
		$BtnSave = "Save Blog";
	}
	else
	{
		CheckRight("View","Redirect");
		$PageTitle = "Edit BLog";
		$BlogID = $_REQUEST['BlogID'];
		$BtnSave = "Update Blog";
	}
	if (isset($_REQUEST['txtDate']))
	{
		$txtDate = explode("-",$_REQUEST['txtDate']);
		$txtDate = $txtDate[2]."-".$txtDate[1]."-".$txtDate[0];
	}
	if (isset($_REQUEST['cboBlogCat']))
		$cboBlogCat = $_REQUEST['cboBlogCat'];
	else
		$cboBlogCat = 0;
	if (isset($_POST["btnSave"]))
	{
		$Add = false;
		$ErrPos = 0;
		$Query = "SELECT blogid FROM blog".
			" WHERE heading = '".TrimText($_REQUEST["txtHeading"],1)."'";
		if ($BlogID > 0)
		{
			$Query .= " AND blogid <> ".$BlogID;
		}
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow))
		{
			if ($BlogID == 0)
				header("Location: blogadd?Err=103");
			else
				header("Location: blogadd?Err=102&BlogID=".$_REQUEST['BlogID']);
			exit;
		}
		if ($BlogID == 0)
		{
			$Add = true;
			$BlogID = GetMax("blog","blogid");
			$Query = "INSERT INTO blog".
				" (blogid, blogdate, blogcatid, heading, seotitle, seodesc, author, blogtext, blogpage, blogstatus)".
				" VALUES (".$BlogID.", '".$txtDate."', ".sprintf("%d",$_REQUEST['cboBlogCat']).",".
				" '".TrimText($_REQUEST["txtHeading"],1)."', '".TrimText($_REQUEST["txtTitle"],1)."',".
				" '".TrimText($_REQUEST["txtDesc"],1)."', '".TrimText($_REQUEST["txtAuthor"],1)."',".
				" '".TrimText($_REQUEST["txtBlogText"],1)."', '".TrimText($_REQUEST["txtBlogPage"],1)."',".
				"  ".sprintf("%d",$_REQUEST['cboStatus']).")";
		}
		else
		{
			$Query = "UPDATE blog SET".
				"  blogdate     = '".$txtDate."'".
				", blogcatid    =  ".sprintf("%d",$_REQUEST['cboBlogCat']).
				", heading      = '".TrimText($_REQUEST["txtHeading"],1)."'".
				", seotitle     = '".TrimText($_REQUEST["txtTitle"],1)."'".
				", seodesc      = '".TrimText($_REQUEST["txtDesc"],1)."'".
				", author       = '".TrimText($_REQUEST["txtAuthor"],1)."'".
				", blogtext     = '".TrimText($_REQUEST["txtBlogText"],1)."'".
				", blogpage     = '".TrimText($_REQUEST["txtBlogPage"],1)."'".
				", blogstatus   =  ".sprintf("%d",$_REQUEST['cboStatus']).
				"  WHERE blogid =  ".$BlogID;
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
		if (is_uploaded_file($_FILES["txtImgTh"]["tmp_name"]))
		{
			$MyFile = $PagePath."../images/blog/".$BlogID.".jpg";
			move_uploaded_file($_FILES["txtImgTh"]["tmp_name"],$MyFile);
		}
		if (is_uploaded_file($_FILES["txtImgLg"]["tmp_name"]))
		{
			$MyFile = $PagePath."../images/blog/".$BlogID."-detail.jpg";
			move_uploaded_file($_FILES["txtImgLg"]["tmp_name"],$MyFile);
		}
		$Url = "blog-add?Err=".$Err;
		if ($Add == false)
		{
			$Url .= "&BlogID=".$BlogID;
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
	<link rel="stylesheet" href="<?php echo($PagePath);?>plugins/daterangepicker/daterangepicker.css">
	<script language="javascript">
		function Verify()
		{
			if (IsEmpty(document.Form.txtHeading.value) == true) 
			{
				ShowError(true,"Error!","Please Enter Blog Heading ",undefined,"txtHeading");
				return(false);
			}
			if (IsEmpty(document.Form.txtTitle.value) == true || document.Form.txtTitle.value.length > 60) 
			{
				ShowError(true,"Error!","Please Enter SEO Title Max 60 Char",undefined,"txtTitle");
				return(false);
			}
			if (IsEmpty(document.Form.txtDesc.value) == true || document.Form.txtDesc.value.length > 158) 
			{
				ShowError(true,"Error!","Please Enter SEO Description Max 158 Char ",undefined,"txtDesc");
				return(false);
			}
			if (IsEmpty(document.Form.txtAuthor.value) == true) 
			{
				ShowError(true,"Error!","Please Enter Blog Author Name ",undefined,"txtAuthor");
				return(false);
			}
			if (IsEmpty(document.Form.txtBlogText.value) == true) 
			{
				ShowError(true,"Error!","Please Enter Blog Summary ",undefined,"txtBlogText");
				return(false);
			}
			if (IsEmpty(document.Form.txtBlogPage.value) == true) 
			{
				ShowError(true,"Error!","Please Enter Blog Page Text ",undefined,"txtBlogPage");
				return(false);
			}
			if (!$("#BlogID").length)
			{
				if (document.Form.txtImgTh.value == "")
				{
					ShowError(true,"Error!","Please Select Blog Thumbnail JPG File To Upload ...",undefined,"txtImgTh");
					return(false);
				}
				if (CheckFile("Form","txtImgTh","JPG") == false)
				{
					ShowError(true,"Error!","Please Select Blog Thumbnail JPG File To Upload ...",undefined,"txtImgTh");
					return(false);
				}
				if (document.Form.txtImgLg.value == "")
				{
					ShowError(true,"Error!","Please Select Blog Large JPG File To Upload ...",undefined,"txtImgLg");
					return(false);
				}
				if (CheckFile("Form","txtImgLg","JPG") == false)
				{
					ShowError(true,"Error!","Please Select Blog Large JPG File To Upload ...",undefined,"txtImgLg");
					return(false);
				}
			}
		}
		function CountDesc()
		{
			document.Form.txtDescCount.value = document.Form.txtDesc.value.length;
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
						$Message = "Blog Added Successfully ...";
						break;
					case 2:
						$Message = "Blog Updated Successfully ...";
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
						$Message = "Same Blog Code Already Exist ...";
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
						$txtHeading = "";
						$txtTitle = "";
						$txtDesc = "";
						$txtAuthor = "";
						$txtBlogText = "";
						$txtBlogPage = "";
						$txtDate = "";
						$txtDate = "";
						$cboStatus = 1;
						$cboBlogCat = 1;
						$Query = "SELECT heading, seotitle, seodesc, author, blogtext,".
							" blogpage, blogstatus, blogcatid, blogdate".
							" FROM blog WHERE blogid = ".$BlogID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow = mysqli_fetch_object($rstRow);
							$txtHeading    = $objRow->heading;
							$txtTitle      = $objRow->seotitle;
							$txtDesc       = $objRow->seodesc;
							$txtAuthor     = $objRow->author;
							$txtBlogText   = $objRow->blogtext;
							$txtBlogPage   = $objRow->blogpage;
							$cboStatus     = $objRow->blogstatus;
							$cboBlogCat    = $objRow->blogcatid;
							if ($objRow->blogdate != NULL)
							{
								$txtDate = explode("-",$objRow->blogdate);
								$txtDate = $txtDate[2]."/".$txtDate[1]."/".$txtDate[0];
							}
						}
					?> 
					<form name="Form" role="form" action="blog-add" method="post" onsubmit="return Verify();" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Blog Heading :</label>
										<input type="text" name="txtHeading" value="<?php echo($txtHeading);?>" class="form-control" maxlength="100">
									</div>
									<div class="form-group">
										<label>SEO Title [60 Char] :</label>
										<input type="text" name="txtTitle" value="<?php echo($txtTitle);?>" class="form-control" maxlength="60">
									</div>
									<div class="form-group">
										<label>SEO Description [158 Char] :</label>
										<textarea type="text" name="txtDesc" id="txtDesc" rows="4" class="form-control"><?php echo($txtDesc);?></textarea>
										<input type="text" name="txtDescCount" id="txtDescCount"  value="<?php echo(strlen($txtDesc));?>" class="form-control" readonly>
									</div>
									<div class="form-group">
										<label>Author :</label>
										<input type="text" name="txtAuthor" value="<?php echo($txtAuthor);?>" class="form-control" maxlength="100">
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-7">
											<div class="form-group">
												<label for="cboBlogCat">Blog Category :</label><br>
												<?php DBCombo("cboBlogCat","blogcategory","blogcatid","blogcatname","",$cboBlogCat,"","form-control select2","TABINDEX=\"2\" STYLE=\"width:100%;\"");?>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<?php
													$ComboData = array();
													$ComboData[0] = "Disabled";
													$ComboData[1] = "Enabled";
												?>
												<label for="cboStatus">Status :</label><br>
												<select name="cboStatus" id="cboStatus" CLASS="form-control select2" style="width: 100%;">
													<?php
														for ($i = 0; $i < count($ComboData); $i++)
														{
															if ($cboStatus == $i)
																$ComboSelect = "SELECTED";
															else
																$ComboSelect = "";
													?>
													<option value="<?php echo($i);?>" <?php echo($ComboSelect);?>><?php echo($ComboData[$i]);?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="row"> 
										<div id="RowDOB" class="col-sm-6">
											<div class="input text">
												<label for="txtDate">Date Of Blog</label>
												<input type="text" name="txtDate" id="txtDate" value="<?php echo($txtDate);?>" class="form-control datepicker" placeholder="dd/mm/yyyy" />
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for="txtImgTh">Upload Blog Thumbnail [ 370 x 246 px ] :</label>
												<input type="file" name="txtImgTh" id="txtImgTh" accept="image/x-png">
											</div>
										</div>
									</div>
									<div class="row"> 
										<div class="col-sm-12">
											<div class="form-group">
												<img src="<?php echo($PagePath);?>../images/blog/<?php echo($BlogID);?>.jpg" height="200px">
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for="txtImgLg">Upload Blog Large Image [ 700 x 467 px ] :</label>
												<input type="file" name="txtImgLg" id="txtImgLg" accept="image/x-png">
											</div>
										</div>
									</div>
									<div class="row"> 
										<div class="col-sm-12">
											<div class="form-group">
												<img src="<?php echo($PagePath);?>../images/blog/<?php echo($BlogID);?>-detail.jpg" height="200px">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12" >
									<div class="form-group">
										<label>Blog Summary :</label>
										<textarea type="text" name="txtBlogText" rows="3" class="form-control" ><?php echo($txtBlogText);?></textarea>
									</div>
									<div class="form-group">
										<label>Blog Page :</label>
										<textarea type="text" name="txtBlogPage" rows="8" class="form-control" ><?php echo($txtBlogPage);?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<?php
								if ($BlogID > 0)
								{
							?>
							<input type="hidden" name="BlogID" id="BlogID" value="<?php echo($BlogID);?>">
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
</div><!-- ./wrapper -->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!-- Page Script -->
<script src="<?php echo($PagePath);?>plugins/daterangepicker/daterangepicker.js"></script>
<script>
	$(function () {
		$(".select2").select2();
		$('#txtDate').daterangepicker({
			singleDatePicker: true,
			locale: {
		      format: 'DD-MM-YYYY'
		    }
		});
		ScrollToContent("PageContent");
	});
	$('#txtDesc').keyup(function () {
		document.Form.txtDescCount.value = document.Form.txtDesc.value.length;
	});
</script>
</body>
</html>