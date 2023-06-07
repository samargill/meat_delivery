
<div id="Modal-Consult-Mode" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Edit Consult Mode</h4>
			</div>
			<div class="modal-body">
				<form name="FrmConsultMode" action="">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Prefered Mode of Video Consultation (*) :</label>
								<select name="cboVideoMode" id="cboVideoMode" onchange="SetVideoMode();" class="form-control select2" style="width: 100%;">
									<?php
										$Query = "SELECT consmodeid, consmodename, datatype, inputtext".
											" FROM consultationmode WHERE status = 1 ORDER BY consmodeid";
										$rstPro = mysqli_query($Conn,$Query);
										while ($objPro = mysqli_fetch_object($rstPro))
										{
											if ($objPro->consmodeid == $objRow->videomode)
												$Selected = "selected";
											else
												$Selected = "";
									?>
									<option value="<?php echo($objPro->consmodeid);?>" <?php echo($Selected); ?> data-name="<?php echo($objPro->consmodename);?>" data-msg="<?php echo($objPro->inputtext);?>" data-type="<?php echo($objPro->datatype);?>"><?php echo(UCString($objPro->consmodename));?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group" id="Que-Form-VideoValue">
								<label id="LblVideoMode">Mobile # (*) :</label>
								<input type="tel" name="txtVideoMode" id="txtVideoMode" value="<?php echo($objRow->videocontact);?>" maxlength="10" class="form-control" style="text-transform: lowercase;">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="return SaveConsultMode();">Save Changes</button>
			</div>
		</div>
	</div>
</div>
