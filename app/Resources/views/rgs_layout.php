<!doctype html>
{config_load file='file:[RgsCatalogModule]rgs.conf'}
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{asset url='/img/retro_favicon.ico'}" />
{block name=bowercomponents}
{include file='includes/bower_components.tpl'}
{/block}

{block name=stylesheets}
{include file='includes/css.tpl'}
{/block}
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="{asset url='/style_ie.css' package='css'}" type="text/css">
<![endif]-->

<title>{nocache}{block name=title}{/block}{#sitename#}{/nocache}</title>
{block name=head}{/block}
</head>

<body id="body">

<div class="navbar-wrapper">

<div class="container">

<nav id="mainNav" class="navbar navbar-inverse  navbar-custom " role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
	  <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{path id='rgs_catalog_index' absolute=true}" title="{#sitename#}">
      	<span class="visible-xs visible-sm">{*#sitename#*}<img src="{asset url='img/logo-brand.png'}" alt="{#sitename#}" height="21" /></span>
        <span class="hidden-xs hidden-sm">{#sitename#}</span>
      </a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li>
		<a href="{path id='rgs_catalog_index' absolute=false}">{'nav.home'|trans}</a></li>
        <li>
		<a href="{path id='rgs_catalog_articles_all'}">Articles</a></li>
        <li>
		<a href="#about">{'nav.about'|trans}</a></li>
        <li>
		<a href="#contact">{'nav.contact'|trans}</a></li>
      </ul>
	  <ul class="nav navbar-nav navbar-right">
		{if $session->isAuthenticated()}
		<li style="background-color: green;"><a href="{path id='rgs_catalog_user_profile' referenceType=ABSOLUTE_URL}"
		title="{'layout.logged_in_as'|trans:['%username%' => $app.user.data.login|escape]:UserModule}"><span class="glyphicon glyphicon-user"></span> {$app.user.data.login|escape:'htmlall'}</a></li>
        <li class="dropdown">
                <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">
                <i class="caret"></i></a>
                <ul id="g-account-menu" class="dropdown-menu">
                	<li> <a href="{path id='rgs_catalog_user_profile' params=['tab'=>'myreservations'] absolute=true}"> My reservations</a></li>
                    <li class="divider"></li>
                    <li> <a href="{path id='user_security_logout' absolute=true}"><i class="glyphicon glyphicon-log-out"></i> {'layout.logout'|trans:[]:UserModule}</a></li>
                </ul>
        </li>
		{else}
        {*****************************************************************************************************************
        <li><a href="{path id='user_registration_register' absolute=true}"><span class="glyphicon glyphicon-pencil"></span> {'layout.register'|trans:[]:UserModule}</a></li>
        *****************************************************************************************************************}
		<li class="no-action" style=""><a href="#connexion" data-toggle="modal" data-target="#connexionModal" ><span class="glyphicon glyphicon-log-in"></span> {"layout.login"|trans:[]:UserModule}</a></li>
        {/if}
        <li>
        	<a href="{path id='rgs_catalog_caddie' referenceType=ABSOLUTE_URL}">
            <i class="glyphicon glyphicon-shopping-cart"></i>
            <span class="badge">{$rgs.caddie->count()}</span>
            </a>
        </li>
        {if $session->isAuthenticated() && $app.user.data->hasRole('ROLE_SUPER_ADMIN')}
		<li class="no-action">
			<a href="{path id='rgs_admin_index' absolute=true}" target="_blank">
				Admin
				<span class="glyphicon glyphicon-new-window"></span>
			</a>
		</li>
        {/if}
        <li class="no-action">
        	<a href="{path id='rgs_catalog_language' _locale='en' absolute=true}">
        		<img alt="en" title="english" height="16" src="{asset url='/img/pictos/United-States-of-Americ-icon.png'}" />
            </a>
        </li>
        <li class="no-action">
        	<a href="{path id='rgs_catalog_language' _locale='fr' absolute=true}">
        		<img alt="fr" title="français" height="16" src="{asset url='/img/pictos/France-icon.png'}" />
            </a>
        </li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>

</div>

</div> <!--/.navbar-wrapper -->


{block name="carousel"}
{include file='includes/carousel.tpl'}
{/block}


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

<SECTION class="row">
{block name=section}Cette page ne contient rien{/block}
</SECTION>

<a href="#top" class="hidden scroll-to-top fade-in-scroll well well-sm" title="back to top">
    <span class="glyphicon glyphicon-arrow-up"></span>
</a><!-- /.fixed-bottom -->

  </div>
</div><!-- /.container -->

<!--FOOTER-->
<FOOTER id="footer" class="footerbar">
	<div class="container testborder">
	<div class="row">
	<div class="hidden-xs">
		<ul class="list-inline">
			<li><i class="glyphicon glyphicon-copyright-mark"></i> {#sitename#} 2017</li>
			<li><a href="{path id='rgs_catalog_index' absolute=true}">{'nav.home'|trans}</a></li>
            <li><a href="#about">{'nav.about'|trans}</a></li>
            <li><a href="#contact">{'nav.contact'|trans}</a></li>
		</ul>
	</div>
	<div class="visible-xs form-group">
		<ul class="list-inline">
			<li><i class="glyphicon glyphicon-copyright-mark"></i> {#sitename#}</li>
		</ul>
		<select class="form-control" id="footernav">
			<option value="#">---</option>
			<option value="{path id='rgs_catalog_index' absolute=true}">{'nav.home'|trans}</option>
			<option value="#about">{'nav.about'|trans}</option>
            <option value="#contact">{'nav.contact'|trans}</option>
		</select>
	</div>
	</div>
	</div>
</FOOTER>
<!-- / FOOTER-->

{block name=javascript}
{include file='includes/js.tpl'}
{/block}

</body>
{block name=script}{/block}
</html>
