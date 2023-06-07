
<div id="Modal-Change-Pharmacy" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Change Pharmacy Details</h4>
			</div>
			<div class="modal-body">
				<form name="FrmPharmacy" action="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Select Nearest Pharmacy To Send Prescription via Fax (*) :</label>
								<div class="row">
									<div class="col-md-10">
										<select name="cboPharmacy" id="cboPharmacy" class="form-control select2" style="width: 100%;">
											<option value="0">-- Select Nearest Pharmacy --</option>
										</select>
									</div>
									<div class="col-md-2">
										<button type="button" class="btn btn-warning" style="width: 100%;" title="Reload Pharmacies" data-toggle="tooltip" data-container="body" onclick="return LoadChangePharmacy();">
											<i class="fa fa-refresh"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveChangePharmacy();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
