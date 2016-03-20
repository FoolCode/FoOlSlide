jQuery(document).ready(function() {
	if (jQuery('input.jqslugcb').is(':checked')) {
		jQuery('input.jqslug').removeClass('uneditable-input');
		jQuery('input.jqslugcb').val(1);
	}
	else {
		jQuery('input.jqslugcb').val(0);
	}
	jQuery('input.jqslugcb').click(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('input.jqslug').removeClass('uneditable-input');
			jQuery('input.jqslugcb').val(1);
		}
		else {
			jQuery('input.jqslug').addClass('uneditable-input');
			jQuery('input.jqslugcb').val(0);
		}
	});
});
