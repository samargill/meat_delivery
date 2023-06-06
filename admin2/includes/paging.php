
							<div class="row mt-2">
								<div class="col-sm-12 col-md-5">
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
								<div class="col-sm-12 col-md-7">
									<div class="dataTables_paginate paging_simple_numbers" id="MyDataTable_paginate">
										<ul class="pagination flex-wrap justify-content-end">
											<?php
												$StartNum = 1;
												$CloseNum = 10;
												$TotalPages = ceil(($Total / $PerPageRec));
												if($Page > $CloseNum)
												{
													$PageLen = strlen($Page) - 1;
													if($Page % 10 == 0)
														$StartNum = ($Page - $CloseNum) + 1;
													else
													{
														if ($PageLen == 1)
															$StartNum = ($StartNum + $CloseNum) * substr($Page,0,$PageLen);
														else
															$StartNum = $StartNum + ($CloseNum * substr($Page,0,$PageLen));
													}
													if($Page % 10 != 0 && $PageLen == 1)
													{
														$StartNum = $StartNum - substr($Page,0,$PageLen) + 1;
													}
													$CloseNum = $StartNum + $CloseNum - 1;
												}
												if($CloseNum > $TotalPages || $TotalPages == 0)
													$CloseNum = $TotalPages;
												if($Page - 1 == 0 || $TotalPages == 0)
												{
													$Disabled = "disabled";
													$Link = "#";
												}
												else
												{
													$Disabled = "";
													$Link     = "$PageLink?$PageParam&Page=".($Page - 1);
												}
											?>
											<li class="paginate_button page-item previous <?php echo($Disabled);?>" id="MyDataTable_previous">
												<a href="<?php echo($Link);?>" aria-controls="MyDataTable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
											</li>
											<?php
												if($StartNum != 1)
												{
											?>
											<li class="paginate_button page-item ">
												<a href="<?php echo($PageLink."?".$PageParam."&Page=".($Page - $CloseNum));?>" aria-controls="MyDataTable" data-dt-idx="1" tabindex="0" class="page-link">...</a>
											</li>
											<?php
												}
												for ($i = $StartNum; $i <= $CloseNum ; $i++)
												{
													if ($i == $Page)
														$PageClass = "active";
													else
														$PageClass = "";
											?>
											<li class="paginate_button page-item <?php echo($PageClass);?>">
												<a href="<?php echo($PageLink."?".$PageParam."&Page=".$i);?>" aria-controls="MyDataTable" data-dt-idx="1" tabindex="0" class="page-link"><?php echo($i);?></a>
											</li>
											<?php
												}
												if($CloseNum < $TotalPages)
												{
											?>
											<li class="paginate_button page-item">
												<a href="<?php echo($PageLink."?".$PageParam."&Page=".($CloseNum + 1));?>" aria-controls="MyDataTable" data-dt-idx="1" tabindex="0" class="page-link">...</a>
											</li>
											<?php
												}
												if($Page == $TotalPages || $TotalPages == 0)
												{
													$Disabled = "disabled";
													$Link = "#";
												}
												else
												{
													$Disabled = "";
													$Link     = "$PageLink?$PageParam&Page=".($Page + 1);
												}
											?>
											<li class="paginate_button page-item next <?php echo($Disabled);?>" id="MyDataTable_next">
												<a href="<?php echo($Link);?>" aria-controls="MyDataTable" class="page-link">Next</a>
											</li>
										</ul>
									</div>
								</div>
							</div>