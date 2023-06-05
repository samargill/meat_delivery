
<div id="Modal-Save-Doc" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Save Specialist & Gp Details</h4>
			</div>
			<div class="modal-body">
				<form name="FrmSaveDoc" action="">
					<div class="row">
						<div class="col-md-12">
							<?php
								$DocList = array(
									array("Name" => "Sp",   "DocTypeText" => "Specialist"),
									array("Name" => "Gp",   "DocTypeText" => "GP")
								);
								for ($i = 0; $i < count($DocList); $i++)
								{
							?>
							<section class="content" id="<?php echo($DocList[$i]["Name"]);?>Detail" style="padding: 15px 0; display: none;">		
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label><?php echo($DocList[$i]["DocTypeText"]);?> Name (Dr.)</label>
											<select name="cbo<?php echo($DocList[$i]["Name"]);?>Name" id="cbo<?php echo($DocList[$i]["Name"]);?>Name" class="form-control select2" style="width: 100%;">
												<option value="0">Type New / Search & Select Specialist</option>
											</select>
											<input type="hidden" name="txt<?php echo($DocList[$i]["Name"]);?>Name" value="">
										</div>
										<div class="form-group">
											<label><?php echo($DocList[$i]["DocTypeText"]);?> Suburb</label>
											<select name="txt<?php echo($DocList[$i]["Name"]);?>Suburb" id="txt<?php echo($DocList[$i]["Name"]);?>Suburb" class="form-control select2" style="width: 100%;">
												<option value="0">-- Select Suburb --</option>
											</select>
										</div>
										<div class="form-group">
											<label><?php echo($DocList[$i]["DocTypeText"]);?> Address</label>
											<input type="text" name="txt<?php echo($DocList[$i]["Name"]);?>Address" id="txt<?php echo($DocList[$i]["Name"]);?>Address" value="" class="form-control" maxlength="100">
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label><?php echo($DocList[$i]["DocTypeText"]);?> Phone #</label>
													<input type="tel" name="txt<?php echo($DocList[$i]["Name"]);?>Phone" id="txt<?php echo($DocList[$i]["Name"]);?>Phone" value="" class="form-control" maxlength="10">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label><?php echo($DocList[$i]["DocTypeText"]);?> Fax #</label>
													<input type="tel" name="txt<?php echo($DocList[$i]["Name"]);?>Fax" id="txt<?php echo($DocList[$i]["Name"]);?>Fax" value="" class="form-control" maxlength="10">
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
							<?php
								}
							?>
						</div>
					</div>
					<input type="hidden" name="ConsID" value="">
					<input type="hidden" name="DocType" value="">
					<input type="hidden" name="Index" value="">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="submit" name="btnSave" class="btn bg-purple next-step submit-detail" onclick="return SaveDetails();">
					<i class="fa fa-stethoscope"></i> &nbsp; Save Specialist
				</button>
			</div>
		</div>
	</div>
</div>
<?php
	$cboArea_Name = "txtSpSuburb";
	$cboArea_Status = "All";
	$cboArea_Multiple = "false";
	include($PagePath."includes/areacombo.php");
?>
<?php
	$cboArea_Name = "txtGpSuburb";
	$cboArea_Status = "All";
	$cboArea_Multiple = "false";
	include($PagePath."includes/areacombo.php");
?>