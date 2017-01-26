{extends file='file:[UserModule]mail_layout.php'}

{block name=content}
{if isset($message)}
{$message}
{/if}
{/block}