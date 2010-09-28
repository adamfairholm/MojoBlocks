// -------------------------------------
// MojoBlocks jQuery
// -------------------------------------

jQuery(document).ready(function()
{
	// If the editor is open, then why not go ahead
	// and start the party
	
	if( mojoEditor.is_open == true )
	{
		mojoBlocks.setup_blocks();
	} 

});