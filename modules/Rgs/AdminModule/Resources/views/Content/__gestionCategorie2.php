{extends file='file:[RgsAdminModule]layout.php'}

{block name=javascript}
{$smarty.block.parent}
<script>

function formTache(tache, id){
	//$("#note").text("formTache").show();

  //$("#adminForm").submit();
  //alert(id);
	//if(tache == 'publish' || tache == 'unpublish'){
		$('#adminForm input[type=checkbox]').each(function(){
			$(this).prop("checked", false);
		});

		//$('#adminForm').append('<input type="hidden" name="'+$('#adminForm #'+id).attr("name")+'" value="'+$('#adminForm #'+id).val()+'" >');

		//$('#adminForm').append('<input type="hidden" name="submit[]" value="'+tache+'" />');
	
		$('#adminForm #'+id).prop("checked", true);
	//}
}

$("#adminForm #delete").click(
	function(e){
		var atLeastOneIsChecked = $('#adminForm input[name="cid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			//alert('Select a categorie');
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
{/block}

{block name=sectionClass}col-sm-12{/block}

{block name=page-wrapper}
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Catégories</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<form method="post" id="adminForm" name="adminForm" data-novice='form-control'>
<div class="row">
<div class="col-md-8">
	<button type="submit" name="submit[]" class="btn btn-outline btn-success" value="add.new">Ajouter</button>
	<button type="submit" name="submit[]" class="btn btn-outline btn-primary itemAction" value="edit">Edit</button>
	<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction" value="publish">Publier</button>
	<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction" value="unpublish">Dépublier</button>
	<button type="submit" name="submit[]" id="delete" class="btn btn-outline btn-danger itemAction" data-novice-toggle="confirm" 
		data-novice-text="This action can not be undone. 
	Continue ?" data-toggle="tooltip" data-placement="top" data-original-title="Delete selected categories" value="delete">Effacer</button>
	<button type="button" class="btn btn-outline btn-link">Link</button>
</div>
<div class="col-md-4 pull-right">
	<div id="note" class="pull-right">
		<div data-toggle="tooltip" data-placement="top" data-original-title="Visibilité :" style="display: block;">
		<select name="visibility" class="selectmenu selectmenu-submit" onchange="this.form.submit();">
			<optgroup label="Visibilité :">
		{foreach $visibilityOptions as $opt => $optTxt name=visibility}
			<option value="{$opt}"{if $visibility == $opt} selected="selected"{/if}>{$optTxt}</option>
		{/foreach}
			</optgroup>
		</select>
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block;">
		<select name="ordering" class="selectmenu selectmenu-submit" onchange="this.form.submit();">
		<optgroup label="Tri par :">
		{foreach $orderingOptions as $opt => $optTxt name=orderings}
			<option value="{$opt}"{if $ordering == $opt} selected="selected"{/if}>{$optTxt}</option>
		{/foreach}
		</optgroup>
		</select>
		</div>
		<div data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block;">
		<select name="limit" class="selectmenu selectmenu-submit" onchange="this.form.submit();">
		{foreach name=limits from=$limitOptions item=opt}
			<option value="{$opt}"{if $limit == $opt} selected="selected"{/if}>{$opt}</option>
		{/foreach}
		</select>
		</div>
	</div>
</div>
</div>
<!--<form method="post" id="adminForm" name="adminForm">-->
<table id="tab" class="table table-striped table-hover ">
  <thead>
    <tr>
      <th>#</th>
      <th>
		<input type="checkbox" id="checkAll" name="checkall-toggle" data-novice-toggle="checkall" title="check all" />
	  </th>
      <th>Statut</th>
      <th>Titre</th>
    </tr>
  </thead>
  <tbody>
	{foreach name=categories from=$categories item=cat}
    <tr>
      <td>{$cat.id}</td>
      <td>
		<input type="checkbox" id="cb{$smarty.foreach.categories.index}" name="cid[]" value="{$cat.id}" />
	  </td>
      <td>
		<!--<a href="javascript:void(0);" name="submit[]" onclick="formTache('cb{$smarty.foreach.categories.index}')" value="publish">-->
		<!--<input type="submit" class="btn btn-outline btn-default" name="submit[]" onclick="" value="publish" />-->
		{if $cat.isPublished}
			{$publishValue='unpublish'}
		{else}
			{$publishValue='publish'}
		{/if}
		<input type="image" src="{statut statut=$cat.isPublished srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('{$publishValue}','cb{$smarty.foreach.categories.index}')" value="{$publishValue}" />
			{*statut statut=$cat.isPublished srconly=false*}
	  </td>
      <td><a href="{path id='rgs_admin_categories_edit' params=['id' => $cat.id, 'slug' => $cat.slug] absolute=true}">{$cat.name}</a></td>
    </tr>
	{/foreach}
  </tbody>
</table>
</form>
</div>
{/block}