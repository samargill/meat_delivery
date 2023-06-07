
<div id="Modal-Change-MedItem" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Change Medicare Item #</h4>
			</div>
			<div class="modal-body">
				<form name="FrmChangeMedItem" action="">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Medicare Item # (*) :</label>
								<input type="tel" name="txtMedItemNo" id="txtMedItemNo" value="" class="form-control">
							</div>
						</div>
					</div>
					<input type="hidden" name="BookID" value="">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveMedItem();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
