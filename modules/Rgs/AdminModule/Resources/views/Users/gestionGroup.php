{extends file='file:[RgsAdminModule]layout.php'}


{block name=stylesheets}
{$smarty.block.parent}
<!-- Novice subhead-collapse CSS -->
<link rel="stylesheet" href="{asset url='css/subhead-collapse.css' package='novice'}" type="text/css">
{/block}


{block name=javascript}
{$smarty.block.parent}

<!-- Novice subhead-collapse JS -->
<script type="text/javascript" src="{asset url='js/subhead-collapse.js' package='novice'}"></script>

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
		var atLeastOneIsChecked = $('#adminForm input[name="gid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			//alert('Select an item');
			e.stopPropagation();
			return false;
		}
	}
);

$(function(){
	var atLeastOneIsChecked = $('#adminForm input[name="gid[]"]:checked').length > 0;
	if(!atLeastOneIsChecked){
		$('#adminForm .itemAction').disable(true);
	}
	else{
		$('#adminForm .itemAction').disable(false);
	}
	$("#adminForm :checkbox").on("change", function(){
		atLeastOneIsChecked = $('#adminForm input[name="gid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			$('#adminForm .itemAction').disable(true);
		}
		else{
			$('#adminForm .itemAction').disable(false);
		}
	});
});

</script>
{/block}

{block name=page-wrapper}
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">User groups</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<form method="post" id="adminForm" name="adminForm" data-novice='form-control'>
<div class="row">

<div class="col-lg-12">
	<div class="col-lg-12 visible-xs">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-tools">Tools <span class="fa fa-wrench"></span></a>
	</div>
	<div id="collapse-tools" class="col-lg-12 collapse subhead-collapse">
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="lock">
		<span class="fa fa-lock text-danger"></span> Bloquer
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="unlock">
		<span class="fa fa-unlock"></span> Débloquer
		</button>
	</div>
</div>

</div>
<div class="table-responsive col-md-8 col-lg-6">
<table id="tab" class="table table-striped table-hover">
  <thead>
    <tr>
      <th>
		<input type="checkbox" id="checkAll" name="checkall-toggle" data-novice-toggle="checkall" title="check all" />
	  </th>
      <th class="text-center">{"group.show.name"|trans:[]:UserModule}</th>
      <th class="text-center">{"user.not_locked"|trans:[]:UserModule}</th>
    </tr>
  </thead>
  <tbody>
	{foreach name=groups from=$groups item=group}
    <tr>
      <td>
	  <input type="checkbox" id="cb{$smarty.foreach.groups.index}" name="gid[]" value="{$group.id}" />
	  </td>
	  <td class="text-center">{$group.name}</td>
      <td class="text-center">
		{if $group.isLocked}
			{$lockValue='unlock'}
		{else}
			{$lockValue='lock'}
		{/if}
		<input type="image" src="{statut statut=!$group.isLocked srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('{$lockValue}','cb{$smarty.foreach.groups.index}')" value="{$lockValue}" {if $group.id == $app.user.data.group.id}disabled{/if} />
			{*statut statut=$group.isLocked srconly=false*}
	  </td> 
    </tr>
	{/foreach}
  </tbody>
</table>
</div>
</form>
</div>
{/block}