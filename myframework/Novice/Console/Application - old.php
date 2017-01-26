<?php

namespace Novice\Console;

use Novice\Version;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    private $managers;
    private $commandsRegistered = false;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct($managers)
    {
        $this->managers = $managers;

        parent::__construct('Novice', Version::VERSION . ' - Console / mode prod? dev?');

        /*$this->getDefinition()->addOption(new InputOption('--shell', '-s', InputOption::VALUE_NONE, 'Launch the shell.'));
        $this->getDefinition()->addOption(new InputOption('--process-isolation', null, InputOption::VALUE_NONE, 'Launch commands from shell as a separate process.'));
        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));*/
    }

    /**
     * Gets the Kernel associated with this Console.
     *
     * @return KernelInterface A KernelInterface instance
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return integer 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        //$this->kernel->boot();


        $this->registerCommands();


        /*$container = $this->kernel->getContainer();

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        $this->setDispatcher($container->get('event_dispatcher'));

        if (true === $input->hasParameterOption(array('--shell', '-s'))) {
            $shell = new Shell($this);
            $shell->setProcessIsolation($input->hasParameterOption(array('--process-isolation')));
            $shell->run();

            return 0;
        }*/

        return parent::doRun($input, $output);
    }

    protected function registerCommands()
    {
		if (!$this->commandsRegistered) {
				$module = new \Acme\AcmeModule();
				$module->registerCommands($this);
            $this->addCommands(array(
				// Doctrine2 Commands
				new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand(),
				new \Novice\Console\Command\ReservedWordsDoctrineCommand(),
	
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearMetadataCacheDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearResultCacheDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearQueryCacheDoctrineCommand(),				
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\DropSchemaDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\EnsureProductionSettingsDoctrineCommand(),
				new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
				new \Novice\Console\Command\GenerateEntitiesDoctrineCommand(),
				new \Novice\Console\Command\GenerateProxiesDoctrineCommand(),
				new \Novice\Console\Command\GenerateRepositoriesDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\ConvertMappingDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunDqlDoctrineCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\ValidateSchemaCommand(),
				new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\InfoDoctrineCommand(),

				// Novice Commands
				new \Novice\Console\Command\GenerateModuleCommand(),

				//new \Acme\Command\GreetingCommand(),
				
				));
			$this->commandsRegistered = true;
		}
       /* foreach ($this->kernel->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }*/
    }
}