{extends file='file:rgs_layout.php'}

{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}

{block  name=section}
{*$smarty.block.parent*}
<fieldset>
{if $session_flash->has('notice')}
{notification type="info" message=$session_flash->get('notice') sign=true close=false}
{/if}
<p>{*$message*}</p>
<form method="get" action="{path id='rgs_catalog_index'}">
<input type="submit" class="btn btn-primary center-block" value="Continuer">
</form>
</fieldset>

{/block}
