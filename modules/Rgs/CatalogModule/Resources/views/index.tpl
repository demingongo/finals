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

<p>{$greetings}</p>

<p>{$tinymce_base_url}</p>

{************
<p>num items : {constant object=$controller name=NUM_ITEMS }</p>
*************}
{/block}
