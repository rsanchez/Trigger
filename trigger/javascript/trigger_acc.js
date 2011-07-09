$(document).ready(function()
{
	$('#trigger_acc_content').keypress(function(e) {
	    
	if(e.keyCode == 13) {
	    
	var textarea = $('#trigger_acc_content').val();

	jQuery.ajax({
		dataType: "text",
		type: "POST",
		data: { XID:EE.XID, line:textarea },
		url:  EE.BASE+"&C=addons_modules&M=show_module_cp&module=trigger&method=parse_trigger_output",
		success: function(data){
		
		$('#trigger_acc_content').val($('#trigger_acc_content').val()+data);
        
        $('#trigger_acc_content').scrollTop($('#trigger_acc_content')[0].scrollHeight - $('#trigger_acc_content').height());
		
		var textarea = null;
		
		}
	});

	    }
	});
});

