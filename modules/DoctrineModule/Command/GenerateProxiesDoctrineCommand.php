<?php

namespace DoctrineModule\Command;

use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProxiesDoctrineCommand extends GenerateProxiesCommand
{
    protected function configure()
    {
		parent::configure();
        $this
        ->setName('doctrine:generate-proxies')
        ->setAliases(array('doctrine:generate:proxies'))
        ->setDescription('Generates proxy classes for entity classes.')
        /*->setDefinition(array(
            new InputOption(
                'filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A string pattern used to match entities that should be processed.'
            ),
            new InputArgument(
                'dest-path', InputArgument::OPTIONAL,
                'The path to generate your proxy classes. If none is provided, it will attempt to grab from configuration.'
            ),
        ))*/
        ->setHelp(<<<EOT
Generates proxy classes for entity classes.
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		DoctrineCommandHelper::setApplicationEntityManager($this->getApplication());

        parent::execute($input, $output);
    }
}