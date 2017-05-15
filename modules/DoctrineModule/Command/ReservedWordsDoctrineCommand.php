<?php

namespace DoctrineModule\Command;

use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReservedWordsDoctrineCommand extends ReservedWordsCommand
{
    protected function configure()
    {
		parent::configure();
        $this
        ->setName('doctrine:reserved-words')
		->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
            ->setHelp(<<<EOT
Checks if the current database contains tables and columns
with names that are identifiers in this dialect or in other SQL dialects.

By default SQLite, MySQL, PostgreSQL, Microsoft SQL Server, Oracle
and SQL Anywhere keywords are checked:

    <info>%command.full_name%</info>

If you want to check against specific dialects you can
pass them to the command:

    <info>%command.full_name% -l mysql -l pgsql --connection=default</info>

The following keyword lists are currently shipped with Doctrine:

    * mysql
    * mysql57
    * pgsql
    * pgsql92
    * sqlite
    * oracle
    * sqlserver
    * sqlserver2005
    * sqlserver2008
    * sqlserver2012
    * sqlanywhere
    * sqlanywhere11
    * sqlanywhere12
    * sqlanywhere16
    * db2 (Not checked by default)
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('connection'));

        parent::execute($input, $output);
    }
}