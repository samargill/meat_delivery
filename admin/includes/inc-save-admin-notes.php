
<div id="Modal-Book-Notes" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="notes-title"></h4>
			</div>
			<div class="modal-body">
				<form name="FrmBookNotes" action="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Booking Notes (*) :</label>
								<div class="row">
									<div class="col-md-12">
										<textarea name="txtBookNotes" id="txtBookNotes" rows="4" class="form-control"><?php echo($BookNotes);?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveBookNotes();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
