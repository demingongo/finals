<script>

function formTache(tache, id){
		$('#adminForm input[type=checkbox]').each(function(){
			$(this).prop("checked", false);
		});
	
		$('#adminForm #'+id).prop("checked", true);
}

function adminFormSubmit(){
	$("#adminForm [name='submit[]']").prop("disabled", true);
	$('#adminForm').submit();
}

$("#adminForm #delete").click(
	function(e){
		var atLeastOneIsChecked = $('#adminForm input[name="cid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			e.stopPropagation();
			return false;
		}
	}
);

$(function(){
	var atLeastOneIsChecked = $('#adminForm input[name="cid[]"]:checked').length > 0;
	if(!atLeastOneIsChecked){
		$('#adminForm .itemAction').disable(true);
	}
	else{
		$('#adminForm .itemAction').disable(false);
	}
	$("#adminForm :checkbox").on("change", function(){
		atLeastOneIsChecked = $('#adminForm input[name="cid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			$('#adminForm .itemAction').disable(true);
		}
		else{
			$('#adminForm .itemAction').disable(false);
		}
	});
});

</script>