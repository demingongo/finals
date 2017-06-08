{* file:[RgsCatalogModule]articlesAll.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}

{block  name=section}

{include file="menu_filtre.tpl"}

<div class="col-sm-9">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<article class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="panel panel-default" style="min-height: 460px;" > <!-- style="height: 470px;" -->
<div class="panel-body" style="height: 240px;">
	<div class="row text-center">
    	<h1>
			<a href="#">{$article.name}</a>
		</h1>
    </div>
	<div>
		{img src=$article.image package=upload class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" title=$article.name}
	</div>
        	
        <form method="post" action="{path id='rgs_catalog_caddie_add'}">
        	<input type="hidden" name="id" value="{$article.id}" />
            {auth permissions=ROLE_ADMIN}
        	<a class="btn btn-xs btn-warning" href="#"> edit </a>
    		{/auth}
            <button type="submit" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-shopping-cart"></span> Panier</button>
        </form>
</div>
</div>
</article>
</div>

</div>
{/block}
