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
						$txtName 	= $txtTitle = $txtKeywords = $txtDesc = $txtPhoto = "";
						$cboParent 	= 0;
						$cboStatus 	= 1;
						$Query = "SELECT category_id, name, parent_id, photo,".
							" meta_title, meta_desc, meta_keywords, adddate, lastedit, status".
							" FROM product_category".
							" WHERE category_id = ".$CategoryID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow        	= mysqli_fetch_object($rstRow);
							$txtName       	= $objRow->name;
							$txtTitle 		= $objRow->meta_title;
							$txtDesc 		= $objRow->meta_desc;
							$txtKeywords 	= $objRow->meta_keywords;
							$cboStatus      = $objRow->status;
							$txtPhoto 		= $objRow->photo;
						}
					?>
					<form name="Form" id="Form" role="form" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Category Name :</label>
										<input type="text" id="txtName" name="txtName" class="form-control" value="<?php echo($txtName);?>">
									</div>
									<div class="form-group">
										<label>Parent Category :</label>
										<select name="cboParent" id="cboParent" class="form-control select2">
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
									<div class="form-group">
										<label>SEO Title [60 Char] :</label>
										<input type="text" id="txtTitle" name="txtTitle" class="form-control" maxlength="60" value="<?php echo($txtTitle);?>">
									</div>
									<div class="form-group">
										<label class="col-form-label">SEO Keywords :</label>
										<input type="text" id="txtKeywords" name="txtKeywords" class="form-control" value="<?php echo($txtKeywords);?>">
									</div>
									<div class="form-group">
										<label>SEO Description [158 Char] :</label>
										<textarea type="text" name="txtDesc" id="txtDesc" rows="4" class="form-control"><?php echo($txtDesc);?></textarea>
										<input type="text" name="txtDescCount" id="txtDescCount"  value="<?php echo(strlen($txtDesc));?>" class="form-control" readonly>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Status :</label>
										<?php
											$ComboData = array();
											$ComboData[0] = "Disabled";
											$ComboData[1] = "Enabled";
										?>
										<?php
											DBComboArray("cboStatus",$ComboData,0,$cboStatus,"form-control select2","");
										?>
									</div>
									<div class="row" style="line-height: 2;">
										<div class="col-md-8">
											<label>Upload Thumbnail [ 80 x 80 px ] :</label>
											<input type="file" name="txtPhoto" id="txtPhoto" value="<?php echo($PagePath);?>../assets/images/category/<?php echo($txtPhoto);?>" accept=".png">
										</div>
										<div class="col-md-4">
											<img class="img-fluid" id="txtPhotoImg" name="txtPhotoImg" src="<?php echo($PagePath);?>../assets/images/category/<?php echo($txtPhoto);?>" style="width: 200px;">
										</div>
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
	});
	$('#txtDesc').keyup(function () {
		document.Form.txtDescCount.value = document.Form.txtDesc.value.length;
	});
	//Form Submit
	$("#Form").submit(function(evt) {
		evt.preventDefault();
		<?php CheckRight("Edit","ShowError");?>
		if (IsEmpty(document.Form.txtName.value) == true)
		{
			ShowError(true,"Error!","Please Enter Category Name ...",undefined,"txtName");
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
		if (document.Form.CategoryID.value == 0)
		{
			if (document.Form.txtPhoto.value == "")
			{
				ShowError(true,"Error!","Please Select PNG File To Upload ...",undefined,undefined);
				return(false);
			}
			if (CheckFile("Form","txtPhoto","PNG") == false )
			{
				ShowError(true,"Error!","Please Select PNG File To Upload ...",undefined,undefined);
				return(false);
			}
		}
		SaveCategory();
	});
	function SaveCategory()
	{
		var FrmData = new FormData(document.Form);
		var Result = "";
		$.confirm({
			title: "Processing",
			content: "",
			icon: "fa fa-save",
			animation: "scale",
			closeAnimation: "scale",
			opacity: 0.5,
			columnClass: "col-md-6 col-md-offset-3",
			content: function () {
				var self = this;
				return $.ajax({
					url: "<?php echo($PagePath);?>pages/ajax/category/category_save",
					type: "POST",
					data: FrmData,
					dataType: "JSON",
					async: false,
					cache: false,
					contentType: false,
					enctype: "multipart/form-data",
					processData: false
					}).done(function (response) {
						Result = response.Status;
						if (Result == "Error")
						{
							self.setType("red");
						}
						self.setTitle(response.Status);
						self.setContent(response.Message);
					}).fail(function(jqXHR,exception) {
						self.setTitle("Error!");
						self.setContent("Error Completing Operation. Please Try Again ...<br><br>"+jqXHR.responseText);
				});
			},
			buttons: {
				"OK": {
					text: "OK",
					btnClass: "btn-blue",
					action: function() {
						if (Result == "Done" && document.Form.CategoryID.value == 0)
						{
							$('#txtPhotoImg').attr('src', "");
							document.getElementById("Form").reset();
						}
					}
				}
			},
			onClose: function () {
			}
		});
	}
	function ShowPhoto(input)
	{
		if (input.files && input.files[0])
		{
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#txtPhotoImg').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#txtPhoto').on('change', function() {
		ShowPhoto(this);
	});
</script>
</body>
</html>