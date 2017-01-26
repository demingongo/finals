<?php

namespace Novice;

use \Novice\Module;

use Symfony\Component\Console\Application as ConsoleApp;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as YFL;
use Symfony\Component\Config\FileLocator;

class FrameworkModule extends Module
{
    private $autoloader;

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        //parent::build($container);
		
		$loader = new YFL($container, new FileLocator(__DIR__.'/Resources/config'));
		$loader->load('listeners.yml');

        //$container->addCompilerPass(new \Novice\DoctrineCompilerPass);
		//$container->addScope(new Scope('request')); //add scope request
		$container->addCompilerPass(new Templating\Compiler\TemplatingCompilerPass());
		$container->addCompilerPass(new Middleware\Compiler\AddSubscriberCompilerPass());
		$container->addCompilerPass(new DependencyInjection\Compiler\TranslatorPass());
        $container->addCompilerPass(new DependencyInjection\Compiler\TranslationExtractorPass());
        $container->addCompilerPass(new DependencyInjection\Compiler\TranslationDumperPass());
		
		$container->addCompilerPass(new DependencyInjection\Compiler\PropertyEditorPass());
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function shutdown()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function registerCommands(ConsoleApp $application)
    {

    }
}
