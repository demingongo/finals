{config_load file='file:[RgsCatalogModule]rgs.conf' section='error'}
<!--<h1 style="font-size: xx-large; font-family:'MS Serif', 'New York', serif; font-style:oblique;">{$status_code} {$status_text}</h1>-->
<img src="{image_src path='framework/error_500.png' package=img}" style="max-height: calc(100vh); max-width: calc(100vw);" />

{if isset($message)}
<p>{$message}</p>
{/if}