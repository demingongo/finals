<?php

namespace Novice\Console;

use Novice\Version;
use Novice\Module;
use Novice\Application as NoviceApplication;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Application extends BaseApplication
{
    private $app;
    private $commandsRegistered = false;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(NoviceApplication $app)
    {
        $this->app = $app;

		parent::__construct('Novice', Version::VERSION.' - '.$app->getName().'/'.$app->getEnvironment().($app->isDebug() ? '/debug' : ''));

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
    public function getApp()
    {
        return $this->app;
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


        $container = $this->app->getContainer();

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        /*$this->setDispatcher($container->get('event_dispatcher'));

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
            $this->addCommands(array(
				// Novice Commands
				new \Novice\Console\Command\GenerateModuleCommand()
				));
			$this->commandsRegistered = true;
		}
       foreach ($this->app->getModules() as $module) {
            if ($module instanceof Module) {
                $module->registerCommands($this);
            }
        }
    }
}