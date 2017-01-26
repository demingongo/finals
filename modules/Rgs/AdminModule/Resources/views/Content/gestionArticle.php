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

{include file='file:[RgsAdminModule]includes/adminFormJavaScript.tpl'}
{/block}

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

<div class="col-lg-8">
	<div class="col-lg-12 visible-xs">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-tools">Tools <span class="fa fa-wrench"></span>
        </a>
	</div>
	<div id="collapse-tools" class="col-lg-8 collapse subhead-collapse">
		<button type="submit" name="submit[]" class="btn btn-outline btn-success to-xs" value="add.new">
        <span class="fa fa-plus-circle"></span> Ajouter
        </button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-primary itemAction to-xs" value="edit">
        <span class="fa fa-edit"></span> Edit
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="publish">
        <span class="fa fa-check-circle text-success"></span> Publier
        </button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="unpublish">
        <span class="fa fa-times-circle text-danger"></span> Dépublier
        </button>
		<button type="submit" name="submit[]" id="delete" class="btn btn-outline btn-danger itemAction to-xs" data-novice-toggle="confirm" 
			data-novice-text="This action can not be undone.
Continue ?" value="delete">
		<span class="fa fa-trash"></span> Effacer
		</button>
	</div>
</div>
<div class="col-md-4 pull-right subhead-collapse">
	<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-sort">Filters <span class="fa fa-filter"> &amp; Sort 
        <span class="glyphicon glyphicon-sort"></span>
        </a>
	</div>
	<div id="collapse-sort" class="col-lg-6 collapse subhead-collapse pull-right">
		<div data-toggle="tooltip" data-placement="left" data-original-title="Categorie :" style="display: block; padding: 1px;">
		{$catWidget}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Visibilité :" style="display: block; padding: 1px;">
		{$visibilityWidget}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
		<div data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
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
	  <th class="hidden-xs">Categorie</th>
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
		{if $itm.isPublished}
			{$publishValue='unpublish'}
		{else}
			{$publishValue='publish'}
		{/if}
		<input type="image" src="{statut statut=$itm.isPublished srconly=true}" class="btn btn-outline btn-default" name="submit[]" onclick="formTache('{$publishValue}','cb{$smarty.foreach.items.index}')" value="{$publishValue}" />
			{*statut statut=$itm.isPublished srconly=false*}
	  </td>
      <td><a href="{path id='rgs_admin_articles_edit' params=['id' => $itm.id, 'slug' => $itm.slug] absolute=true}">{$itm.name}</a></td>
	  <td class="hidden-xs"><a href="{path id='rgs_admin_categories_edit' params=['id' => $itm.categorie.id, 'slug' => $itm.categorie.slug] absolute=true}">{$itm.categorie.name}</a></td>
    </tr>
	{/foreach}
  </tbody>
</table>
{include file='file:[RgsAdminModule]includes/adminFormPagination.tpl'}
</form>
</div>
{/block}