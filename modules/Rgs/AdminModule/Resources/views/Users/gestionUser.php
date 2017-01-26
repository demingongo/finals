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
		var atLeastOneIsChecked = $('#adminForm input[name="uid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			//alert('Select an item');
			e.stopPropagation();
			return false;
		}
	}
);

$(function(){
	var atLeastOneIsChecked = $('#adminForm input[name="uid[]"]:checked').length > 0;
	if(!atLeastOneIsChecked){
		$('#adminForm .itemAction').disable(true);
	}
	else{
		$('#adminForm .itemAction').disable(false);
	}
	$("#adminForm :checkbox").on("change", function(){
		atLeastOneIsChecked = $('#adminForm input[name="uid[]"]:checked').length > 0;
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
		<h1 class="page-header">Users</h1>
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
		<button type="submit" name="submit[]" class="btn btn-outline btn-success to-xs" value="add.new">
		<span class="fa fa-plus-circle"></span> Ajouter
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-primary itemAction to-xs" value="edit">
		<span class="fa fa-edit"></span> Edit
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="lock">
		<span class="fa fa-lock text-danger"></span> Bloquer
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="unlock">
		<span class="fa fa-unlock"></span> Débloquer
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="activate">
		<span class="fa fa-check-circle text-success"></span> Activer
		</button>
		<button type="submit" name="submit[]" id="delete" class="btn btn-outline btn-danger itemAction to-xs" data-novice-toggle="confirm" 
		data-novice-text="This action can not be undone.
Continue ?" value="delete">
		<span class="fa fa-trash"></span> Effacer
		</button>
	</div>
</div>
<div class="col-md-9">
	<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-filter">Filters <span class="fa fa-filter"></span></a>
	</div>
	<div id="collapse-filter" class="col-lg-12 collapse subhead-collapse">
		<div class="col-sm-3" data-toggle="tooltip" data-placement="left" data-original-title="User group :" style="display: block; padding: 1px;">
		{$groupWidget}
		</div>
		<div class="col-sm-3" data-toggle="tooltip" data-placement="left" data-original-title="Visibilité :" style="display: block; padding: 1px;">
		{$visibilityWidget}
		</div>
		<div class="col-sm-3" style="display: block; padding: 1px;">
		{$activatedWidget}
		</div>
		<div class="col-sm-3" style="display: block; padding: 1px;">
		{$createdAtWidget}
		</div>
	</div>
</div>

<div class="col-md-3 pull-right subhead-collapse">
<!--<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-filter">Filters <span class="fa fa-filter"></span></a>
</div>
	<div id="collapse-filter" class="col-lg-12 collapse subhead-collapse">
		<div data-toggle="tooltip" data-placement="left" data-original-title="User group :" style="display: block; padding: 1px;">
		{$groupWidget}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Visibilité :" style="display: block; padding: 1px;">
		{$visibilityWidget}
		</div>
		<div style="display: block; padding: 1px;">
		{$activatedWidget}
		</div>
		<div style="display: block; padding: 1px;">
		{$createdAtWidget}
		</div>
	</div>-->
<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-sort">Sort <span class="glyphicon glyphicon-sort"></span></a>
</div>
	<div id="collapse-sort" class="col-lg-12 collapse subhead-collapse">
		<div class="col-xs-8" data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
		<div class="col-xs-4" data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
		</div>
	</div>
</div>

<!--<div id="collapse-filter" class="col-md-3 collapse subhead-collapse pull-right">
	<div class="">
		<div data-toggle="tooltip" data-placement="left" data-original-title="User group :" style="display: block; padding: 1px;">
		{$groupWidget}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Visibilité :" style="display: block; padding: 1px;">
		{$visibilityWidget}
		</div>
		<div style="display: block; padding: 1px;">
		{$activatedWidget}
		</div>
		<div style="display: block; padding: 1px;">
		{$createdAtWidget}
		</div>
		<div class="col-xs-8" data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
		<div class="col-xs-4" data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
		</div>
	</div>
</div>-->

</div>
<div class="table-responsive">
<table id="tab" class="table table-striped table-hover">
  <thead>
    <tr>
      <th>
		<input type="checkbox" id="checkAll" name="checkall-toggle" data-novice-toggle="checkall" title="check all" />
	  </th>
      <th class="text-center">{"user.login"|trans:[]:UserModule}</th>
      <th class="text-center">{"user.not_locked"|trans:[]:UserModule}</th>
	  <th class="text-center">{"user.activated"|trans:[]:UserModule}</th>
      <th class="hidden-xs text-center">{"user.group"|trans:[]:UserModule}</th>
	  <th class="text-center">{"user.email"|trans:[]:UserModule}</th>
	  <th class="text-center">{"user.last_login"|trans:[]:UserModule}</th>
	  <th class="text-center">{"user.created_at"|trans:[]:UserModule}</th>
	  <th>#</th>
    </tr>
  </thead>
  <tbody>
	{foreach name=users from=$users item=usr}
    <tr>
      <td>
	  <input type="checkbox" id="cb{$smarty.foreach.users.index}" name="uid[]" value="{$usr.id}" />
	  </td>
	  <td class="text-center"><a href="{path id='rgs_admin_users_edit' params=['id' => $usr.id] absolute=true}">{$usr.login}</a></td>
      <td class="text-center">
		<!--<a href="javascript:void(0);" name="submit[]" onclick="formTache('cb{$smarty.foreach.items.index}')" value="publish">-->
		<!--<input type="submit" class="btn btn-outline btn-default" name="submit[]" onclick="" value="publish" />-->
		{if $usr.isLocked}
			{$lockValue='unlock'}
		{else}
			{$lockValue='lock'}
		{/if}
		<input type="image" src="{statut statut=!$usr.isLocked srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('{$lockValue}','cb{$smarty.foreach.users.index}')" value="{$lockValue}" {if $usr.id == $app.user.data.id}disabled{/if} />
			{*statut statut=$usr.isLocked srconly=false*}
	  </td>    
	  <td class="text-center">
		{if $usr.isActivated}
			{$aTitle='Activé'}
			<input type="image" src="{statut statut=$usr.isActivated srconly=true}" class="btn btn-outline btn-default" title="{$aTitle}" disabled />
		{else}
			{$aTitle='Désactivé'}
			<input type="image" src="{statut statut=$usr.isActivated srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('activate','cb{$smarty.foreach.users.index}')" title="Activer cet utilisateur" value="activate" />
		{/if}
	  </td>
	  <td class="hidden-xs text-center">{$usr.group.name}</td>
	  <td class="text-center">{$usr.email}</td>
	  <td class="text-center">
	  {if $usr.last_login == null}
		Jamais
	  {else}
		{$usr.last_login|date_format:"%Y-%m-%d %H:%M:%S"}
	  {/if}
	  </td>
	  <td class="text-center">{$usr.created_at|date_format:"%Y-%m-%d %H:%M:%S"}</td>
	  <td>{$usr.id}</td>
    </tr>
	{/foreach}
  </tbody>
</table>
</div>
{include file='file:[RgsAdminModule]includes/adminFormPagination.tpl'}
</form>
</div>
{/block}