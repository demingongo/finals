{extends file='file:[RgsAdminModule]layout.php'}

{block name=page-wrapper}
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Brand: Edit</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<div class="col-xs-10 col-sm-8 col-md-6">
	<form data-toggle="validator" lang={#lang#} {*data-novice='form-control'*} method="post" {*class="form-inline"*}>

		{form_build_widget form=$form}

		{* begin submit *}
		<div class="row form-group col-md-12">
			<input type="submit" id="_submit" name="_submit" class="btn btn-primary" value="Edit" />
		</div>
		{* end submit *}
	</form>
</div>
</div>
{/block}