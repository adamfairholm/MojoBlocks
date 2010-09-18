<form method="post" id="mb_editor" onsubmit="return false;">

	{fields}
	
		<p><label for="{slug}">{label}</label> {input}</p>
	
	{/fields}
	
	<input type="hidden" name="layout_id" id="layout_id" value="{layout_id}" />
	
	<p><button id="submit_button" onclick="mb_form_submit();">Submit</button></p>

</form>