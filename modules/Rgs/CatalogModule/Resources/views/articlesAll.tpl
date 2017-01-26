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

{**********
<div>
<h1>TEST <small>file:[RgsCatalogModule]articlesAll.tpl</small></h1>
{form var="article_attr" method="post" data-toggle="validator" novalidate="true" enctype="multipart/form-data"}
    <div class="form-group">
    <label for="name">{form_error path="name" style="color: red;"}</label>
    {form_input path="name" class="form-control"}
    {/form_input}
    </div>
    <div class="form-group">
    {form_input path="stock" type="number" class="form-control"}
    {/form_input}
    </div>
    <div class="form-group">
    {form_select path="categorie" class="form-control"}
    	{form_options items=$categories itemLabel="name" itemValue="id"}
    	{/form_options}
    {/form_select}
    </div>
    <div class="form-group">
    {form_submit class="form-control btn-success"}
    </div>
{/form}
<p>
</p>
</div>
*********************}

<div class="col-sm-3 hidden-xs">    
</div>
<div class="col-sm-9">
	<div class="col-xs-12 hidden-xs">
    <h3 class="text-nowrap"><b>{$titre} <span style="color:#B8B8B8;">{if $filter}<small>+ filtre</small> {/if}({count($articles)})</span></b></h3>
	</div>
    <h3 class="text-nowrap visible-xs">
    	<b>{$titre} <span style="color:#B8B8B8;">{if $filter}<small>+ filtre</small> {/if}({count($articles)})</span></b>
    </h3>
</div>

{include file="menu_filtre.tpl"}

<div class="col-sm-9">

<div class="col-xs-12">
	<div class="pull-right">
		{pagination paginator=$articles max="4" queryStrict=['categorie', 'etat'] noQuery=false}
    </div>
    
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
{foreach $articles as $a}
<article class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
<div class="panel panel-default" style="min-height: 460px;" > <!-- style="height: 470px;" -->
<div class="panel-body" style="height: 240px;">
	<div class="row text-center">
    	<h4>
			<a href="#">{$a.name}</a>
		</h4>
    </div>
    <div class="row text-center">
    	<!--<div class="col-md-3 col-sm-3">-->
        <div>
        	<a href="#">
    			<img src="{image_src path=$a.image package=upload}" class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" title="{if !empty($a.teaser)}        
        	{$a.teaser|purify} 
        {else}
        	{$a.description|purify|truncate:60:'...':true}       
        {/if}" />
            </a>
    	</div>
    </div>
        
        <!--<div class="hidden-xs col-sm-8 col-md-8 col-sm-offset-1 col-md-offset-1">-->
        <div class="row text-center">
        {if !empty($a.teaser)}        
        	{$a.teaser|purify} 
        {else}
        	{$a.description|purify|truncate:60:'...':true}       
        {/if}
        </div>
        	
    	<!--<div class="clearfix visible-sm-block"></div>-->
    </div>
   		<!--<div class="col-md-12">
    		<div class="row">-->
        		<!--<aside class="col-lg-12">-->
                {*****************************
            		<table class="table table-bordered table-condensed"> <!-- table-condensed-->
                		<tbody>
                    		<tr>
                        		<th>Categorie</th><td title="{$a.categorie.name}">{$a.categorie.name|truncate:15:'...':true}</td>
	                        </tr>
                            <tr>
        	                	<th>Etat</th><td>{$a.etat.name}</td>
            	            </tr>
    	                    <tr>
        	                	<th>Prix</th>
                                <td>
                                	{if !empty($a.prix) && $a.prix gt 0}
                                    	&euro;&nbsp;{$a.prix}
                                    {else}
                                    	<a href="#contact"><small>Contactez-nous</small></a>
                                    {/if}
                                </td>
            	            </tr>
                	        <tr>
                    	    	<th>Stock</th>
                                <td>
                                	{if !empty($a.stock) && $a.stock gt 0}
                                		{$a.stock}
                                    {else}
                                    	<small class="text-danger">Hors stock</small>
                                    {/if}
                                </td>
                        	</tr>
	                    </tbody>
    	            </table>
                    *******************}
        	    <!--</aside>-->
                <!--<aside class="col-lg-12">
                	<form method="post">
                    	<input type="hidden" name="id_article" value="{$a.id}" />
                        <button type="submit" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-shopping-cart"></span>Reserver</button>
                    </form>
                </aside>-->
	        <!--</div>
    	</div>-->
        <div class="panel-footer">
        <table class="table table-bordered table-condensed"> <!-- table-condensed-->
                		<tbody>
                    		<tr>
                        		<th>Categorie</th><td title="{$a.categorie.name}">{$a.categorie.name|truncate:15:'...':true}</td>
	                        </tr>
                            <tr>
        	                	<th>Etat</th><td>{$a.etat.name}</td>
            	            </tr>
    	                    <tr>
        	                	<th>Prix</th>
                                <td>
                                	{if !empty($a.prix) && $a.prix gt 0}
                                    	&euro;&nbsp;{$a.prix}
                                    {else}
                                    	<a href="#contact"><small>Contactez-nous</small></a>
                                    {/if}
                                </td>
            	            </tr>
                	        <tr>
                    	    	<th>Stock</th>
                                <td>
                                	{if !empty($a.stock) && $a.stock gt 0}
                                		<small class="text-success">Disponible</small>
                                    {else}
                                    	<small class="text-danger">Hors stock</small>
                                    {/if}
                                </td>
                        	</tr>
	                    </tbody>
    	            </table>
        <form method="post" action="{path id='rgs_catalog_caddie_add'}">
        	<input type="hidden" name="id" value="{$a.id}" />
            {if $session->isAuthenticated() && $app.user.data->hasRole("ROLE_SUPER_ADMIN")}
        	<a class="btn btn-xs btn-warning" href="#"> edit </a>
    		{/if}
            <button type="submit" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-shopping-cart"></span> Panier</button>
        </form>
        </div>
	</div>
</article>
{if $a@last}
<!--&nbsp;
<hr />-->
{/if}
{/foreach}
</div>
<div class="col-xs-12">
	<div class="pull-right">
		{pagination paginator=$articles max="4" queryStrict=['categorie', 'etat'] noQuery=false}
    </div>
    
</div>
</div>
{/block}
