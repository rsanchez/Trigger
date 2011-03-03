$(document).ready(function()
{
	$('#trigger_content').keypress(function(e) {
	    
	if(e.keyCode == 13) {
	    
	var textarea = $('#trigger_content').val();

	jQuery.ajax({
		dataType: "text",
		type: "POST",
		data: { XID:EE.XID, line:textarea },
		url:  EE.BASE+"&C=addons_modules&M=show_module_cp&module=trigger&method=parse_trigger_output",
		success: function(data){
		
		$('#trigger_content').val($('#trigger_content').val()+data);
		
		var textarea = null;
		
		}
	});

	    }
	});
});

