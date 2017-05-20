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

{include file='file:[RgsAdminModule]includes/managerFormJavascript.html'}
{/block}

{block name=page-wrapper}
<div class="row">
	<div class="col-lg-12">    
		<h1 class="page-header">
        {if isset($title)}
            {$title}
        {/if}
        </h1>
    </div>
</div>
<div class="row">
<form method="post" id="adminForm" name="adminForm" data-novice='form-control' class="managerForm">
<div class="row">

<div class="col-lg-8">
	<div class="col-lg-12 visible-xs">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-tools">Tools <span class="fa fa-wrench"></span>
        </a>
	</div>
	<div id="collapse-tools" class="col-lg-8 collapse subhead-collapse">
		{*****************************************
		<button type="submit" name="submit[]" class="btn btn-outline btn-success to-xs" value="add.new">
        <span class="fa fa-plus-circle"></span> Add
        </button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-primary itemAction to-xs" value="edit">
        <span class="fa fa-edit"></span> Edit
		</button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="publish">
        <span class="fa fa-check-circle text-success"></span> Publish
        </button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-default itemAction to-xs" value="unpublish">
        <span class="fa fa-times-circle text-danger"></span> Unpublish
        </button>
		<button type="submit" name="submit[]" class="btn btn-outline btn-danger itemAction to-xs" data-novice-toggle="confirm" 
			data-novice-text="Delete selected items ?
Warning: This action cannot be undone." value="delete">
		<span class="fa fa-trash"></span> Delete
		</button>
		*********************************************}
		{foreach $toolButtons as $toolButton}
		{$toolButton}
        {/foreach}
	</div>
</div>
<div class="col-md-4 pull-right subhead-collapse">
	<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-sort">Filters <span class="fa fa-filter"> &amp; Sort 
        <span class="glyphicon glyphicon-sort"></span>
        </a>
	</div>
	<div id="collapse-sort" class="col-lg-6 collapse subhead-collapse pull-right">
        {foreach $customWidgets as $customWidget}
		<div data-toggle="tooltip" data-placement="left" data-original-title="{$customWidget@key} :" style="display: block; padding: 1px;">
		{$customWidget}
		</div>
        {/foreach}
        {if isset($visibilityWidget)}
		<div data-toggle="tooltip" data-placement="left" data-original-title="Visibility :" style="display: block; padding: 1px;">
		{$visibilityWidget}
		</div>
        {/if}
        {if isset($orderingWidget)}
		<div data-toggle="tooltip" data-placement="left" data-original-title="Order by :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
        {/if}
        {if isset($limitWidget)}
		<div data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
		</div>
        {/if}
	</div>
</div>

</div>

{* plugin Novice - SmartyBootstrapModule : sb_table *}
{sb_table columns=$columns items=$items management=true}

{include file='file:[RgsAdminModule]includes/managerFormPagination.html'}
</form>
</div>
{/block}