{* file:[RgsCatalogModule]menu_filtre.tpl *}

<div class="col-xs-12 col-md-3 col-lg-3 col-sm-3">

	<h4><b>{'Configure the choice'|trans}</b></h4>
	<a href="{$nofilterHref}">
		<small>{'Deactivate current filters'|trans}</small>
    </a>
    <hr />
    <form method="get" id="filtre" name="filtre" data-novice='form-control'>
    	<div style="display: block; padding: 1px;">
		{$categoryWidget}
		</div>
        <br />
        <div style="display: block; padding: 1px;">
		{$stateWidget}
		</div>
    </form>
    <div id="somelink" ></div>

</div>
