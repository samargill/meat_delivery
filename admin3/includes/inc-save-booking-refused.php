
<div id="Modal-Book-Refused" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Save Booking Refusal Reason</h4>
			</div>
			<div class="modal-body">
				<form name="FrmBookRefused" action="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Please Choose Booking Cancellation Reason (*) :</label>
								<select name="cboReason" id="cboReason" class="form-control select2" style="width: 60%;">
									<option value="0">-- Select Refusal Reason --</option>
									<?php
										$Query = "SELECT reasonid, reasonname FROM cancelreason WHERE status = 1";
										$rstPro = mysqli_query($Conn,$Query);
										while ($objPro = mysqli_fetch_object($rstPro))
										{
									?>
									<option value="<?php echo($objPro->reasonid);?>"><?php echo(UCString($objPro->reasonname));?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group" id="Que-Form-VideoValue">
								<label>Please Describe The Reason In Free Text (*) :</label>
								<textarea name="txtReason" id="txtReason" class="form-control" style="width:90%; border-radius:3px;" rows="4"></textarea>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveRefusalReason();">Save Reason</button>
			</div>
		</div>
	</div>
</div>
