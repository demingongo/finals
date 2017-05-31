{* file:[RgsCatalogModule]index.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}




{block  name=section}
{******************
<p>{path id="rgs_catalog_index" referenceType=ABSOLUTE_URL}</p>
*******************}

<p>{$tinymce_base_url}</p>

{foreach name=item from=$ads item=item}
<div class="col-sm-12">
<h3 class="text-center">{$item.name}</h3>
{if !empty($item.image)}
<p class="text-center">
<img src="{image_src path=$item.image package=upload}" class="img-responsive img-thumbnail" alt="{$item.name}" title="{$item.name}"  />
</p>
{/if}
<p>
{$item.description}
</p>
</div>
<hr />
{/foreach}

{************
<p>num items : {constant object=$controller name=NUM_ITEMS }</p>
*************}
{/block}
