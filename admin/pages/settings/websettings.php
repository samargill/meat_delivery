<?php
	$PageID = array(11,1,0);
	$PagePath = "../../";
	$PageMenu = "Settings";
	$PageName = "Website Settings";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View","Redirect");
	include($PagePath."lib/functions.php");

	if (isset($_REQUEST['Tab']))
		$Tab = $_REQUEST['Tab'];
	else
		$Tab = "General";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
	<script language="javascript">
		function LoadTab(TabID)
		{
			window.location = "websettings?Tab="+TabID;
		}
		function Verify(Type,TextBox,Empty,ConfigID,Lable = '')
		{
			<?php CheckRight("Edit","ShowError");?>
			if (Lable != "")
			{
				if (IsEmpty(document.Form.elements[Lable].value) == true && Empty == false)
				{
					ShowError(true,"Error!","Please Enter Valid Lable Text ...","",Lable)
					return(false);
				}
				else
				{
					Lable = document.Form.elements[Lable].value;
				}
			}
			if (Type == "Text")
			{
				if (IsEmpty(document.Form.elements[TextBox].value) == true && Empty == false)
				{
					ShowError(true,"Error!","Please Enter Valid Text ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Phone")
			{
				if (IsPhone(document.Form.elements[TextBox].value,Empty) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Phone ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Email")
			{
				if (IsEmail(document.Form.elements[TextBox].value,Empty) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Email ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Boolean")
			{
				if (document.Form.elements[TextBox].value != "0" && document.Form.elements[TextBox].value != "1")
				{
					ShowError(true,"Error!","Please Enter Value 1 For Yes or 0 For No ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Integer")
			{
				if (IsNumber(document.Form.elements[TextBox].value,Empty,false,0) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Numeric Whole Number Value ...","",TextBox)
					return(false);
				}
			}
			else if (Type == "Double")
			{
				if (IsNumber(document.Form.elements[TextBox].value,Empty,true,0) == false)
				{
					ShowError(true,"Error!","Please Enter Valid Numeric Decimal Value ...","",TextBox)
					return(false);
				}
				else
				{
					document.Form.elements[TextBox].value = ShowFloat(document.Form.elements[TextBox].value,2);
				}
			}
			$.confirm({
				title: "Saving!",
				content: "Are You Sure You Want To Save This Setting ?",
				icon: "fa fa-save",
				animation: "scale",
				closeAnimation: "scale",
				opacity: 0.5,
				buttons: {
					"confirm": {
						text: "Yes",
						btnClass: "btn-blue",
						keys: ['enter'],
						action: function() {
							$.confirm({
								content: function () {
									var self = this;
									return $.ajax({
										url: '../ajaxs/admin/setting/setting-save',
										dataType: 'JSON',
										method: 'POST',
										data: {
											"Parent"      : "SaveSetting",
											"ConfigID"    : ConfigID,
											"ConfigName"  : Lable,
											"ConfigValue" : document.Form.elements[TextBox].value
										}
										}).done(function (response) {
											self.setTitle("Saved!");
											self.setContent(response.Message);
										}).fail(function(){
											self.setTitle("Error!");
											self.setContent('Error completing operation. Please try again ...');
										});
								},
								buttons: {
									"OK": {
										text: "OK",
										btnClass: "btn-blue"
									}
								},
								onClose: function () {
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
<body class="hold-transition <?php echo(WebsiteSkin);?> sidebar-mini">
<div class="wrapper">
	<?php
		if (defined("AppView") == false)
		{
	?>
	<?php
		include($PagePath."includes/header.php");
	?>
	<?php
		include($PagePath."includes/left.php");
	?>
	<?php
		}
	?>
	<div class="content-wrapper" <?php if (defined("AppView")){ echo("style=\"margin-left: 0px;\"");} ?> >
		<?php
			if (defined("AppView"))
			{
				$Padding     = "style=\"padding:0\"";
				$CardBodyPad = "style=\"padding:0.30rem;\"";
				$CardStyle   = "style=\"padding:0.30rem; margin-bottom: 0rem;\"";
				$CardClass   = "";
				$Margin      = "";
				$RowPadding  = "style=\"padding-right: 0px;padding-left:0px;\"";
			}
			else
			{
				$Margin      = "mb-2";
				$CardBodyPad = "";
				$CardStyle   = "";
				$CardClass   = "card-outline card-primary";
				$Padding     = "";
				$RowPadding  = "";
			}
		?>
		<section class="content-header" <?php echo($Padding);?>>
			<div class="container-fluid">
				<div class="row <?php echo($Margin);?>">
					<?php
						if (defined("AppView") == false)
						{
					?>
					<div class="col-sm-6">
						<h1><?php echo($PageName)?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item"><a href="#"><?php echo($PageMenu)?></a></li>
							<li class="breadcrumb-item active"><?php echo($PageName)?></li>
						</ol>
					</div>
					<?php
						}
					?>
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
					case 2:
						$Message = "Website Settings Updated Successfully ...";
						break;
					case 101:
						$Message = "Unable To Update Website Settings - Fatal Error ...";
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
		<section class="content" <?php echo($Padding);?>>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12" <?php echo($RowPadding);?> >
						<div class="card <?php echo($CardClass);?>" <?php echo($CardStyle);?>>
							<div class="card-header p-0 border-bottom-0">
								<?php
									if (defined("AppView") == false)
									{
								?>
								<ul class="nav nav-tabs" role="tablist">
								<?php
									$TabList = array(
										array("Name" => "General",      "Text" => "General"),
										array("Name" => "Social",       "Text" => "Social Media")
									);
									for ($i = 0; $i < count($TabList); $i++)
									{
								?>
									<li class="nav-item">
										<a href="#<?php echo($TabList[$i]["Name"]);?>" class="nav-link <?php if ($Tab == $TabList[$i]["Name"]) echo("active");?>" data-toggle="tab" onclick="LoadTab('<?php echo($TabList[$i]["Name"]);?>');">
											<i class="fa fa-gear"></i>&nbsp;&nbsp;<?php echo($TabList[$i]["Text"]);?>
										</a>
									</li>
								<?php
									}
								?>
								</ul>
								<?php
									}
								?>
								<div class="card-body" <?php echo($CardBodyPad);?>>
									<div class="tab-content">
									<?php
										if ($Tab == "General")
										{
									?>
									<div id="General" class="tab-pane active">
										<section>
											<form name="Form" role="form" action="websettings" method="post">
												<div class="box-body">
													<div class="row">
														<div class="col-md-6">
														<?php
															$Query = "SELECT config_id, config_name, config_value".
																" FROM websettings WHERE ".
																"    (config_id BETWEEN 1   AND 11)".
																" OR (config_id BETWEEN 101 AND 102)".
																" OR (config_id = 307)".
																" OR (config_id = 401)".
																" ORDER BY config_id";
															$rstRow = mysqli_query($Conn,$Query);
															$NumRow = mysqli_num_rows($rstRow);
															$RowColumn = 8;
															$i = 1;
															$Integer = array("102","307");
															$Phone   = array("2","3");
															$Email   = array("4","5","6","7");
															while ($objRow = mysqli_fetch_object($rstRow))
															{
																$Type = "Text";
																if (in_array($objRow->config_id,$Integer))
																{
																	$Type = "Integer";
																}
																elseif (in_array($objRow->config_id,$Phone))
																{
																	$Type = "Phone";
																}
																elseif (in_array($objRow->config_id,$Email))
																{
																	$Type = "Email";
																}
														?>
															<div class="form-group">
																<label><?php echo($objRow->config_name);?> (*) :</label>
																<div class="input-group">
																	<input type="text" name="txtGeneralField<?php echo($objRow->config_id);?>" id="txtGeneralField<?php echo($objRow->config_id);?>" value="<?php echo($objRow->config_value);?>" class="form-control">
																	<span class="input-group-btn">
																		<button type="button" class="btn btn-primary btn-flat" onclick="Verify('<?php echo($Type);?>','txtGeneralField<?php echo($objRow->config_id);?>',false,<?php echo($objRow->config_id);?>);">Save</button>
																	</span>
																</div>
															</div>
															<?php
																if ($i == $RowColumn)
																{
																	echo(
																		"</div>".
																		"<div class=\"col-md-6\">"
																	);
																}
															?>
														<?php
																$i++;
															}
														?>
														</div>
													</div>
												</div>
											</form>
										</section>
									</div>
									<?php
										}
										else if ($Tab == "Social")
										{
									?>
									<div id="Social" class="tab-pane active">
										<section>
											<form name="Form" role="form" action="websettings" method="post">
												<div class="box-body">
													<div class="row">
														<?php
															$Query = "SELECT config_id, config_name, config_value".
																" FROM websettings WHERE config_id BETWEEN 201 AND 205".
																" ORDER BY config_id";
															$rstRow = mysqli_query($Conn,$Query);
															while ($objRow = mysqli_fetch_object($rstRow))
															{
														?>
														<div class="col-md-6">
															<div class="form-group">
																<label><?php echo($objRow->config_name);?> (*) :</label>
																<div class="input-group">
																	<input type="text" name="txtSocial<?php echo($objRow->config_id);?>" id="txtSocial<?php echo($objRow->config_id);?>" value="<?php echo($objRow->config_value);?>" maxlength="100" class="form-control">
																	<span class="input-group-btn">
																		<button type="button" class="btn btn-primary btn-flat" onclick="Verify('Text','txtSocial<?php echo($objRow->config_id);?>',true,<?php echo($objRow->config_id);?>);">Save</button>
																	</span>
																</div>
															</div>
														</div>
														<?php
															}
														?>
													</div>
												</div>
											</form>
										</section>
									</div>
									<?php
										}
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
<?php
	if (defined("AppView") == false)
	{
?>
<?php
	include($PagePath."includes/footer.php");
?>
<?php
	}
?>
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