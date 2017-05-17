<?php

use Novice\Application;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;

use Novice\Middleware\MiddlewareDispatcher;

use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;

class App extends Application
{
    public function registerModules()
    {
        $modules = array(
            //new Acme\AcmeModule\AcmeModule(),
			new DoctrineModule\DoctrineModule(),
			new Novice\FrameworkModule(),
			new Novice\Module\SwiftmailerModule\SwiftmailerModule(),
			new Novice\Module\SmartyBootstrapModule\SmartyBootstrapModule(),
			
			new Rgs\UserModule\UserModule(),
			new Rgs\CatalogModule\RgsCatalogModule(),
			new Rgs\AdminModule\RgsAdminModule(),
			new Doctrine\NestedSetModule\NestedSetModule(),
			new Api\CatalogModule\CatalogApiModule()

			//new Cocur\Slugify\Bridge\Symfony\CocurSlugifyModule(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        }

        return $modules;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        //$loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
		//$loader->load(__DIR__.'/config/config.yml');
		$loader->load(__DIR__.'/config/rgs/config.yml');
    }

  public function run()
  {
	$this->boot();
	$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
	$response = $this->handle($request);
	$response->send();
	/*dump($this->container->get('session')->get("user_security_login_form_r1im6pqt9bi56e0l4mmic1umb2_token"));
	dump(__METHOD__);*/
  }
}
