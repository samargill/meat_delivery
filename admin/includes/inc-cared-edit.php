
<div id="Modal-Cared-Detail" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Edit Cared Details</h4>
			</div>
			<div class="modal-body">
				<form name="FrmCaredDetails" action="">
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label>Please enter the full name of the person being cared :</label>
								<input type="text" id="txtCared" name="txtCared" class="form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Please select the relationship with the person being cared :</label>
								<div class="row">
									<div class="col-md-10">
										<?php
											$Query = "SELECT relaid, relaname".
												" FROM relationship WHERE relaid > 1 ORDER BY relaid";
											$rstRow = mysqli_query($Conn,$Query);
										?>
										<select name="cboCaredRela" id="cboCaredRela" class="form-control select2" style="width: 100%;">
											<option value="0">-- Select Relationship --</option>
											<?php
												while ($objRow = mysqli_fetch_object($rstRow))
												{
											?>
											<option value="<?php echo($objRow->relaid);?>"><?php echo(UCString($objRow->relaname));?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label>Please enter date of birth of person being cared :</label>
								<input type="text" name="txtCaredDOB" id="txtCaredDOB" class="form-control dob-datepicker" placeholder="Select Date" readonly="">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label>Please enter the Medical Condition of the person being cared :</label>
								<input type="text" name="txtCaredCond" id="txtCaredCond" value="" maxlength="100" class="form-control">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveCaredDetails();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
