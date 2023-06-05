<!-- Add New Patient Modal -->
<div id="Modal_EditFamily" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<form name="FrmPatient" id="FrmPatient" role="form" method="post">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<h4 class="modal-title">Edit Family Member</h4>
				</div>
				<div class="modal-body">
					<div id="RowRelationRow" class="row">
						<div class="col-12 col-md-6">
							<div id="RowRelation" class="form-group form-sm-group text-left">
								<p class="color-light">Relationship With Account Holder :</p>
								<div class="md-select md-input">
									<?php
										$Query = "SELECT relaid, relaname".
											" FROM relationship WHERE relatype = 1 AND relaid > 1 ORDER BY relaid";
										$rstRow = mysqli_query($Conn,$Query);
									?>
									<select name="cboRelation" id="cboRelation" class="form-control select2" style="width: 100%;">
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
					<div class="row mb-4">
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">First Name</p>
								<div class="md-input">
									<input type="text" name="txtFirstName" id="txtFirstName" value="" class="form-control" maxlength="100">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Last Name</p>
								<div class="md-input">
									<input type="text" name="txtLastName" id="txtLastName" value="" class="form-control" maxlength="100">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Medicare Number</p>
								<div class="md-input">
									<input type="text" name="txtMedNo" id="txtMedNo" value="" class="form-control" maxlength="10">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Medicare Ref #</p>
								<div class="md-input">
									<input type="text" name="txtMedRef" id="txtMedRef" value="" class="form-control" maxlength="2">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Medicare Expiry</p>
								<div class="md-input">
									<input type="text" name="txtMedExp" id="txtMedExp" value="" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group text-left">
								<p class="color-light">Concession Card</p>
								<div class="md-select md-input">
									<select name="cboMedCon" id="cboMedCon" class="form-control select2">
										<option value="0">-- Select --</option>
										<option value="1">Yes</option>
										<option value="2">No</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group text-left">
								<p class="color-light">Gender</p>
								<div class="md-select md-input">
									<select name="cboGender" id="cboGender" class="form-control select2">
										<option value="0">-- Select --</option>
										<option value="1">Male</option>
										<option value="2">Female</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Date of Birth</p>
								<div class="md-input">
									<input type="tel" name="txtDOB" id="txtDOB" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group">
								<p class="color-light">Mobile</p>
								<div class="md-input">
									<input type="tel" name="txtMobile" id="txtMobile" class="form-control" maxlength="10">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group form-sm-group text-left">
								<p class="color-light">Any Disability</p>
								<div class="md-select md-input">
									<select name="cboDisab" id="cboDisab" class="form-control select2">
										<option value="0">-- Select --</option>
										<option value="1">Yes</option>
										<option value="2">No</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="PatientID" value="0">
					<input type="hidden" name="FamilyID"  value="0">
					<input type="hidden" name="SaveFamilyMember" value="">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save Member</button>
				</div>
			</div>
		</form>
	</div>
</div>