
<div id="Modal-Copy-Rights" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Copy User Rights</h4>
			</div>
			<div class="modal-body">
				<form name="FrmCopyRights" action="">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<?php 
									$AdminType = GetValue("admintype", "adminlogin", "adminid = ".$UserID);
								?>
								<label>Select User To Copy Rights (*) :</label>
								<select name="cboUser" id="cboUser" class="form-control select2" style="width: 100%;">
									<option value="0">-- Select Admin --</option>
									<?php 
										$Query = "SELECT AL.adminid, AL.firstname, AL.lastname".
											" FROM adminlogin AL".
											" INNER JOIN adminmenurights AMR ON AL.adminid = AMR.adminid".
											" WHERE AL.admintype = ".$AdminType." AND AL.adminid <> ".$UserID.
											" AND AL.deletedate IS NULL AND AL.status = 1".
											" GROUP BY AL.adminid";
										$rstRow = mysqli_query($Conn,$Query);
										if (mysqli_num_rows($rstRow) > 0)
										{
											while ($objRow = mysqli_fetch_object($rstRow))
											{
												$AdminName = $objRow->firstname." ".$objRow->lastname;
												if ($AdminType == 3)
													$AdminName = "Dr. ".$AdminName;
									?>
									<option value="<?php echo($objRow->adminid); ?>"><?php echo($AdminName); ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="SaveUserRights();">Copy User Rights</button>
			</div>
		</div>
	</div>
</div>
