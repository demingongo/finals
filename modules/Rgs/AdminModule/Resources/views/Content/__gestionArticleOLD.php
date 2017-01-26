{extends file='file:[RgsAdminModule]layout.php'}

{block name=javascript}
{$smarty.block.parent}
<script>

function formTache(tache, id){
		$('#adminForm input[type=checkbox]').each(function(){
			$(this).prop("checked", false);
		});
	
		$('#adminForm #'+id).prop("checked", true);
}

$("#adminForm #delete").click(
	function(e){
		var atLeastOneIsChecked = $('#adminForm input[name="cid[]"]:checked').length > 0;
		if(!atLeastOneIsChecked){
			//alert('Select an item');
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
		<h1 class="page-header">Articles</h1>
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
	Continue ?" value="delete">Effacer</button>
	<!--<input type="submit" class="btn btn-outline btn-success" name="submit[]" onclick="" value="add.new" />
	<input type="submit" class="btn btn-outline btn-primary itemAction" name="submit[]" onclick="" value="edit" />
	<input type="submit" class="btn btn-outline btn-default itemAction" name="submit[]" onclick="" value="publish" />
	<input type="submit" class="btn btn-outline btn-default itemAction" name="submit[]" onclick="" value="unpublish" />
	<input type="submit" id="delete" class="btn btn-outline btn-danger itemAction" data-novice-toggle="confirm" 
		data-novice-text="This action can not be undone. 
	Continue ?" 
	title="Delete" name="submit[]" onclick="" value="delete" />-->
	<button type="button" class="btn btn-outline btn-link">Link</button>
</div>
<div class="col-md-4 pull-right">
	<div id="note" class="pull-right">
		<div data-toggle="tooltip" data-placement="left" data-original-title="Categorie :" style="display: block;">
		{$catField}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Visibilité :" style="display: block;">
		{$visibilityField}
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
	{foreach name=items from=$items item=itm}
    <tr>
      <td>{$itm.id}</td>
      <td>
		<input type="checkbox" id="cb{$smarty.foreach.items.index}" name="cid[]" value="{$itm.id}" />
	  </td>
      <td>
		<!--<a href="javascript:void(0);" name="submit[]" onclick="formTache('cb{$smarty.foreach.items.index}')" value="publish">-->
		<!--<input type="submit" class="btn btn-outline btn-default" name="submit[]" onclick="" value="publish" />-->
		{if $itm.isPublished}
			{$publishValue='unpublish'}
		{else}
			{$publishValue='publish'}
		{/if}
		<input type="image" src="{statut statut=$itm.isPublished srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('{$publishValue}','cb{$smarty.foreach.items.index}')" value="{$publishValue}" />
			{*statut statut=$itm.isPublished srconly=false*}
	  </td>
      <td><a href="{path id='rgs_admin_articles_edit' params=['id' => $itm.id, 'slug' => $itm.slug] absolute=true}">{$itm.name}</a></td>
    </tr>
	{/foreach}
  </tbody>
</table>
</form>
</div>
{/block}