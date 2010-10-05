<div class="mojoblock_edit">

	<div class="block_header">
	
		{icon} <p class="block_title">{block_name} Block</p>
	
	</div><!--block_header-->

	<form class="mojoblocks_form" method="post" id="mb_editor" onsubmit="return false;">

	<div style="width: 100%; overflow: hidden;">

	<table class="mojoblocks_table" cellpadding="0"cellspacing="0">

	{fields}
	
		<tr>
			<td><label for="{slug}">{label}</label></td>
			<td>{error} {input}</td>
		</tr>
	
	{/fields}
	
	</table>
	
	<input type="hidden" name="layout_id" id="layout_id" value="{layout_id}" />
	<input type="hidden" name="page_url_title" id="page_url_title" value="{page_url_title}" />
	<input type="hidden" name="region_id" id="region_id" value="{region_id}" />
	<input type="hidden" name="block_type" id="block_type" value="{block_type}" />
	<input type="hidden" name="row_id" id="row_id" value="{row_id}" />
	<input type="hidden" name="region_class" id="region_class" value="{region_class}" />
	
	{hidden_inputs}
	
	</div>
	
	<p class="actions">
		
		<a id="submit_block_form" class="{region_id}"><img src="<?php echo base_url().SYSDIR;?>/mojomotor/third_party/mb/views/themes/images/submit_button.gif" alt="Submit" /></a>
		<a id="cancel_block_editor" class="{region_id}"><img src="<?php echo base_url().SYSDIR;?>/mojomotor/third_party/mb/views/themes/images/cancel_button.gif" alt="Cancel" /></a>
		
	</p>

	</form>

</div>