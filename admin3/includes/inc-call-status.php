<div id="Modal-CallStatus" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Incomplete Booking Call Status</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="FrmCallStatus" action="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<?php
									$Query = "SELECT statusid, statusname".
										" FROM callstatus WHERE status = 1";
									$rstRow = mysqli_query($Conn,$Query);
								?>
								<select name="cboReason" id="cboReason" CLASS="form-control select2" style="width: 100%;">
									<option value="0">-- Select Reason--</option>
									<?php
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											if ($cboReason == $objRow->statusid)
												$ComboSelect = "SELECTED";
											else
												$ComboSelect = "";
									?>
									<option value="<?php echo($objRow->statusid);?>" <?php echo($ComboSelect);?>><?php echo($objRow->statusname);?></option>
									<?php
										}
									?>
								</select>
								<input type="hidden" name="txtBookID" value="">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveStatus();">Save Status</button>
			</div>
		</div>
	</div>
</div>