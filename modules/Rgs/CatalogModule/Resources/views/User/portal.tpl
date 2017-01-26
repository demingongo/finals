{* file:[RgsCatalogModule]User/portal.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}




{block  name=section}

<h1>{$titre}</h1>
<hr />

<div class="col-md-3">
  <ul class="nav nav-pills nav-stacked">
    <li {if $tab == "myprofile"}class="active"{/if}><a data-toggle="pill" href="#myprofile">My profile</a></li>
    <li {if $tab == "myreservations"}class="active"{/if}><a data-toggle="pill" href="#myreservations">My reservations</a></li>
  </ul>
</div>
<div  class="col-md-9 tab-content">
	<div id="myprofile" class="tab-pane fade {if $tab == 'myprofile'}in active{/if}">
    	{include file="file:[RgsCatalogModule]User/profile.tpl"}
	</div>
    
    <div id="myreservations" class="tab-pane fade {if $tab == 'myreservations'}in active{/if}">
    	{include file="file:[RgsCatalogModule]User/reservations.tpl"}
    </div>
</div>

{/block}
