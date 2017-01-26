{* file:[RgsAdminModule]index.tpl *}

{extends file='file:[RgsAdminModule]layout.php'}

{block name=page-wrapper}
<!-- 16:9 aspect ratio -->
<div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="{$filemanagerPath|escape:'htmlall'}"></iframe>
</div>
{/block}
