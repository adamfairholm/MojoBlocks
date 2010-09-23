mojoBlocks = {
	allow_editor_init: true,
	are_open: true,
	are_active: false,
	region: "",
	fallback_contents: []
};

// Scoopin' up all the content and boppin' 'em on the head

function is_numeric(input)
{
   return (input - 0) == input && input.length > 0;
}

mojoBlocks.gather_contents = function ()
{
	jQuery(".mojoblock_region").each(function () {

		r_id = jQuery(this).attr('id');
		mojoBlocks.fallback_contents[r_id] = jQuery('#'+r_id).html();

	});
}

// Click to start the editor

jQuery(".mojoblock_region").click(function () {

	if (mojoBlocks.allow_editor_init)
	{
		mojoBlocks.allow_editor_init = false;
		mojoBlocks.init_editor(this);
	}
});

// Enable the mojoblock regions

mojoBlocks.enable_block_regions = function ()
{
	jQuery(".mojoblock_region").each(function () {

		// Add in the editable stuff
	
		block_editable = jQuery("<div class='mojoblock_editable_layer'></div>").css({opacity: '0.4', width: jQuery(this).width(), height: jQuery(this).outerHeight()}).fadeIn('fast');
		jQuery(this).prepend(jQuery("<div class='mojo_editable_layer_header'><p>"+$(this).attr('name')+"</p></div>")).prepend(block_editable);

	});
};


// Once the page loads, load up the cache and enable the regions

mojoBlocks.gather_contents();

mojoBlocks.enable_block_regions();
	
// Power down our blocks

mojoBlocks.deactivate_blocks = function()
{
	jQuery(".mojoblock_region").each(function () {

		// Remove the mojoblock_editable_layer class
		
		jQuery(this).removeClass('mojoblock_editable_layer');
		
	});
}

// Fire up the editor

mojoBlocks.init_editor = function (region) {

	// Need to remove all other regions
	
	mojoBlocks.deactivate_blocks();
	
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

}


// Submit the editor form

function mb_form_submit(region_id)
{
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
					
						success: function(new_data){ $('#'+region_id).html(new_data); mojoBlocks.enable_block_regions(); }
					
					})
			}
		 }
	});
}

// Cancel the editor

function mb_form_cancel(region_id)
{	
	$('#'+region_id).empty();

	jQuery('#'+region_id).html(mojoBlocks.fallback_contents[region_id]);

	mojoBlocks.enable_block_regions();

	mojoBlocks.allow_editor_init = true;
}
