<form class="mojoblocks_form" method="post" id="mb_editor" onsubmit="return false;">

	<h2>{icon} {block_name}</h2>

	{fields}
	
		{error}
	
		<p><label for="{slug}">{label}</label> {input}</p>
	
	{/fields}
	
	<input type="hidden" name="layout_id" id="layout_id" value="{layout_id}" />
	<input type="hidden" name="page_url_title" id="page_url_title" value="{page_url_title}" />
	<input type="hidden" name="region_id" id="region_id" value="{region_id}" />
	<input type="hidden" name="block_type" id="block_type" value="{block_type}" />
	
	<p><button id="submit_button" onclick="mb_form_submit();">Submit</button></p>
	
	<p><a onclick="mb_form_cancel('{region_id}');">Cancel</a></p>

</form>