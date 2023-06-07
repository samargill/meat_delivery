
							<div class="row">
								<div class="col-sm-12">
									<div class="dataTables_info" id="MyDataTable_info" role="status" aria-live="polite">
										<?php
											$PageStart = (($Page - 1) * $PerPageRec) + 1;
											$PageClose = $Page * $PerPageRec;
											if ($PageClose > $Total)
											{
												$PageClose = $Total;
											}
										?>
										Showing <?php echo($PageStart);?> To <?php echo($PageClose);?> of <?php echo($Total);?> Rows
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="dataTables_paginate paging_simple_numbers" id="MyDataTable_paginate">
										<ul class="pagination">
											<li class="paginate_button previous disabled" id="MyDataTable_previous">
												<a href="#" aria-controls="MyDataTable" data-dt-idx="0" tabindex="0">Previous</a>
											</li>
											<?php
												for ($i = 1; $i <= ceil($Total / $PerPageRec); $i++)
												{
													if ($i == $Page)
														$PageClass = "active";
													else
														$PageClass = "";
											?>
											<li class="paginate_button <?php echo($PageClass);?>">
												<a href="<?php echo($PageLink."?".$PageParam."&Page=".$i);?>" tabindex="0"><?php echo($i);?></a>
											</li>
											<?php
												}
											?>
											<li class="paginate_button next disabled" id="MyDataTable_next">
												<a href="#" aria-controls="MyDataTable" data-dt-idx="2" tabindex="0">Next</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
