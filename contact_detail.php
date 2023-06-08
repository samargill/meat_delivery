<?php
	$PageID = array(5,0,0);
	$PagePath = "../../";
	$PageMenu = "Contact Us";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");

	$ContactID 	= $_REQUEST['ContactID'] ?? 0;
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
						<h1>Contact Details</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Contact Details</li>
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
						$Query = "SELECT contact_id, contactdate, contacttype, name, mobile, email, message".
							" FROM contactus WHERE contact_id = ".$ContactID;
						$rstRow = mysqli_query($Conn,$Query);
						if (mysqli_num_rows($rstRow) > 0)
						{
							$objRow = mysqli_fetch_object($rstRow);						
							if ($objRow->contacttype == 0)
							{
								$ContType = "Inquiry";
							}
							elseif ($objRow->contacttype == 1)
							{
								$ContType = "Suggestion";
							}
							elseif ($objRow->contacttype == 2)
							{
								$ContType = "Complaint";
							}
					?>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Contact Name :</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<button type="button" class="btn btn-primary">
												<i class="fa fa-user color-white"></i>
											</button>
										</div>
										<input type="text" class="form-control" value="<?php echo($objRow->name);?>" data-mask readonly>
									</div>
								</div>
								<div class="form-group">
									<label>Mobile # :</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<button type="button" class="btn btn-primary">
												<i class="fa fa-phone color-white"></i>
											</button>
										</div>
										<input type="text" class="form-control" value="<?php echo($objRow->mobile);?>" data-mask readonly>
									</div>
								</div>
								<div class="form-group">
									<label>Email :</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<button type="button" class="btn btn-primary">
												<i class="fa fa-envelope color-white"></i>
											</button>
										</div>
										<input type="text" class="form-control" value="<?php echo($objRow->email);?>" data-mask readonly>
									</div>
								</div>
								<div class="form-group">
									<label>Message :</label>
									<textarea class="form-control" readonly rows="4"><?php echo($objRow->message);?></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Contact Date :</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<button type="button" class="btn btn-primary">
												<i class="fa fa-calendar color-white"></i>
											</button>
										</div>
										<input type="text" class="form-control" value="<?php echo(ShowDate($objRow->contactdate,4));?>" data-mask readonly>
									</div>
								</div>
								<div class="form-group">
									<label>Contact Type :</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<button type="button" class="btn btn-primary">
												<i class="fa fa-check color-white"></i>
											</button>
										</div>
										<input type="text" class="form-control" value="<?php echo($ContType);?>" data-mask readonly>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php 
						}
					?>
				</div>
			</div>
		</section>
	</div>
</div>
<?php
	include($PagePath."includes/inc-js.php");
?>
</body>
</html>