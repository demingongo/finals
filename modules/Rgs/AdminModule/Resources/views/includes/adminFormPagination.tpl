<input type="hidden" name="page" value="{$page}">
<ul class="pagination pagination-sm">
{if $pagesCount neq 1 && $page gt 0 && $pagesCount gt 1}
{if $page eq 1}
	<li class="disabled">
	<a><span class="glyphicon glyphicon-fast-backward"></span></a>
	</li>
	<li class="disabled">
	<a><span class="glyphicon glyphicon-backward"></span></a>
	</li>
{else}
	<li>
	<a href="#" onclick="document.adminForm.page.value=1; adminFormSubmit(); return false;"><span class="glyphicon glyphicon-fast-backward"></span></a>
	</li>
	<li>
	<a href="#" onclick="document.adminForm.page.value={$page-1}; adminFormSubmit(); return false;"><span class="glyphicon glyphicon-backward"></span></a>
	</li>
{/if}
{$i=$page-4}
{if $i gt 1}
	<li><a href="#" onclick="document.adminForm.page.value={$i-1}; adminFormSubmit(); return false;">...</a></li>
{/if}
{while $i lt $page}
	{if $i gte 1}
	<li><a href="#" onclick="document.adminForm.page.value={$i}; adminFormSubmit(); return false;">{$i}</a></li>
	{/if}
	{$i=$i+1}
{/while}
{for $i=$page to $pagesCount max=4}
	{if $i eq $page}
	<li class="active"><a>{$i}</a></li>
	{else}
	<li><a href="#" onclick="document.adminForm.page.value={$i}; adminFormSubmit(); return false;">{$i}</a></li>
	{/if}
{/for}
{if $i lte $pagesCount}
<li><a href="#" onclick="document.adminForm.page.value={$i}; adminFormSubmit(); return false;">...</a></li>
{/if}
{if $page eq $pagesCount}
	<li class="disabled">
	<a><span class="glyphicon glyphicon-forward"></span></a>
	</li>
	<li class="disabled">
	<a><span class="glyphicon glyphicon-fast-forward"></span></a>
	</li>
{else}
	<li>
	<a href="#" onclick="document.adminForm.page.value={$page+1}; adminFormSubmit(); return false;"><span class="glyphicon glyphicon-forward"></span></a>
	</li>
	<li>
	<a href="#" onclick="document.adminForm.page.value={$pagesCount}; adminFormSubmit(); return false;"><span class="glyphicon glyphicon-fast-forward"></span></a>
	</li>
{/if}
{/if}
</ul>