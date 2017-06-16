{extends file='file:[RgsAdminModule]layout.php'}

{block name=page-wrapper}
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<h1 class="page-header">{'User request'|trans}: Details</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
	<form data-toggle="validator" lang={#lang#} {*data-novice='form-control'*} method="post" {*class="form-inline"*}>
    
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
				<th>
					{'From'|trans}:
				</th>
				<td>
					<a href="{path id='rgs_admin_users_edit' params=['id' => $item.user.id] absolute=true}">{$item.user.login}</a>
        <small>({$item.user.email})</small>
				</td>
			</tr>
			<tr>
				<th>
					{'Subject'|trans}:
				</th>
				<td>
					{$item.subject}
				</td>
			</tr>
			<tr>
				<th>
					{'Image'|trans}:
				</th>
				<td>
					<a href="{image_src path=$item.image package=upload}" target="_blank">
					{img src=$item.image package=upload style="max-height: 200px;"}
					</a>
				</td>
			</tr>
			<tr>
				<th>
					{'Description'|trans}:
				</th>
				<td>
					{$item.description}
				</td>
			</tr>
		</table>
	</div>


		{* begin submit *}
		
		<div class="row form-group col-md-12">
			{if !$item.hasStatus}
			<button type="submit" id="_submit" name="submit[]" class="btn btn-warning pull-right" value="decline" title="Decline" >
                {'Decline'|trans}
            </button>
            <button type="submit" id="_submit" name="submit[]" class="btn btn-primary pull-right" value="accept" title="Accept" >
                {'Accept'|trans}
            </button>
			{else}
				{if $item.status}
					<span class="text-success pull-right" style="font-weight: bolder;">
					{'Accepted'|trans}
					</span>
				{else}
					<span class="text-danger pull-right" style="font-weight: bolder;">
					{'Declined'|trans}
					</span>
				{/if}
			{/if}
		</div>
		
		{* end submit *}
	</form>
</div>
</div>
{/block}