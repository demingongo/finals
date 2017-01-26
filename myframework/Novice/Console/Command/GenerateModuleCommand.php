<?php

namespace Novice\Console\Command;

use Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface;

class GenerateModuleCommand extends Command
{
	const SUFFIX = 'Module';
	
	protected $moduleName;
	protected $moduleDirname;
	protected $moduleNamespace;
	protected $moduleDir;
	
	private $module = array();
	private $directories = array(	'views' => '/Resources/views',
									'config' => '/Resources/config',
									'controller' => '/Controller',
									'dependency_injection' => '/DependencyInjection',
									'entity' => '/Entity',
									);

    protected function configure()
    {
        $this
        ->setName('novice:generate-module')
		->setAliases(array('novice:generate:module'))
		->setDescription(
            'Generate module with the name mentioned in the command.'
        )
		->setDefinition(array(
			new InputArgument(
                'namespace', InputArgument::REQUIRED,
                'The namespace of the module to create. The namespace should begin with a "vendor" name like your company name, your project name, or your client name, followed by one or more optional category sub-namespaces, and it should end with the module name itself (which must have Module as a suffix).'
            ),
            new InputOption(
                'module-name', null, InputOption::VALUE_REQUIRED,
				'The module\'s name.'
            ),
			new InputOption(
                'dir', null, InputOption::VALUE_REQUIRED,
                'The directory in which to store the module. By convention, the command detects and uses the application\'s modules/ folder.'
            ),
			new InputOption(
                'format', null, InputOption::VALUE_REQUIRED,
                'Determine the format to use for the generated configuration files (like routing). By default, the command uses the xml format.'
            ),
			new InputOption(
                'filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A string pattern used to match directories that should be processed.'
            ),
			new InputOption(
                'force', null, InputOption::VALUE_NONE,
                'Causes the files to be overwritten.'
            ),
        ))
        ->setHelp(<<<EOT
Generate module with the name mentioned in the command :

    <info>%command.full_name% namespace</info>

If the module already exist, it will only 
create the missing directories.

EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$container = $this->getApplication()->getApp()->getContainer();

		$namespace = $this->cleanNamespace($input->getArgument('namespace'));

		$pos = strrpos($namespace, "\\");
		$pos2 = 1;

		if ($pos === false) {
			$pos2 = 0;
		}

		$name = ucfirst($this->checkSuffix( substr($namespace ,$pos + $pos2), true, 'In the namespace,'));

		//echo $name."\n";

		$this->module['dirname'] = $name;

		$this->module['dirname_without_suffix'] = $this->getStringWithoutSuffix($name);

		//echo $this->module['dirname_without_suffix']."\n";

		$this->moduleNamespace = substr($namespace ,0,$pos + $pos2).$this->module['dirname'];

		/*echo $this->moduleNamespace."\n";
		exit();*/

		$optionName = $input->getOption('module-name');
		if(!empty($optionName)){		
			$name = $optionName;
			$val = preg_match('`^([A-Za-z0-9]+)$`', $name);
			if (!$val) {
				throw new \InvalidArgumentException(
					sprintf("Module name can only contain alphanumeric characters, \n'%s' is invalid.", $input->getOption('module-name'))
				);
			}
			$name = ucfirst($this->checkSuffix($name, true, 'For the module-name,'));
		}
		
		$this->moduleName = $name;

		$this->module['underscored_namespace_without_suffix'] = 
			str_replace(array("\\", "/"),"_",$container::underscore($this->getStringWithoutSuffix($this->moduleNamespace)));

		/*echo "namespace = ".$this->moduleNamespace;
		echo "\nname = ".$this->moduleName;
		exit;*/
		
		$dir = $input->getOption('dir');
		if(empty($dir)){
			$appDir = $container->getParameter('app.root_dir');
			/*$path = is_dir('app') || is_dir('App') ? '' : '../';
			if(!is_dir($path.'app') || !is_dir($path.'App')) {
				throw new \RuntimeException(
					sprintf("'app' folder cannot be found . Put the console php file in 'app' folder or precise a directory with option --dir.")
				);
			}*/
			$path = $appDir.'/../';
			$dir = $path.'modules';
		}
		
		$newModuleDir = $dir.'/'.str_replace("\\","/",$this->moduleNamespace);

		$this->moduleDir = $newModuleDir;

		$output->write(
                       PHP_EOL . sprintf('Checking directories...'). PHP_EOL
                    );

		foreach($this->directories as $dir)
		{
			if ( ! is_dir($newModuleDir.$dir) ) {
				if (! @mkdir($newModuleDir.$dir, 0777, true)) {
					throw new \InvalidArgumentException(
						sprintf('The directory "%s" does not exist and could not be created.',$newModuleDir.$dir)
					);
				}
				$output->write(
                        sprintf('  Created <info>%s</info>', $newModuleDir.$dir ). PHP_EOL
                    );
			}
		}

		$this->createFiles($input, $output);

		$output->write(
                        PHP_EOL . sprintf('OK'). PHP_EOL
                    );

    }

	private function createFiles(InputInterface $input, OutputInterface $output)
	{
		$output->write(
                        PHP_EOL . sprintf('Checking files...'). PHP_EOL
                    );

		$controllerFilename = $this->moduleDir.$this->directories['controller']."/".$this->module['dirname_without_suffix']."Controller.php";
		$controllerContent = "<?php
namespace %module.namespace%\Controller;

use Novice\BackController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class %module.dirname_without_suffix%Controller extends BackController
{
	/**
	 * @Route(\"/\", name=\"%underscored_namespace_without_suffix%_index\")
	 */
	public function executeIndex(Request %$%request)
	{	
		%$%this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}
}
";
		$routesFilename = $this->moduleDir.$this->directories['config']."/routes.xml";
		$routesContent = '<?xml version="1.0" encoding="UTF-8" ?>
<routes xmlns="http://symfony.com/schema/routing"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/routing
        http://symfony.com/schema/routing/routing-1.0.xsd">

    <route id="%underscored_namespace_without_suffix%_index" path="/">
        <default key="_controller">%module.name%:%module.dirname_without_suffix%Controller:index</default>
    </route>
</routes>
';
		
		$viewsFilename = $this->moduleDir.$this->directories['views']."/index.tpl";
		$viewsContent = '{* file:[%module.name%]index.tpl *}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}

<p>{$greetings}</p>
';

		$moduleFilename = $this->moduleDir."/".$this->moduleName.".php";
		$moduleContent = '<?php

namespace %module.namespace%;

use \Novice\Module;

class %module.name% extends Module
{
	//possibilité d\'ajouter des méthodes ou de redéfinir celles héritées 
}
';

		$AllFilesSets = array(
			'controller' => array($controllerFilename => $controllerContent),
			'config' => array($routesFilename => $routesContent),
			'views' => array($viewsFilename => $viewsContent),
			'module' => array($moduleFilename => $moduleContent),
			);

		$force = true === $input->getOption('force');
		$filter = $input->getOption('filter');

		$filesSets = array();
		if($filter) {
			$result=array_intersect(array_keys($AllFilesSets),$filter); //var_dump($result);
			if($result) {
				foreach ($result as $res) {
					$filesSets[$res] = $AllFilesSets[$res];
				}
			}
		}
		else {
			$filesSets = $AllFilesSets;
		}
		//var_dump(array_keys($filesSets));
		//exit;
		
		foreach ($filesSets as $files) {
				foreach ($files as $filename => $content) {
				
					if(!file_exists($filename) || $force) {
						$output->write(
								    sprintf('  Created <info>%s</info>', $filename). PHP_EOL
								);

						file_put_contents($filename, $this->processContent($content));

						/*
						$controllerFile = fopen($filename, "w");
						fwrite($controllerFile, $content);
						fclose($controllerFile);*/
					}
				}
		}
	}

	private function processContent($content)
	{		
		$translater = array(
            '%module.name%' => $this->moduleName,
			'%module.dirname_without_suffix%' => $this->module['dirname_without_suffix'],
			'%underscored_namespace_without_suffix%' => $this->module['underscored_namespace_without_suffix'],
			'%module.namespace%' => $this->moduleNamespace,
			'%$%' => '$',
        );

        return utf8_encode(str_replace(array_keys($translater), array_values($translater), $content));
	}

	private function cleanNamespace($string){
		$string = str_replace(array(' ', '-'), '_', $string); // Replaces all spaces with underscores.

		$string = preg_replace('#[^A-Za-z0-9/\\\\_]#', '', $string); // Removes special chars except(\/_)

		$string = str_replace('/', '\\', $string); // Replaces all backslashes with slashes.

		return preg_replace(array('/_+/', '#\\\\+#'), array('_', '\\'), $string); // Replaces multiples with single one.
	}

	private function checkSuffix($string, $strict=false, $title = ''){
		
		/*echo $string;
		exit();*/

		$suffix = $this::SUFFIX;
		if($strict && strtolower($string) == strtolower($suffix)){
			throw new \InvalidArgumentException(
				sprintf("%s you can't use \"%s\" as a name.", $title, $string, $suffix)
			);
		}

		$len = strlen($suffix);
		if($len >= strlen($string) || substr($string, -$len) != $suffix){
			throw new \InvalidArgumentException(
				sprintf("%s \"%s\" must have %s as a suffix.", $title, $string, $suffix)
			);		
		}

		return $string;
	}

	private function getStringWithoutSuffix($string){
		
		$suffix = $this::SUFFIX;

		$len = strlen($suffix);
		if($len < strlen($string) && substr($string, -$len) == $suffix){
			$string = substr($string, 0, -$len);
		}

		return $string;
	}
}