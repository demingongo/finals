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
		<h1 class="page-header">
        Users Requests
        </h1>
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
    	<button type="submit" name="submit[]" class="btn btn-outline btn-primary itemAction to-xs" value="edit">
		<span class="fa fa-edit"></span> Details
		</button>
    	<button type="submit" name="submit[]" class="btn btn-outline btn-warning itemAction to-xs" value="cancel">
		 Cancel
        </button>
        <button type="submit" name="submit[]" id="cancelAll" class="btn btn-outline btn-danger to-xs" data-novice-toggle="confirm" 
		 data-toggle="tooltip" data-placement="top" data-original-title="Cancel all" value="cancelAll">
		<span class="fa fa-trash"></span> Cancel all
		</button>
	</div>
</div>
<div class="col-md-4 pull-right subhead-collapse">
	<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-sort">Sort 
        <span class="glyphicon glyphicon-sort"></span>
        </a>
	</div>
	<div id="collapse-sort" class="col-lg-7 collapse subhead-collapse pull-right">
    	<div data-toggle="tooltip" data-placement="left" data-original-title="Search username :" style="display: inline-block; padding: 1px;">
		{$searchWidget}
		</div>
		<div data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
		<div data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
		</div>
        <button type="submit" class="btn btn-info btn-sm" name="submit[]" value="search">Search</button>
	</div>
</div>
</div>
<div class="table-responsive">
<table id="tab" class="table table-striped table-hover ">
  <thead>
    <tr>
      <th>
      <input type="checkbox" id="checkAll" name="checkall-toggle" data-novice-toggle="checkall" title="check all" />
      </th>
      <th>#</th>
      <th>User</th>
      <th>Email user</th>
      <th>Created at</th>
    </tr>
  </thead>
  <tbody>
	{foreach name=requests from=$requests item=ureq}
    <tr>
    <td>
	  	<input type="checkbox" id="cb{$smarty.foreach.users.index}" name="cid[]" value="{$ureq.id}" />
	</td>
      <td>
      	<a href="">{$ureq.id}</a>
      </td>
      <td>
		<a href="{path id='rgs_admin_users_edit' params=['id' => $ureq.user.id] absolute=true}">{$ureq.user.login}</a>
	  </td>
      <td>
		<a href="{path id='rgs_admin_users_edit' params=['id' => $ureq.user.id] absolute=true}">{$ureq.user.email}</a>
	  </td>
      <td>
		{$ureq.created_at|date_format:"%Y-%m-%d %H:%M:%S"}
	  </td>
    </tr>
	{/foreach}
  </tbody>
</table>
</div>
{include file='file:[RgsAdminModule]includes/adminFormPagination.tpl'}
</form>
</div>
{/block}