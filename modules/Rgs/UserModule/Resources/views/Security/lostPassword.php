{* file:[UserModule]Security/lostPassword.php *}

{extends file='file:rgs_layout.php'}

{block name="title" prepend}
{'layout.lostpassword'|trans:[]:UserModule} | 
{/block}

{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}

{block  name=section}
<div id="header" class="col-xs-12 col-sm-11 col-md-11 col-lg-11 col-sm-offset-1 main-title primary">
	<h1>{'layout.lostpassword'|trans:[]:UserModule}</h1>
</div>


<div class="col-sm-5 col-xs-offset-1">
{if $session_flash->has('notice')}
{notification type="error" message=$session_flash->get('notice') sign=true close=false}
{/if}
<form action="" data-toggle="validator" method="post" role="form" {*class="form-inline"*}>

{form_build_widget form=$form}

{* begin submit *}
<div class="row form-group col-md-12">
<input type="submit" id="_submit" name="_submit" class="btn btn-primary" value="{'security.lostpassword.submit'|trans:[]:UserModule}" />
</div>
{* end submit *}

</form>
</div>
{/block}