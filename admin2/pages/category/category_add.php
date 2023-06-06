<?php
	$PageID = array(2,0,0);
	$PagePath = "../../";
	$PageMenu = "Categories";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");

	$CategoryID 	= $_REQUEST['CategoryID'] ?? 0;
	$cboAdminType 	= $_REQUEST['cboAdminType'] ?? 0;
	if ($CategoryID == 0)
	{
		CheckRight("Add","Redirect");
		$PageTitle 	= "Add Category";
		$BtnSave 	= "Save Category";
	}
	else
	{
		CheckRight("View","Redirect");
		$PageTitle 	= "Edit Category";
		$BtnSave 	= "Update Category";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
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
					<?php
						$txtName 		= $txtDesc = "";
						$cboParentCat 	= 0;
						$cboStatus 		= -1;
						$Query = "SELECT category_id, name, parent_id, description, icon,".
							" meta_title, meta_desc, meta_keywords, adddate, lastedit, status".
							" FROM product_category".
							" WHERE category_id = ".$CategoryID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow        	= mysqli_fetch_object($rstRow);
							$txtName       	= $objRow->name;
							$txtDesc 		= $objRow->description;
							$cboStatus     	= $objRow->status;
						}
					?>
					<form name="Form" id="Form" role="form" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-form-label">Category Name :</label>
										<input type="text" id="txtName" name="txtName" class="form-control" value="<?php echo($txtName);?>">
									</div>
									<div class="form-group">
										<label class="col-form-label">Parent Category :</label>
										<select name="cboParentCat" id="cboParentCat" class="form-control select2">
											<option value="0">-- Select --</option>
											<?php 
												$Query = "SELECT category_id, name FROM product_category WHERE 1";
												$rstRow = mysqli_query($Conn,$Query);
												while ($objRow = mysqli_fetch_object($rstRow))
												{
											?>
											<option value="<?php echo($objRow->category_id); ?>"><?php echo($objRow->name); ?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>Description :</label>
										<textarea name="txtDesc" id="txtDesc" class="form-control" rows="6"><?php echo($txtDesc);?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<input type="hidden" name="CategoryID" id="CategoryID" value="<?php echo($CategoryID);?>">
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
		$('#txtDesc').summernote({
			height: 200
		});
	});
</script>
</body>
</html>