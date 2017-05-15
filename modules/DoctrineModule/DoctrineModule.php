<?php

namespace DoctrineModule;

use \Novice\Module;

/*use Doctrine\Common\Util\ClassUtils;*/
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use DoctrineModule\DependencyInjection\Compiler\DoctrineCompilerPass;


class DoctrineModule extends Module
{
    private $autoloader;

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineCompilerPass);
    }

    /**
     * {@inheritDoc}
     */
    public function registerCommands(Application $application)
    {
		$application->add(new \DoctrineModule\Command\CreateDatabaseDoctrineCommand());
		$application->add(new \DoctrineModule\Command\DropDatabaseDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\RunSqlDoctrineCommand());
		
		$application->add(new \DoctrineModule\Command\ReservedWordsDoctrineCommand());
		
		$application->add(new \DoctrineModule\Command\Proxy\ClearMetadataCacheDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\ClearResultCacheDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\ClearQueryCacheDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\CreateSchemaDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\UpdateSchemaDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\DropSchemaDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\EnsureProductionSettingsDoctrineCommand());
		$application->add(new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand());
		
		$application->add(new \DoctrineModule\Command\GenerateEntitiesDoctrineCommand());
		$application->add(new \DoctrineModule\Command\GenerateProxiesDoctrineCommand());
		$application->add(new \DoctrineModule\Command\GenerateRepositoriesDoctrineCommand());
		
		$application->add(new \DoctrineModule\Command\Proxy\ConvertMappingDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\RunDqlDoctrineCommand());
		$application->add(new \DoctrineModule\Command\Proxy\ValidateSchemaCommand());
		$application->add(new \DoctrineModule\Command\Proxy\InfoDoctrineCommand());
    }
}
