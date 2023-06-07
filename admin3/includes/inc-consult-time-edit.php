
<div id="Modal-Consult-Time" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Edit Consult Time</h4>
			</div>
			<div class="modal-body">
				<form name="FrmConsultTime" action="">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Appointment Date (*) :</label>
								<input type="text" name="txtConsultDate" id="txtConsultDate" value="<?php echo(date("d/m/Y"));?>" class="form-control datepicker" placeholder="&nbsp; Select Date">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Preferred Time For Appointment [AEST] (*) :</label>
								<div class="row">
									<div class="col-md-10">
										<select name="cboConsultTimeSlot" id="cboConsultTimeSlot" class="form-control select2" style="width: 100%;">
											<option value="0">--- Select Appointment Time ---</option>
										</select>
									</div>
									<div class="col-md-2">
										<button type="button" name="btnSavePatient" class="btn btn-warning" style="width: 100%;" title="Reload Appointment Time" data-toggle="tooltip" data-container="body" onclick="return LoadTimeSlots();">
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
				<button type="button" class="btn btn-primary" onclick="return SaveConsultTime();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
