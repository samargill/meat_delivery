<?php
	// $PageID   = array(1,0,0);
	$PageID   = array(8,0,0);
	$PagePath = "../../";
	$PageMenu = "Upload Contact";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	CheckRight("View");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/combos.php");
	require($PagePath."lib/PHPSpreadsheet/vendor/autoload.php");
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Csv;
	use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

	if (isset($_REQUEST["BtnUploadContact"]))
	{
		if (!is_uploaded_file($_FILES['txtUploadFile']['tmp_name']))
		{
			header("location: upload-contact?Err=103");
			exit();
		}
		$UploadDir = $PagePath."../files/upload-contact/";
		$FileType  = pathinfo($_FILES['txtUploadFile']['name'],PATHINFO_EXTENSION);
		$FileName  = date("YmdHis").".".$FileType;
		if ($FileType == 'csv')
		{
			$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		}
		elseif ($FileType == 'xlsx')
		{
			$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}
		else
		{
			header("location: upload-contact?Err=104");
			exit();	
		}
		if (move_uploaded_file($_FILES["txtUploadFile"]["tmp_name"],$UploadDir.$FileName) == false)
		{
			header("location:upload-contact?Err=105");
			exit();
		}
		$Reader->setReadDataOnly(true);
		$SpreadSheet = $Reader->load($UploadDir.$FileName);
		$WorkSheet   = $SpreadSheet->getActiveSheet();
		$MaxRow 	 = $WorkSheet->getHighestRow();
		$MaxCol 	 = $WorkSheet->getHighestColumn();
		if ($MaxCol != "B")
		{
			header("location:upload-contact?Err=106");
			unlink($UploadDir.$FileName);
			exit();
		}
		foreach ($WorkSheet->getRowIterator() as $ExcelRow) 
		{
			$ExcelCell = $ExcelRow->getCellIterator();
			$ExcelCell->setIterateOnlyExistingCells(false);
			$ExcelData = [];
			foreach ($ExcelCell as $cell) 
			{
				$ExcelData[] = $cell->getValue();
			}
			$ContactName   = mysqli_real_escape_string($Conn,$ExcelData[0]);
			$ContactNumber = mysqli_real_escape_string($Conn,$ExcelData[1]);
			//Check For Duplicate Entries
			$CheckContactNum = GetValue("contactnumber","uploadcontact","contactnumber = '".$ContactNumber."'");
			if ($CheckContactNum == 0)
			{
				$Query = "INSERT INTO uploadcontact (contactname, contactnumber)".
					" VALUES ('".$ContactName."', '".$ContactNumber."')";
				$rstRow = mysqli_query($Conn,$Query);
				if (count(mysqli_error_list($Conn)) > 0)
				{
					$_SESSION["MysqlErr"] = mysqli_error($Conn);
					header("Location: addcampaign?Err=101");
					exit;
				}
			}	
		}
		$SpreadSheet->disconnectWorksheets();
		$SpreadSheet->garbageCollect();
		unset($Reader);
		unset($SpreadSheet);
		@unlink($UploadDir.$FileName);
		header("location:upload-contact?Err=1");
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include($PagePath."includes/inc-css.php");
	?>
</head>
<body class="hold-transition <?php echo(constant("WebsiteSkin"));?> sidebar-mini">
<div class="wrapper">
	<!--===== Top Menu =====-->
	<?php
		include($PagePath."includes/header.php");
	?>
	<!--===== Left Menu =====-->
	<?php
		include($PagePath."includes/left.php");
	?>
	<!--===== Page Content =====-->
	<div class="content-wrapper">
		<!--===== Page Header =====-->
		<section class="content-header">
			<h1>Upload Contacts</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo($PagePath);?>main"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">upload-contact</li>
			</ol>
		</section>
		<!--===== Main Content =====-->
		<section class="content">
			<div class="box box-primary">
				<?php
					if (isset($_REQUEST['Err']))
					{
						$Message 	 = "";
						$MessageBG 	 = "callout-danger lead";
						$MessageHead = "Error:";
						$MessageIcon = "fa-exclamation-circle";
						switch ($_REQUEST['Err'])
						{
							case 1:
								$Message = "Contacts Uploaded Successfully ...";
								break;
							case 101:
								if (isset($_SESSION["MysqlErr"]))
								{
									$Message .= "<!-- Error : ".$_SESSION["MysqlErr"]."-->";
									unset($_SESSION["MysqlErr"]);
								}
								break;
							case 103:
								$Message = "Failed To Save File. Invalid / Corrupt File ...";
								break;
							case 104:
								$Message = "File Type Is Not Valid. Invalid / Corrupt File ...";
								break;
							case 105:
								$Message = "Sorry, there was an error uploading your file. Please Try Again";
								break;
							case 106 :
								$Message = "Uploaded Excel File Does Not Have 2 Required Columns ...";	
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
				<form name="Form" role="form" action="upload-contact" method="post" enctype="multipart/form-data">
					<div class="box-body pad margin">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="txtUploadFile">Upload Bulk Contact</label>
									<input type="file" id="txtUploadFile" name="txtUploadFile" class="form-control">
								</div>
								<button type="submit" name="BtnUploadContact" class="btn btn-primary" onclick="return Verify();">
									<i class="fa fa-search"></i> &nbsp; Search Clients
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</section><!--===== End content =====-->
	</div><!--===== End Content Wrapper =====-->
	<?php
		include($PagePath."includes/footer.php");
	?>
</div><!--===== End wrapper =====-->
<?php
	include($PagePath."includes/inc-js.php");
?>
<!--===== Page Script =====-->
<script>
	$(function () {
		//Init Select2
		$(".select2").select2();
		//Init DataTable
		$('#MyDataTable').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": true,
			"bInfo" : false,
			"autoWidth": false,
			"iDisplayLength": 50,
			"scrollX": true,
		});
	});

	function Verify()
	{
		CheckRight("Add","Redirect");
		if (document.getElementById('txtUploadFile').value == "")
		{
			ShowError(true,"Error!","Please Attach File First",undefined,'txtUploadFile');
			return(false);
		}
	}
</script>
<?php
	$GLOBALS["DateRangePickerSingle"] = false;
	$GLOBALS["DateRangePickerFormatShow"] = "d-m-Y H:i:s";
	$GLOBALS["DateRangePickerFormatSave"] = "YYYY-MM-DD HH:mm:ss";
	$GLOBALS["DateRangePickerAlign"] = "right";
	include($PagePath."includes/daterangepicker.php");
?>
</body>
</html>