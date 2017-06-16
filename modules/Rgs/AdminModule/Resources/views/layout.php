<!doctype html>
{config_load file='file:[RgsCatalogModule]rgs.conf'}
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{asset url='/img/retro_favicon_admin_2.ico'}" />

{block name=bowercomponents}
{include file='file:includes/bower_components.tpl'}
{/block}

{block name=stylesheets}
<!-- Bootstrap Core CSS -->
    <link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/bootstrap/dist/css/bootstrap.min.css' package='theme'}" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/metisMenu/dist/metisMenu.min.css' package='theme'}" rel="stylesheet">

<!-- Timeline CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/css/timeline.css' package='theme'}" rel="stylesheet">

<!-- Custom CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/css/sb-admin-2.css' package='theme'}" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/morrisjs/morris.css' package='theme'}" rel="stylesheet">

<!-- Custom Fonts -->
<link href="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/font-awesome/css/font-awesome.min.css' package='theme'}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{asset url='/main.admin.css' package='css'}" type="text/css">
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
<title>{nocache}{block name=title}{/block}{#sitename#} - Administration{/nocache}</title>
{block name=head}{/block}
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-static-top" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{path id='rgs_admin_index' absolute=true}">Administration</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
            
            {********************************************************************* unused for now
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 2</strong>
                                        <span class="pull-right text-muted">20% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                            <span class="sr-only">20% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 3</strong>
                                        <span class="pull-right text-muted">60% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                            <span class="sr-only">60% Complete (warning)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 4</strong>
                                        <span class="pull-right text-muted">80% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                            <span class="sr-only">80% Complete (danger)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Tasks</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small">12 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-tasks fa-fw"></i> New Task
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                **********************************************************************************************}
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
					{auth}
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> {$app.user.data.login|escape:'htmlall'}</a>
                        </li>
                        <li><a href="{path id=rgs_admin_users_edit params=['id' => $app.user.data.id]}"><i class="fa fa-gear fa-fw"></i> {'layout.edit'|trans:[]:UserModule}</a>
                        </li>
                        <li class="divider"></li>
					{/auth}
                        <li><a href="{path id='rgs_admin_logout' absolute=true}"><i class="fa fa-sign-out fa-fw"></i> {'layout.logout'|trans:[]:UserModule}</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
						<li>
                            <a href="#"><i class="fa fa-folder-open fa-fw"></i> {"nav.content"|trans}<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
									<a href="{path id='rgs_admin_gestion_advertisement' absolute=true}">Gestion des publicités</a>
								</li>
                                <li>
									<a href="{path id='rgs_admin_gestion_article' absolute=true}">Gestion des articles</a>
								</li>
								<li>
									<a href="{path id='rgs_admin_gestion_category' absolute=true}">Gestion des catégories</a>
								</li>
								<li>
									<a href="{path id='rgs_admin_gestion_state' absolute=true}">Gestion des états</a>
								</li>
								<li>
									<a href="{path id='rgs_admin_gestion_brand' absolute=true}">Gestion des marques</a>
								</li>
								<li>
									<a href="{path id='rgs_admin_gestion_media' absolute=true}">Gestion des médias</a>
								</li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        {auth permissions=ROLE_SUPER_ADMIN}
						<li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Users<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="{path id='rgs_admin_gestion_users' absolute=true}">Gestion des utilisateurs</a>
                                </li>
                                <li>
                                    <a href="{path id='rgs_admin_gestion_groups' absolute=true}">Gestion des groupes</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        {/auth}
                        <li>
                        	<a href="#"><i class="fa fa-cubes"></i> Reservations<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="{path id='rgs_admin_gestion_reservations'}">Gestion des réservations</a>
                                </li>
                                <li>
                                    <a href="{path id='rgs_admin_gestion_expired_reservations'}">Réservations expirées</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="{path id='rgs_admin_gestion_requests'}"><i class="fa fa-gamepad"></i> User requests</a>
                        </li>
                        {******************************************
						<li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/panels-wells.html" target="_blank">Panels and Wells</a>
                                </li>
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/buttons.html" target="_blank">Buttons</a>
                                </li>
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/notifications.html" target="_blank">Notifications</a>
                                </li>
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/typography.html" target="_blank">Typography</a>
                                </li>
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/icons.html" target="_blank"> Icons</a>
                                </li>
                                <li>
                                    <a href="http://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/grid.html" target="_blank">Grid</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        *******************************************}
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
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
		{notification type="success" message=$session_flash->get('success') sign=true}
		{/if}
		{if $session_flash->has('info')}
		{notification type="info" message=$session_flash->get('info') sign=true}
		{/if}

		{block name=page-wrapper}
		{/block}
        </div>
        <!-- /#page-wrapper -->
			<a href="#top" class="hidden scroll-to-top fade-in-scroll well well-sm" title="back to top">
				<span class="glyphicon glyphicon-arrow-up"></span>
			</a><!-- /.fixed-bottom -->
    </div>
    <!-- /#wrapper -->

{block name=javascript}


<!-- bootstrap JS theme -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/bootstrap/dist/js/bootstrap.min.js' package='theme'}"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/bower_components/metisMenu/dist/metisMenu.min.js' package='theme'}"></script>

<!-- Custom Theme JavaScript -->
<script src="{asset url='/startbootstrap-sb-admin-2-1.0.8/dist/js/sb-admin-2.js' package='theme'}"></script>

<!-- Novice JS -->
<script type="text/javascript" src="{asset url='js/novice.js' package='novice'}"></script>

<!-- Custom JS -->
<script src="{asset url='chosen-select.js' package='js_custom'}" type="text/javascript"></script>
<script src="{asset url='select2.js' package='js_custom'}" type="text/javascript"></script>
<script src="{asset url='/default.js' package='js'}"></script>
<!--<script type="text/javascript" src="{asset url='/main.admin.js' package='js'}?th"></script>-->
{/block}

</body>
{block name=script}{/block}
</html>
