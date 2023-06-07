
<script>
	$(function () {
		//Initialize Select2 Elements
		$("#<?php echo(isset($cboArea_Name) ? $cboArea_Name : "txtSuburb");?>").select2({
			minimumInputLength: 2,
			<?php
				if (isset($cboArea_Multiple))
				{
			?>
			multiple: <?php echo($cboArea_Multiple);?>,
			<?php
				}
			?>
			createTag: function() {
				return(null);
			},
			tags: [],
			ajax: {
				url: "<?php echo($PagePath);?>../json?GetSuburb&PostCode=<?php echo(isset($cboArea_PostCode) ? $cboArea_PostCode : "false");?>",
				dataType: 'json',
				type: "GET",
				quietMillis: 50,
				data: function (params) {
					return {
						q: params.term
					};
				},
				processResults: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								text: item.text,
								id: item.id
							}
						})
					};
				}
			}
		});
	});
</script>
