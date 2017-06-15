{* file:[RgsCatalogModule]User/portal.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}

{block name="title" prepend}
{'My account'|trans} | 
{/block}

{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}

{block  name=section}

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 main-title">
    <h1>{'My account'|trans}</h1>
</div>
<hr />

<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
  <ul class="nav nav-pills nav-stacked">
    <li {if $tab == "myprofile"}class="active"{/if}><a data-toggle="pill" href="#myprofile">{'My profile'|trans}</a></li>
    <li {if $tab == "myreservations"}class="active"{/if}><a data-toggle="pill" href="#myreservations">{'My reservations'|trans}</a></li>
  </ul>
</div>
<div  class="col-xs-12 col-sm-12 col-md-9 col-lg-9 tab-content">
	<div id="myprofile" class="tab-pane fade {if $tab == 'myprofile'}in active{/if}">
    	{include file="file:[RgsCatalogModule]User/profile.tpl"}
	</div>
    
    <div id="myreservations" class="tab-pane fade {if $tab == 'myreservations'}in active{/if}">
    	{include file="file:[RgsCatalogModule]User/reservations.tpl"}
    </div>
</div>

{/block}
