<?php
	$PageID = array(5,5,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	$PageName = "Blog";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	if (isset($_REQUEST['txtSearch']))
		$txtSearch = $_REQUEST['txtSearch'];
	else
		$txtSearch = "";
	if (isset($_REQUEST['cboCategory']))
		$cboCategory = $_REQUEST['cboCategory'];
	else
		$cboCategory = 0;
	if (isset($_POST["btnSubmit"]))
	{
		if ($_POST["btnSubmit"] == "Delete")
		{
			$Query = "SELECT faqid FROM faqs WHERE faqid = ".$_REQUEST['FaqID'];
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow))
			{
				header("Location: faqview?Err=102&FaqID=".$_REQUEST['FaqID']);
				exit;
			}
			$Query = "DELETE FROM faqs WHERE faqid = ".$_POST['FaqID'];
			@mysqli_query($Conn,$Query);
			if (count(mysqli_error_list($Conn)) > 0)
			{
				$_SESSION["MysqlErr"] = mysqli_error($Conn);
				header("Location: faq-view?Err=101");
				exit;
			}
			header("Location: blog-view?Err=3&txtSearch=".$txtSearch);
			exit;
		}
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
		}
		function AddRecord()
		{
			var Win = Popup("blog-add","KS_ArtMarketGallery_Edit",740,1024,100,100);
			Win.focus();
		}
		function AddBlogCat()
		{
			var Win = Popup("blog-cat-add","KS_ArtMarketGallery_Edit",740,1024,100,100);
			Win.focus();
		}
		function EditRecord(BlogID)
		{
			var Win = Popup("blog-add?BlogID="+BlogID,"KS_ArtMarketGallery_Edit",740,1024,100,100);
			Win.focus();
		}
		function DeleteRecord(BlogID,Index)
		{
			$.confirm({
				title: "Delete!",
				content: "Are You Sure You Want To Delete This Blog ?",
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
							var RemoveRow = false;
							$.confirm({
								content: function () {
									var self = this;
									return $.ajax({
										url: "<?php echo($PagePath) ?>pages/ajaxs/setting/blog-del",
										dataType: "JSON",
										method: "POST",
										data: {
											"DelBlog": "",
											"BlogID": BlogID
										}
										}).done(function (response) {
											self.setTitle(response.Status);
											self.setContent(response.Message);
											if (response.Status == "Done")
											{
												RemoveRow = true;
											}
										}).fail(function(){
											self.setTitle("Error!");
											self.setContent('Error Completing Operation. Please Try Again ...');
										});
								},
								buttons: {
									"OK": {
										text: "OK",
										btnClass: "btn-blue"
									}
								},
								onClose: function () {
									if (RemoveRow)
									{
										$("#DataRow"+Index).hide();
									}
								}
							});
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
			<!-- Page Header -->
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1><?php echo("View ".$PageName)?></h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
								<li class="breadcrumb-item active"><?php echo("View ".$PageName)?></li>
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
						case 3:
							$Message = "Blog Deleted Successfully ...";
							break;
						case 101:
							$Message = "Unable To Perform Operation - Fatal Error ...";
							if (isset($_SESSION["MysqlErr"]))
							{
								$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
								unset($_SESSION["MysqlErr"]);
							}
							break;
						case 102:
							$Message = "Blog Cannot Be Deleted - Speciality Have Some Bookings ...";
							break;					}
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
						<form name="Form" role="form" action="blog-view" method="post" onsubmit="return Verify();">
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Search Text :</label>
											<input type="text" name="txtSearch" value="<?php echo($txtSearch);?>" class="form-control">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<?php
												$Query = "SELECT blogcatid, blogcatname FROM blogcategory ORDER BY blogcatid";
												$rstRow = mysqli_query($Conn,$Query);
											?>
											<label>Blog Category :</label>
											<div class="md-select md-input">
												<select name="cboCategory" id="<cboCategory></cboCategory>" class="form-control select2">
													<option value="0">-- Show All --</option>
													<?php
														while ($objRow = mysqli_fetch_object($rstRow))
														{
															if ($cboCategory == $objRow->blogcatid)
																$ComboSelect = "SELECTED";
															else
																$ComboSelect = "";
													?>
													<option value="<?php echo($objRow->blogcatid);?>" <?php echo($ComboSelect);?>><?php echo($objRow->blogcatname);?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<button type="submit" name="btnSearch" class="btn btn-primary">Search Blog</button>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<button type="button" name="btnAdd" class="btn btn-primary" style="margin-right: 10px;" onclick="AddRecord();">Add New Blog</button>
											<button type="button" name="btnAdd" class="btn btn-primary" onclick="AddBlogCat();">Add New Blog Category</button>
										</div>
									</div>
								</div>
								<table id="MyDataTable" class="table table-bordered table-hover" style="min-width:800px;">
									<thead>
										<tr>
											<th width="5%"  style="text-align:center;">Sr #</th>
											<th width="9%"  style="text-align:left;"  >Date</th>
											<th width="20%" style="text-align:left;"  >Category</th>
											<th width="58%" style="text-align:left;"  >Heading</th>
											<th width="8%"  style="text-align:center; min-width: 80px;">-</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$Index = 0;
										$Query = "SELECT B.blogid, B.blogdate, BC.blogcatname, B.heading".
											" FROM blog B INNER JOIN blogcategory BC ON B.blogcatid = BC.blogcatid".
											" WHERE 1 ";
										if (strlen($txtSearch) > 0)
										{
											$Query .= " AND B.heading LIKE '%".$txtSearch."%'";
										}
										if($cboCategory > 0)
										{
											$Query .= " AND B.blogcatid = $cboCategory";
										}
										$Query .= " ORDER BY B.blogid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											$Index++;
									?>
										<tr id="DataRow<?php echo($Index);?>">
											<td align="right" ><?php echo($Index);?></td>
											<td align="left"  ><?php echo(ShowDate($objRow->blogdate,0));?></td>
											<td align="left"  ><?php echo($objRow->blogcatname);?></td>
											<td align="left"  ><?php echo($objRow->heading);?></td>
											<td align="center">
												<div class="btn-group">
													<button type="button" class="btn btn-info btn-sm" title="Edit" onclick="EditRecord(<?php echo($objRow->blogid);?>);" data-toggle="tooltip" data-container="body">
														<i class="fa fa-edit"></i>
													</button>
													<button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="DeleteRecord(<?php echo($objRow->blogid);?>,<?php echo($Index);?>);" data-toggle="tooltip" data-container="body">
														<i class="fa fa-trash"></i>
													</button>
												</div><!-- /.btn-group -->
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
			</section>
		</div>
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
			$('#MyDataTable').DataTable({
				"paging": true,
				"lengthChange": false,
				"searching": false,
				"ordering": false,
				"info": true,
				"autoWidth": true,
				"iDisplayLength": 50,
				"scrollX": true
			});
		});
		$(function() {
			$(".select2").select2();
		});
	</script>
</body>
</html>