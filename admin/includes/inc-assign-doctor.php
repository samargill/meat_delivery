
<div id="Modal-Assign-Doc" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div  class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Assign Doctor</h4>
			</div>
			<div class="modal-body">
				<form name="FrmAssignDoc" action="">
					<div class="row">
						<div class="col-md-12">
							<table id="Doc-DataTable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th width="10%" style="text-align:left;">Sr#</th>
										<th width="45%" style="text-align:left;">Doctor</th>
										<th width="15%" style="text-align:center;">Status</th>
										<th width="15%" style="text-align:center;">Assigned</th>
										<th width="15%" style="text-align:center;">-</th>
									</tr>
								</thead>
								<tbody id="Doc-DataTableBody">
								</tbody>
							</table>
						</div>
					</div>
					<input type="hidden" name="BookID" value="">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" onclick="SaveAssignDoc(0,0);">Cancel Assign</button>
			</div>
		</div>
	</div>
</div>
