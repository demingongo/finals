<?php

namespace DoctrineModule;

use \Novice\Module;

/*use Doctrine\Common\Util\ClassUtils;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand;*/
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
    public function boot()
    {
        // Register an autoloader for proxies to avoid issues when unserializing them
        // when the ORM is used.
        /*if ($this->container->hasParameter('doctrine.orm.proxy_namespace')) {
            $namespace = $this->container->getParameter('doctrine.orm.proxy_namespace');
            $dir = $this->container->getParameter('doctrine.orm.proxy_dir');
            // See https://github.com/symfony/symfony/pull/3419 for usage of
            // references
            $container =& $this->container;

            $this->autoloader = function($class) use ($namespace, $dir, &$container) {
                if (0 === strpos($class, $namespace)) {
                    $fileName = str_replace('\\', '', substr($class, strlen($namespace) +1));
                    $file = $dir.DIRECTORY_SEPARATOR.$fileName.'.php';

                    if (!is_file($file) && $container->getParameter('doctrine.orm.auto_generate_proxy_classes')) {
                        $originalClassName = ClassUtils::getRealClass($class);
                        // @var $registry Registry 
                        $registry = $container->get('doctrine');

                        // Tries to auto-generate the proxy file
                        // @var $em \Doctrine\ORM\EntityManager 
                        foreach ($registry->getManagers() as $em) {

                            if ($em->getConfiguration()->getAutoGenerateProxyClasses()) {
                                $classes = $em->getMetadataFactory()->getAllMetadata();

                                foreach ($classes as $classMetadata) {
                                    if ($classMetadata->name == $originalClassName) {
                                        $em->getProxyFactory()->generateProxyClasses(array($classMetadata));
                                    }
                                }
                            }
                        }

                        clearstatcache(true, $file);
                    }

                    if (file_exists($file)) {
                        require $file;
                    }
                }
            };
            spl_autoload_register($this->autoloader);
        }*/
    }

    /**
     * {@inheritDoc}
     */
    public function shutdown()
    {
        /*if (null !== $this->autoloader) {
            spl_autoload_unregister($this->autoloader);
            $this->autoloader = null;
        }*/
    }

    /**
     * {@inheritDoc}
     */
    public function registerCommands(Application $application)
    {
        // Use the default logic when the ORM is available.
        // This avoids listing all ORM commands by hand.
        /*if (class_exists('Doctrine\\ORM\\Version')) {
            parent::registerCommands($application);

            return;
        }

        // Register only the DBAL commands if the ORM is not available.
        $application->add(new CreateDatabaseDoctrineCommand());
        $application->add(new DropDatabaseDoctrineCommand());
        $application->add(new RunSqlDoctrineCommand());*/
    }
}
