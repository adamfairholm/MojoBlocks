mojoBlocks = {
	allow_editor_init: true,
	fallback_contents: []
};

// -------------------------------------
// Setup the blocks
// -------------------------------------

mojoBlocks.setup_blocks = function()
{
	// Gather the contents for use later
	mojoBlocks.gather_contents();

	// Enable the regions for clicking
	mojoBlocks.enable_block_regions();

	// Set the regions to be clickable
	jQuery(".mojoblock_region").click(function ()
	{
		if (mojoBlocks.allow_editor_init == true)
		{
			mojoBlocks.allow_editor_init = false;
		
			mojoBlocks.init_editor(this);
		}
	});

	// Submit button for editor
	$('#submit_block_form').live('click', function() {
	
		mojoBlocks.submit_blocks_form( jQuery(this).attr('class') );
	
	});
	
	// Cancel button for editor
	$('#cancel_block_editor').live('click', function() {
	
		mojoBlocks.cancel_editor( jQuery(this).attr('class') );
	
	});
}

// -------------------------------------
// Enable the mojoblock regions
// -------------------------------------

mojoBlocks.enable_block_regions = function ()
{
	jQuery(".mojoblock_region").each(function () {

		// Add in the editable stuff
	
		block_editable = jQuery("<div class='mojoblock_editable_layer'></div>").css({opacity: '0.4', width: jQuery(this).width(), height: jQuery(this).outerHeight()}).fadeIn('slow');
		jQuery(this).prepend(jQuery("<div class='mojo_editable_layer_header'><p>"+$(this).attr('name')+"</p></div>")).prepend(block_editable);

	});
};

// -------------------------------------
// Is input numerical?
// -------------------------------------

function is_numeric(input)
{
   return (input - 0) == input && input.length > 0;
}

// -------------------------------------
// Gather data from regions
// -------------------------------------

mojoBlocks.gather_contents = function ()
{
	jQuery(".mojoblock_region").each(function () {

		r_id = jQuery(this).attr('id');
		mojoBlocks.fallback_contents[r_id] = jQuery('#'+r_id).html();

	});
}

// -------------------------------------
// Initialize the editor
// -------------------------------------

mojoBlocks.init_editor = function (region) {

	// Need to remove all other regions
	mojoBlocks.disable_block_regions();
	
	// Get some data
	region_id = jQuery(region).attr('id');
	block_type = jQuery(region).attr('name');	
	mojoBlocks.region = region_id;
	layout_id = Mojo.Vars.layout_id;
	
	//Get rid of the stuff we just put in there when we activated the mojoblock regions
	$('#'+region_id).empty();
	
	// Replace MB Region with editor
	jQuery.ajax({
		dataType: "text",
		type: "POST",
		data: Mojo.Vars.csrf_token+'='+Mojo.Vars.csrf+'&layout_id='+layout_id+'&region_id='+region_id+'&page_url_title='+Mojo.Vars.page_url_title+'&block_type='+block_type,
		url:  Mojo.URL.site_path+"/addons/mb/editor",
		success: function(data){ $('#'+region_id).html(data); }
	});

	mojoBlocks.allow_editor_init = false;
}

// -------------------------------------
// Disable block regions
// -------------------------------------

mojoBlocks.disable_block_regions = function ()
{	
	$('.mojoblock_editable_layer').remove();
	$('.mojo_editable_layer_header').remove();
};

// -------------------------------------
// Remove the editor and cancel
// -------------------------------------

mojoBlocks.cancel_editor = function (region_id) {

	// Clear the region
	$('#'+region_id).empty();

	// Add the fallback content
	jQuery('#'+region_id).html(mojoBlocks.fallback_contents[region_id]);
	
	jQuery('#'+region_id).removeClass('editor_activated');

	mojoBlocks.enable_block_regions();

	mojoBlocks.allow_editor_init = true;
};

// -------------------------------------
// Submit the form
// -------------------------------------

mojoBlocks.submit_blocks_form = function (region_id) {

	// Let's go through all the inputs.

	var $inputs = $('form#mb_editor :input');
    
    var data_string = '';
    
    $inputs.each(function() {
        data_string += this.name+'='+$(this).val()+'&';
    });
    
    data_string += Mojo.Vars.csrf_token+'='+Mojo.Vars.csrf+'&form_submit=true';

	// Send out the request

	jQuery.ajax({
		dataType: "text",
		type: "POST",
		data: data_string,
		url:  Mojo.URL.site_path+"/addons/mb/editor",
		success: function(return_data){ 

			// Grab the new content
			
			if( return_data == 'BLOCKS_FORM_INPUT_FAILURE' )
			{
				alert('an_error');
			}
			else if( is_numeric(return_data) == false )
			{
				$('#'+region_id).html(return_data);
			}
			else
			{			
				ajax_block_data = 'block_id='+return_data+'&'+Mojo.Vars.csrf_token+'='+Mojo.Vars.csrf;
			
				jQuery.ajax({
					dataType: "text",
					type: "POST",
					data: ajax_block_data,
					url:  Mojo.URL.site_path+"/addons/mb/ajax_block",
					
						success: function(new_data){ 
						
							$('#'+region_id).html(new_data); 
							
							mojoBlocks.enable_block_regions(); 
						
							mojoBlocks.allow_editor_init = true;
		
						}
					
					})
			}
		 }
	});
}