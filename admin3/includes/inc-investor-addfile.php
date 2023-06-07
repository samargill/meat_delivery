<div id="Modal-AddFile" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Add New Investor Document</h4>
			</div>
			<div class="modal-body">
				<form name="FrmAdd" action="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Search by Investor (Client Name | Contact Name)</label>
								<select name="cboInvestor" CLASS="form-control select2" style="width: 100%;">
									<option value="0">-- Select --</option>
									<?php
										$Query = "SELECT investorid, CONCAT(clientname,' | ',contactname) ".
											" AS Name FROM investor ORDER BY investorid";
										$rstRow = mysqli_query($Conn,$Query);
										while ($objRow = mysqli_fetch_object($rstRow))
										{
											if ($cboInvestor == $objRow->investorid)
												$Selected = "SELECTED";
											else
												$Selected = "";
									?>
									<option value="<?php echo($objRow->investorid);?>" <?php echo($Selected);?>><?php echo($objRow->Name);?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<?php
									$ComboData = array();
									$ComboData[] = "-- Select --";
									$ComboData[] = "Contract";
									$ComboData[] = "General";
								?>
								<select name="cboDocuType" id="cboDocuType" class="form-control select2" style="width: 100%;">
									<?php
										for ($i = 0; $i < count($ComboData); $i++)
										{
											if ($cboDocuType == $i)
												$ComboSelect = "SELECTED";
											else
												$ComboSelect = "";
									?>
									<option value="<?php echo($i);?>" <?php echo($cboDocuType);?> <?php echo($ComboSelect);?>><?php echo($ComboData[$i]);?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<textarea name="txtDesc" id="txtDesc" class="form-control"></textarea>
							</div>
							<div class="form-group">
								<input type="file" name="txtFiles[]" id="txtMyFile" multiple="" accept="application/pdf">
							</div>
							<input type="hidden" name="BtnType" value="">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" name="btnAdd" class="btn btn-primary" onclick="return VerifyFiles();">Upload Files</button>
			</div>
		</div>
	</div>
</div>