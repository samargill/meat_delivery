			<?php
				if (isset($_REQUEST['Tab']))
					$Tab = $_REQUEST['Tab'];
				else
					$Tab = "SpDetail";
			?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Save Specialist & Gp Details</h4>
			</div>
			<div class="modal-body">
				<form name="FrmSaveSpGp" action="">
					<div class="row">
						<div class="col-md-12">
							<!-- Main Content -->
							<section class="content" style="padding: 15px 0;">
								<div class="nav-tabs-custom">
									<ul class="nav nav-tabs">
										<?php
											$TabList = array(
												array("Name" => "SpDetail",   "Text" => "Specialist Detail"),
												array("Name" => "GpDetail",   "Text" => "GP Detail")
											);
											for ($i = 0; $i < count($TabList); $i++)
											{
										?>
										<li <?php if ($Tab == $TabList[$i]["Name"]) echo("class=\"active\"");?>>
											<a href="#<?php echo($TabList[$i]["Name"]);?>" data-toggle="tab" onclick="LoadTab('<?php echo($TabList[$i]["Name"]);?>');">
												<i class="fa fa-gear"></i>&nbsp;&nbsp;<?php echo($TabList[$i]["Text"]);?>
											</a>
										</li>
										<?php
											}
										?>
									</ul>
									<div class="tab-content">
										<?php
											if ($Tab == "SpDetail" || $Tab == "GpDetail")
											{
												// Specialist Information
												$cboName     = 0;
												$cboNameText = "Type New / Search & Select";
												if ($Tab == "SpDetail")
												{
													$DocTypeText = "Specialist";
													$TextBox = "Sp";
													$cboNameText .= " Specialist";
												}
												else
												{
													$DocTypeText = "GP";
													$TextBox = "Gp";
													$cboNameText .= " GP";
												}
												$txtName     = "";
												$txtAddress  = "";
												$txtPhone    = "";
												$txtFax      = "";
												$txtSuburbID = 0;
												$txtSuburb   = "-- Select Suburb --";
										?>
										<div id="SpDetail" class="tab-pane active">
											<section>
												<div class="box-body">
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label><?php echo($DocTypeText);?> Name (Dr.)</label>
																<select name="cbo<?php echo($TextBox);?>Name" id="cbo<?php echo($TextBox);?>Name" class="form-control select2" style="width: 100%;">
																	<option value="<?php echo($cboName);?>"><?php echo($cboNameText);?></option>
																</select>
																<input type="hidden" name="txt<?php echo($TextBox);?>Name" value="<?php echo($txtName);?>">
															</div>
															<div class="form-group">
																<label><?php echo($DocTypeText);?> Suburb</label>
																<select name="txt<?php echo($TextBox);?>Suburb" id="txt<?php echo($TextBox);?>Suburb" class="form-control select2" style="width: 100%;">
																	<option value="<?php echo($txtSuburbID);?>"><?php echo($txtSuburb);?></option>
																</select>
															</div>
															<div class="form-group">
																<label><?php echo($DocTypeText);?> Address</label>
																<input type="text" name="txt<?php echo($TextBox);?>Address" id="txt<?php echo($TextBox);?>Address" value="<?php echo($txtAddress);?>" class="form-control" maxlength="100">
															</div>
															<div class="row">
																<div class="col-md-6">
																	<div class="form-group">
																		<label><?php echo($DocTypeText);?> Phone #</label>
																		<input type="tel" name="txt<?php echo($TextBox);?>Phone" id="txt<?php echo($TextBox);?>Phone" value="<?php echo($txtPhone);?>" class="form-control" maxlength="10">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label><?php echo($DocTypeText);?> Fax #</label>
																		<input type="tel" name="txt<?php echo($TextBox);?>Fax" id="txt<?php echo($TextBox);?>Fax" value="<?php echo($txtFax);?>" class="form-control" maxlength="10">
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</section>
										</div>
										<?php
											}
										?>
									</div>
								</div>
							</section><!-- /.content -->
						</div>
					</div>
					<input type="hidden" name="ConsID" value="">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="submit" name="btnSave<?php echo($TextBox);?>" class="btn bg-purple next-step" onclick="return Save<?php echo($TextBox);?>();">
					<i class="fa fa-stethoscope"></i> &nbsp; Save <?php echo($DocTypeText);?>
				</button>
			</div>