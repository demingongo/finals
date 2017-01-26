{extends file='file:rgs_layout.php'}

{block  name=section}
<div id="header" class="col-sm-11 col-xs-offset-1">
	<h3>{'layout.register'|trans:[]:UserModule}</h3>
</div>

<div class="col-sm-5 col-xs-offset-1">
{if $session_flash->has('notice')}
{notification type="error" message=$session_flash->get('notice') sign=true close=false}
{/if}
<form data-toggle="validator" method="post" {*class="form-inline"*}>

{* begin login *}
<div class="row form-group col-sm-8">
	{form_build_widget form=$form field='login'}
	<span class="help-block with-errors "></span>
</div>
{* end login *}

{* begin email *}
<div class="row form-group col-sm-8{*col-xs-offset-0*}">
	{form_build_widget form=$form field='email'}
	<span class="help-block with-errors "></span>
</div>
{* end email *}

{* begin password *}
<div class="row form-group">
	<div class="form-group has-feedback col-sm-6">
		{form_build_widget form=$form field='password'}
		<span class="help-block with-errors"></span>
	</div>
	<div class="form-group has-feedback col-sm-6">
		{form_build_widget form=$form field='confirm' }
		<span class="help-block with-errors"></span>
	</div>
</div>
{* end password *}

{* begin securimage *}
<div class="row form-group col-xs-offset-0">
	{form_build_widget form=$form field='_captcha_code'}
</div>
{* end securimage *}

{* _csrf_token *}
{form_build_widget form=$form field='_csrf_token'}

{*form_build_widget form=$form*}

{* begin submit *}
<div class="row form-group col-md-12">
{form_submit id="_submit" name="_submit" class="btn btn-primary" value="{'registration.submit'|trans:[]:UserModule}"}
{**********************
<input type="submit" id="_submit" name="_submit" class="btn btn-primary" value="{'registration.submit'|trans:[]:UserModule}" />
***********************}
</div>
{* end submit *}

</form>
</div>
{/block}