<form action="{path id='user_security_login' absolute=true}" data-toggle="validator" method="post" enctype="multipart/form-data" {*accept="image/gif"*} >

{* begin login *}
<div class="row form-group center-block">
	{form_build_widget form=$formModalLogin field='login'}
	<span class="help-block with-errors "></span>
</div>
{* end login *}

{* begin password *}
<div class="row form-group">
	<div class="form-group col-sm-6 has-feedback">
		{form_build_widget form=$formModalLogin field='password'}
		<span class="help-block with-errors ">Minimum 6 caractères</span>
	</div>
</div>
{* end password *}

{* begin remember_me *}
<div class="row form-group center-block">
	{form_build_widget form=$formModalLogin field='remember_me'}
	<span class="help-block with-errors "></span>
</div>
{* end remember_me *}

{* _csrf_token *}
{form_build_widget form=$formModalLogin field='_csrf_token'}

{*form_build_widget form=$formModalLogin*}

{* begin submit *}
<div class="row form-group ">
	<input type="submit" class="btn btn-primary center-block" id="_submit_login" name="_submit" value="{'security.login.submit'|trans:[]:UserModule}" />
</div>
{* end submit *}
<div class="row form-group text-center top40">
	Pas encore membre ? <a href="{path id='user_registration_register' absolute=true}">Inscrivez-vous gratuitement</a>
</div>


</form>