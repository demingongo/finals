{* file:[RgsAdminModule]index.tpl *}

{extends file='file:[RgsAdminModule]layout.php'}

{block name=menuA}
	<nav class="panel panel-default col-sm-3" style="border: 0px solid red;">
		<div class="panel-body">
		<h5>{"nav.content"|trans}</h5>
		<ul class="nav nav-stacked">
			<li>
			<a href="{path id='rgs_admin_gestion_media' absolute=true}">
			<span class="glyphicon glyphicon-file"></span>
			&nbsp;Gestion des articles
			</a>
			</li>
			<li>
			<a href="{path id='rgs_admin_gestion_categorie' absolute=true}">
			<span class="glyphicon glyphicon-folder-open"></span>
			&nbsp;Gestion des catégories
			</a>
			</li>
			<li>
			<a href="{path id='rgs_admin_gestion_media' absolute=true}">
			<span class="glyphicon glyphicon-folder-open"></span>
			&nbsp;Gestion des états
			</a>
			</li>
			</li>
			<li>
			<a href="{path id='rgs_admin_gestion_media' absolute=true}">
			<span class="glyphicon glyphicon-folder-open"></span>
			&nbsp;Gestion des marques
			</a>
			</li>
			<li>
			<a href="{path id='rgs_admin_gestion_media' absolute=true}">
			<span class="glyphicon glyphicon-picture"></span>
			&nbsp;Gestion des médias
			</a>
			</li>
		</ul>
		<h5>Users</h5>
		<ul class="nav nav-stacked">
			<li>
			<a href="{path id='rgs_admin_gestion_media' absolute=true}">
			<span class="glyphicon glyphicon-user"></span>
			&nbsp;Gestion des utilisateurs
			</a>
			</li>
		</ul>
		</div>
	</nav>
{/block}

{block name=page-wrapper}
<p>{$greetings}</p>
<p>You are in "file:[RgsAdminModule]index.tpl"</p>
{/block}
