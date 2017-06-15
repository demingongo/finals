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
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 main-title">
  <h1>{'nav.home'|trans}</h1>
</div>

{foreach name=item from=$ads item=item}
<div class="col-xs-12 col-md-4 col-lg-4 col-sm-4">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="text-center">{$item.name}</h3>
    </div>
    <div class="panel-body">
      {if !empty($item.image)}
        <p class="text-center">
          <img src="{image_src path=$item.image package=upload}" class="img-responsive img-thumbnail" alt="{$item.name}" title="{$item.name}"  />
        </p>
      {/if}
      <p>
        {$item.description}
      </p>
    </div>
  </div>
</div>
{/foreach}

{/block}
