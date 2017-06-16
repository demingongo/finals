<?php

use Novice\Application;
use Symfony\Component\Config\Loader\LoaderInterface;

class App extends Application
{
    public function registerModules()
    {
        $modules = array(
			new DoctrineModule\DoctrineModule(),
			new Novice\FrameworkModule(),
			new Novice\Module\SwiftmailerModule\SwiftmailerModule(),
			new Novice\Module\SmartyBootstrapModule\SmartyBootstrapModule(),
			new Novice\Module\ContentManagerModule\ContentManagerModule(),
			
			new Rgs\UserModule\UserModule(),
			new Rgs\CatalogModule\RgsCatalogModule(),
			new Rgs\AdminModule\RgsAdminModule(),
			new Doctrine\NestedSetModule\NestedSetModule(),

			//new Cocur\Slugify\Bridge\Symfony\CocurSlugifyModule(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        }

        return $modules;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
		$loader->load(__DIR__.'/config/rgs/config.yml');
    }

  public function run()
  {
	$this->boot();
	$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
	$response = $this->handle($request, false); //handle(request, exceptionEvent = false)
	$response->send();
	/*dump($this->container->get('session')->get("user_security_login_form_r1im6pqt9bi56e0l4mmic1umb2_token"));
	dump(__METHOD__);*/
  }
}
