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
		<h1 class="page-header">Reservations</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<form method="post" id="adminForm" name="adminForm" data-novice='form-control'>
<div class="row">

<div class="col-lg-8">

</div>
<div class="col-md-4 pull-right subhead-collapse">
	<div class="col-lg-12 visible-xs" style="margin-top: 2px;">
		<a class="btn btn-outline btn-primary btn-block" data-toggle="collapse" data-target="#collapse-sort">Sort 
        <span class="glyphicon glyphicon-sort"></span>
        </a>
	</div>
	<div id="collapse-sort" class="col-lg-7 collapse subhead-collapse pull-right">
		<div data-toggle="tooltip" data-placement="left" data-original-title="Tri par :" style="display: inline-block; padding: 1px;">
		{$orderingWidget}
		</div>
		<div data-toggle="tooltip" data-placement="top" data-original-title="number per page :" style="display: inline-block; padding: 1px;">
		{$limitWidget}
		</div>
	</div>
</div>
</div>
<div class="table-responsive">
<table id="tab" class="table table-striped table-hover ">
  <thead>
    <tr>
      <th>#</th>
      <th>Date de réservation</th>
      <th>Date d'expiration</th>
      <th>User</th>
      <th>Email user</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
	{foreach name=reservations from=$reservations item=resa}
    <tr>
      <td>
      	{$resa.id}
      </td>
      <td>
		{$resa.created_at|date_format:"%Y-%m-%d %H:%M:%S"}
	  </td>
      <td>
		{$resa.expires_at|date_format:"%Y-%m-%d %H:%M:%S"}
	  </td>
      <td>
		<a href="{path id='rgs_admin_users_edit' params=['id' => $resa.user.id] absolute=true}">{$resa.user.login}</a>
	  </td>
      <td>
		<a href="{path id='rgs_admin_users_edit' params=['id' => $resa.user.id] absolute=true}">{$resa.user.email}</a>
	  </td>
      <td>
      	<a href="{path id='rgs_admin_reservations_details' params=['id' => $resa.id] absolute=true}" class="btn btn-info">
        	Details
        </a>
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