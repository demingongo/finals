{* file:[RgsCatalogModule]menu_filtre.tpl *}

<div class="col-sm-3">

	<h4><b>Configurer le choix</b></h4>
	<a href="{$nofilterHref}">
		<small>Désactiver tous les filtres</small>
    </a>
    <hr />
    <form method="get" id="filtre" name="filtre" data-novice='form-control'>
    	<div style="display: block; padding: 1px;">
		{$categoryWidget}
		</div>
        <br />
        <div style="display: block; padding: 1px;">
		{$etatWidget}
		</div>
    </form>
    <div id="somelink" ></div>

</div>
