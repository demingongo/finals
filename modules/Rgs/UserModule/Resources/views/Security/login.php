{* file:[RgsCatalogModule]lostPassword.php *}

{extends file='file:rgs_layout.php'}

{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}


{block  name=section}
<div class="col-sm-5 col-xs-offset-1">
{if $session_flash->has('notice')}
{notification type="error" message=$session_flash->get('notice') sign=true close=false}
{/if}
<form action="" data-toggle="validator" method="post" role="form" {*class="form-inline"*}>

<div class="col-sm-12">
{form_build_widget form=$form}

{* begin submit *}
<div class="row form-group col-md-12">
<input type="submit" id="_submit" name="_submit" class="btn btn-primary" value="{'security.login.submit'|trans:[]:UserModule}" />
</div>
{* end submit *}
</div>
<div class="row form-group text-center col-sm-12">
	<a href="{path id='user_security_lostpassword' absolute=true}">Forgot password ?</a>
</div>

<div class="row form-group text-center col-sm-12">
	Not a memeber yet ? <a href="{path id='user_registration_register' absolute=true}">Register for free</a>
</div>

</form>
</div>
{/block}