{* file:[RgsCatalogModule]articlesAll.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}

{block name=title}
{$article.name}
{/block}

{block  name=section}

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 main-title">
  <h1>{$article.name}</h1>
  <span><a href="{path id=rgs_catalog_articles_all}">{'Articles'|trans}</a> &gt; {$article.name}</span>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 full-width">

<div class="col-xs-12 col-md-3 col-lg-3 col-sm-3">
</div>

<div class="col-sm-9">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 full-width">

{**********
{annotated_form class="col-xs-12 col-sm-12 col-md-12 col-lg-12" form=$article}
{/annotated_form}
*****}

<article class="col-lg-12 col-md-12 col-sm-12 col-xs-12 full-width">
<div class="panel panel-default panel-single-article" > <!-- style="height: 470px;" -->
<div class="panel-body">
	<div class="row text-center">
    	<h1>
			<a href="#">{$article.name}</a>
		</h1>
    </div>
	<div>
		{img src=$article.image package=upload class="img-thumbnail" alt=$article.name style="height: 120px; min-width: 120px;" title=$article.name}
	</div>
        	
        <form method="post" action="{path id='rgs_catalog_caddie_add'}">
        	<input type="hidden" name="id" value="{$article.id}" />
            {auth permissions=ROLE_ADMIN}
        	<a class="btn btn-xs btn-warning" 
				href="{path id=rgs_admin_articles_edit params=['id' => $article.id, 'slug' => $article.slug]}" 
				target="_blank">
			 edit 
			 </a>
    		{/auth}
			<div class="description-article">
				{$article.teaser}
			</div>
			<div class="description-article">
				{$article.description}
			</div>
			{if !empty($article.url)}
				<div class="description-article">
					<a href="{$article.url}" target="_blank">{$article.url}</a>
				</div>
			{/if}
			<table class="table table-bordered table-condensed"> <!-- table-condensed-->
                		<tbody>
                    		<tr>
                        		<th>{'Category'|trans}</th><td title="{$article.category.name}">{$article.category.name}</td>
	                        </tr>
                            <tr>
        	                	<th>{'State'|trans}</th><td>{$article.state.name|trans}</td>
            	            </tr>
							{if !empty($article.brand)}
							<tr>
        	                	<th>{'Brand'|trans}</th>
								<td>
									{if !$article.brand.url}
										{$article.brand.name}
									{else}
										<a href="{$article.brand.url}" target="_blank">{$article.brand.name}</a>
									{/if}
								</td>
            	            </tr>
							{/if}
    	                    <tr>
        	                	<th>{'Price'|trans}</th>
                                <td>
                                	{if !empty($article.price) && $article.price gt 0}
                                    	&euro;&nbsp;{$article.price}
                                    {else}
                                    	<a href="#contact"><small>Contactez-nous</small></a>
                                    {/if}
                                </td>
            	            </tr>
                	        <tr>
                    	    	<th>{'Stock'|trans}</th>
                                <td>
                                	{if !empty($article.stock) && $article.stock gt 0}
                                		<small class="text-success">{'available_stock'|trans}</small>
                                    {else}
                                    	<small class="text-danger">{'unavailable_stock'|trans}</small>
                                    {/if}
                                </td>
                        	</tr>
	                    </tbody>
    	            </table>
            <button type="submit" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-shopping-cart"></span> Panier</button>
        </form>
</div>
</div>
</article>
</div>

</div>

</div>
{/block}
