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
    {form_select path="category" class="form-control"}
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
    <h3 class="text-nowrap"><b>{$titre} <span style="color:#B8B8B8;">{if $filter}<small>+ filtre</small> {/if}({count($paginator)})</span></b></h3>
	</div>
    <h3 class="text-nowrap visible-xs">
    	<b>{$titre} <span style="color:#B8B8B8;">{if $filter}<small>+ filtre</small> {/if}({count($paginator)})</span></b>
    </h3>
</div>

{include file="menu_filtre.tpl"}

<div class="col-sm-9">

<div class="col-xs-12">
	<div class="pull-right">
		{pagination paginator=$paginator max="4" queryStrict=['category', 'state'] noQuery=false}
    </div>
    
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
{foreach $articles as $a}
<article class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
<div class="panel panel-default" style="min-height: 460px;" > <!-- style="height: 470px;" -->
<div class="panel-body" style="height: 240px;">
	<div class="row text-center">
    	<h4>
			<a href="{path id=rgs_catalog_article_details params=['id' => $a.id, 'slug' => $a.slug]}">{$a.name}</a>
		</h4>
    </div>
    <div class="row text-center">
    	<!--<div class="col-md-3 col-sm-3">-->
        <div>
        	<a href="#">
                {img src=$a.image package=upload class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" title=$a.name}
            </a>
    	</div>
    </div>
        
        <div class="row text-center">
        {if !empty($a.teaser)}        
        	{$a.teaser|purify} 
        {else}
        	{$a.description|purify|truncate:60:'...':true}       
        {/if}
        </div>
        	
    	<!--<div class="clearfix visible-sm-block"></div>-->
    </div>
        <div class="panel-footer">
        <table class="table table-bordered table-condensed"> <!-- table-condensed-->
                		<tbody>
                    		<tr>
                        		<th>Category</th><td title="{$a.category.name}">{$a.category.name|truncate:15:'...':true}</td>
	                        </tr>
                            <tr>
        	                	<th>State</th><td>{$a.state.name}</td>
            	            </tr>
    	                    <tr>
        	                	<th>Prix</th>
                                <td>
                                	{if !empty($a.price) && $a.price gt 0}
                                    	&euro;&nbsp;{$a.price}
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
            {auth permissions=ROLE_ADMIN}
        	<a class="btn btn-xs btn-warning" href="#"> edit </a>
    		{/auth}
            <button type="submit" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-shopping-cart"></span> Panier</button>
        </form>
        </div>
	</div>
</article>
{/foreach}
</div>
<div class="col-xs-12">
	<div class="pull-right">
		{pagination paginator=$paginator max="4" queryStrict=['category', 'state']}
    </div>
    
</div>
</div>
{/block}
