mojoBlock = {
	is_open: true,
	is_active: false,
	region: "",
	original_contents: "" // tracks region data, and is used to "cancel"
};

mojoBlock.enable_block_regions = function ()
{
	jQuery(".mojoblock_region").each(function () {

		//var bubble_name = jQuery(this).attr('id').replace("_", " ");

		jQuery(this).prepend(jQuery("<div class='mojoblock_editable_layer'></div>").css({opacity: '0.4', width: jQuery(this).width(), height: jQuery(this).outerHeight()}).fadeIn('fast'));
	});
};

jQuery(".mojoblock_region").click(function () {
	if (mojoBlock.is_open && mojoBlock.is_active === false)
	{
		mojoBlock.is_active = true;
		mojoBlock.init_editor(this);
	}
});

mojoBlock.enable_block_regions();

mojoBlock.init_editor = function (region) {

	region_id = jQuery(region).attr('id');

	mojoBlock.region = region_id;
	
	//Replace MB Region with editor
	
	jQuery.ajax({
		dataType: "text",
		type: "POST",
		data:  mojoBlock.region,
		url:   Mojo.URL.site_path+"/addons/mb/editor/"+region_id,
		success: function(data){ $('#'+region_id).html(data); }
	});

}
