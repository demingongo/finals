<?php

namespace Novice\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand;

class GenerateRepositoriesDoctrineCommand extends GenerateRepositoriesCommand
{
    protected function configure()
    {
		parent::configure();
        $this
        ->setName('dodo:generate-repositories')
        ->setAliases(array('dodo:generate:repositories'))
		->setDefinition(array(
            new InputOption(
                'filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A string pattern used to match entities that should be processed.'
            ),
            new InputArgument(
                'dest-path', InputArgument::OPTIONAL, 'The path to generate your repository classes.', '../modules'
            )
        ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		DoctrineCommandHelper::setApplicationEntityManager($this->getApplication());

        //parent::execute($input, $output);
		$em = $this->getHelper('em')->getEntityManager();

        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $input->getOption('filter'));

        // Process destination directory
		if(null != $destPath = $input->getArgument('dest-path'))
		{
			$destPath = realpath($destPath);

			if ( ! file_exists($destPath)) {
				throw new \InvalidArgumentException(
					sprintf("Entities destination directory '<info>%s</info>' does not exist.", $destPath)
				);
			}

			if ( ! is_writable($destPath)) {
				throw new \InvalidArgumentException(
					sprintf("Entities destination directory '<info>%s</info>' does not have write permissions.", $destPath)
				);
			}
		}
		

        if (count($metadatas)) {
            $numRepositories = 0;
            $generator = new EntityRepositoryGenerator();
			
			if(null != $destPath){
				foreach ($metadatas as $metadata) {
					if ($metadata->customRepositoryClassName) {
						$output->writeln(
							sprintf('Processing repository "<info>%s</info>"', $metadata->customRepositoryClassName)
						);
						
						$generator->writeEntityRepositoryClass($metadata->customRepositoryClassName, $destPath);

						$numRepositories++;
					}
				}

				if ($numRepositories) {
					// Outputting information message
					$output->writeln(PHP_EOL . sprintf('Repository classes generated to "<info>%s</INFO>"', $destPath) );
				} else {
					$output->writeln('No Repository classes were found to be processed.' );
				}
			}
			/*else{
				$pdir = '';
				$destDirs = array();
				foreach ($metadatas as $metadata) {
					if ($metadata->customRepositoryClassName) {

						$dir = dirname($metadata->reflClass->getFileName());
						if ($dir != $pdir){
							$repositoryDir = $dir.'/Repository';
							if(!file_exists($repositoryDir)){
								mkdir($repositoryDir, 0777, true);
							}
							$destDirs[] = $repositoryDir;
							$pdir = $dir;
							$output->writeln(PHP_EOL . sprintf('Repository classes generated to "<info>%s</INFO>" :', $repositoryDir) );
						}

						$output->writeln(
							sprintf('Processing repository "<info>%s</info>"', $metadata->customRepositoryClassName)
						);
						
						$generator->writeEntityRepositoryClass($metadata->customRepositoryClassName, $repositoryDir);
					}
				}

				if(empty($destDirs)) {
					$output->writeln('No Repository classes were found to be processed.' );
				}
			}*/

        } else {
            $output->writeln('No Metadata Classes to process.' );
        }
    }
}