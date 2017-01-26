<!doctype html>
{config_load file='file:[RgsCatalogModule]rgs.conf'}
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
{block name=stylesheets}
<link rel="stylesheet" href="https://bootswatch.com/slate/bootstrap.min.css" type="text/css">
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css">-->
<!--<link rel="stylesheet" href="{asset url='/twitter-bootstrap/3.3.5/css/bootstrap.min.css' package='cdnjs'}" type="text/css">-->
<link rel="stylesheet" href="{asset url='/bootstrap.novice.css' package='css'}" type="text/css">
<link rel="stylesheet" type="text/css" href="{asset url='/fancybox/source/jquery.fancybox.css' package='plugins'}" media="screen" />
<link rel="stylesheet" href="{asset url='/main.css' package='css'}" type="text/css">
{/block}
<link rel="icon" href="{asset url='/img/retro_favicon.ico'}" />
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="{asset url='/style_ie.css' package='css'}" type="text/css">
<![endif]-->
{block name=javascript}
<script src="{asset url='/jquery-1.11.2.min.js' package='js'}"></script>
<script src="{asset url='/jquery-ui.min.js' package='js'}"></script>
<script src="{asset url='/twitter-bootstrap/3.3.6/js/bootstrap.min.js' package='cdnjs'}"></script>
<script src="{asset url='/jquery-easing/1.3/jquery.easing.min.js' package='cdnjs'}"></script>
<script src="{asset url='/agency.js' package='js'}"></script>
<script src="{asset url='/default.js' package='js'}"></script>
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.4.6/angular.min.js"></script>
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.4.6/angular-sanitize.js"></script>
<script type="text/javascript" src="{asset url='/bootstrap-filestyle.min.js' package='js'}"> </script>
<script type="text/javascript" src="{asset url='/jquery/jquery.observe_field.js' package='js'}"></script>
<script type="text/javascript" src="{asset url='/fancybox/source/jquery.fancybox.pack.js' package='plugins'}"></script>
<script type="text/javascript" src="{asset url='/main.js' package='js'}"></script>
<script src="{asset url='/1000hz-bootstrap-validator/0.8.1/validator.min.js' package='cdnjs'}"></script>
{/block}
<script>

</script>
<title>{nocache}{block name=title}{/block}{#sitename#} - Administration{/nocache}</title>
{block name=head}{/block}
</head>

<body id="body" data-ng-app="AngularJSApp">

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
	  <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{path id='rgs_admin_index' absolute=true}">Administration</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" id="menuUsers" data-toggle="dropdown">
				Users
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menuUsers">
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des utilisateurs</a></li>
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des groupes</a></li>
			</ul>
		</li>
        
		<li class="dropdown">		
			<a href="#" class="dropdown-toggle" id="menuContent" data-toggle="dropdown">
				{"nav.content"|trans}
				<span class="caret"></span>
			</a>		
			<ul class="dropdown-menu" role="menu" aria-labelledby="menuContent">
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des articles</a></li>
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_categorie' absolute=true}">Gestion des catégories</a></li>
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des états</a></li>
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des marques</a></li>
				<li class="dropdown-submenu"><a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des médias</a></li>
			</ul>
		</li>
        
		<li>
		<a href="#about">{"nav.about"|trans}</a></li>
        <li>
		<a href="#contact">Contact</a></li>
      </ul>
	  <ul class="nav navbar-nav navbar-right" >
		<li>
			<a href="{path id='rgs_catalog_index' absolute=true}" target="_blank">
				{#sitename#}
				<span class="glyphicon glyphicon-new-window">
			</a>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown dropdown-toggle" id="menuParam" data-toggle="dropdown">
				<span class="glyphicon glyphicon-cog"></span>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menuParam">
			{if $session->isAuthenticated()}
				<li class="text-center" style="background-color: green;" title="{'layout.logged_in_as'|trans:['%username%' => $app.user.data.login|escape]:UserModule}">
					<span class="glyphicon glyphicon-user"></span> 
					&nbsp;{$app.user.data.login|escape:'htmlall'}
				</li>
				<li role="presentation" class="divider"></li>
				<li class="text-center">
					<a href="#update">
						{'layout.edit'|trans:[]:UserModule}
					</a>
				</li>
				<li role="presentation" class="divider"></li>
			{/if}
				<li class="text-center"><a href="{path id='rgs_admin_logout' absolute=true}"><span class="glyphicon glyphicon-log-out"></span> {'layout.logout'|trans:[]:UserModule}</a></li>
			</ul>
		</li>
    </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

<div class="container">
  <div class="starter-template">

<!--HEADER-->
{if !$session->isAuthenticated()}
	{bootstrap_modal id="connexionModal" title="{'layout.login'|trans:[]:UserModule}" class="fade" show_button=false}
		{include file='file:[UserModule]Security/login_form.php'}
	{/bootstrap_modal}
{/if}

{if $session_flash->has('danger')}
{notification type="danger" message=$session_flash->get('danger') sign=true close=false}
{/if}
{if $session_flash->has('error')}
{notification type="error" message=$session_flash->get('error') sign=true}
{/if}
{if $session_flash->has('warning')}
{notification type="warning" message=$session_flash->get('warning') sign=true}
{/if}
{if $session_flash->has('success')}
{notification type="success" message=$session_flash->get('success') sign=true} {*glyphicon="thumbs-up"*}
{/if}
{if $session_flash->has('info')}
{notification type="info" message=$session_flash->get('info') sign=true}
{/if}

<div {block name=contentAttr}{/block}>
{block name=menuA}{/block}

<SECTION class="{block name=sectionClass}row{/block}">
{block name=section}Cette page ne contient rien{/block}
</SECTION>

{block name=menuB}{/block}
</div>

<a href="#top" class="hidden scroll-to-top fade-in-scroll well well-sm" title="back to top">
    <span class="glyphicon glyphicon-arrow-up"></span>
</a><!-- /.fixed-bottom -->

  </div>
</div><!-- /.container -->

<!--FOOTER-->
<FOOTER id="footer">
	<div class="container testborder">
	<div class="row">
	<div class="hidden-xs">
		<ul class="list-inline">
			<li><i class="glyphicon glyphicon-copyright-mark"></i> {#sitename#}</li>
			<li><a href="{path id='rgs_catalog_index' ext='html' absolute=true}">{"nav.home"|trans}</a></li>
			<li><a href="#">Privacy Policy</a></li>
			<li><a href="#">Terms of use</a></li>
			<li><a href="#">Contact Us</a></li>
		</ul>
		{*************************
		<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
		</p>
		<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
		</p>
		****************}
	</div>
	<div class="visible-xs form-group">
		<ul class="list-inline">
			<li><i class="glyphicon glyphicon-copyright-mark"></i> {#sitename#}</li>
		</ul>
		<select class="form-control" id="footernav">
			<option value="#">---</option>
			<option value="{path id='rgs_catalog_index' absolute=true}">{"nav.home"|trans}</option>
			<option value="#">Privacy Policy</option>
			<option value="#">Terms of use</option>
			<option value="#">Contact Us</option>
		</select>
	</div>
	</div>
	</div>
</FOOTER>
<!-- / FOOTER-->

</body>
{block name=script}{/block}
</html>
