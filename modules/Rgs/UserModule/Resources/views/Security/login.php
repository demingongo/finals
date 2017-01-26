{* file:[RgsCatalogModule]login.php *}

{extends file='file:rgs_layout.php'}

{block name=javascript}
{$smarty.block.parent}
<!--<script src="{asset url='/1000hz-bootstrap-validator/0.8.1/validator.min.js' package='cdnjs'}"></script>-->
{/block}


{block  name=section}
<div class="col-sm-5 col-xs-offset-1">
{if $session_flash->has('notice')}
{notification type="error" message=$session_flash->get('notice') sign=true close=false}
{/if}
<form action="" data-toggle="validator" method="post" role="form" {*class="form-inline"*}>

{form_build_widget form=$form}

{* begin submit *}
<div class="row form-group col-md-12">
<input type="submit" id="_submit" name="_submit" class="btn btn-primary" value="{'security.login.submit'|trans:[]:UserModule}" />
</div>
{* end submit *}

</form>
</div>
{/block}