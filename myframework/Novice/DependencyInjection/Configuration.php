<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novice\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Configuration implements ConfigurationInterface
{
	private $environment;
    private $debug;

    /**
     * Constructor
     *
     * @param Boolean $debug Whether to use the debug mode
     */
    public function  __construct($environment, $debug)
    {
		$this->environment = $environment;
        $this->debug = (Boolean) $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('framework');

		$events = $this->getEvents();

		$rootNode
			 ->addDefaultsIfNotSet()
             ->append($this->getRouterNode())
			 ->append($this->getTemplatingNode())
			 ->append($this->getAssetNode())
			 ->fixXmlConfig('middleware')
             ->children()
                 ->arrayNode('middlewares')
                     ->useAttributeAsKey('name')
                     ->prototype('array')
						->beforeNormalization()
							->ifString()
							->then(function($v) { return array($v); })
						->end()
					 ->useAttributeAsKey('name')
					 ->prototype('scalar')->end()
					 ->end()
                 ->end()
             ->end()
			 ->fixXmlConfig('listener')
             ->children()
                 ->arrayNode('listeners')
                     ->useAttributeAsKey('name')
                     ->prototype('array')
					 ->children()
							->scalarNode('class')->isRequired()->end()
							->integerNode('priority')->min(0)->defaultValue(0)->end()
							->scalarNode('pattern')->defaultValue('^/')->end()->end()
							->fixXmlConfig('event')
							->children()
							->arrayNode('events')
							->beforeNormalization()
								->ifString()
								->then(function($v) { return array($v); })
							->end()
							->prototype('scalar')
								->info('event possible values are: "REQUEST", "VIEW", "CONTROLLER", "RESPONSE", "TERMINATE"')
									->validate()
										->ifTrue(function ($v) use ($events) {
											if (is_string($v)) {
												if (in_array(strtoupper($v), $events['names']) || in_array($v, $events['values'])) {
													return false;
												}
											}

											return true;
										})
										->thenInvalid('Invalid event value %s')
								 ->end()
								 ->validate()
										->ifString()
										->then(function ($v)  use ($events) {
											if (in_array($v, $events['values'])) {
												return $v;
											}
											
											return constant('Novice\Events::'.strtoupper($v));
										})
									->end()
								->end()
							->end()
					 ->end()
                 ->end()
             ->end();

		$this->addTranslatorSection($rootNode);

        return $treeBuilder;
    }
	

	private function getRouterNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('router');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('resource')->defaultValue('%app.root_dir%/config/routing.yml')->end()
			->end()
			->fixXmlConfig('option')
            ->children()
				->arrayNode('options')
				->addDefaultsIfNotSet()
				->children()
                    ->scalarNode('cache_dir')->defaultValue('%app.cache_dir%/symfony/routing')->end()
					->booleanNode('debug')->defaultValue($this->debug)->end()
					->scalarNode('generator_class')->defaultValue('Symfony\Component\Routing\Generator\UrlGenerator')->end()
					->scalarNode('generator_base_class')->defaultValue('Symfony\Component\Routing\Generator\UrlGenerator')->end()
					->scalarNode('generator_dumper_class')->defaultValue('Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper')->end()
					->scalarNode('generator_cache_class')->defaultValue($this->getCacheClassElementName('UrlGenerator'))->end()
					->scalarNode('matcher_class')->defaultValue('Symfony\Component\Routing\Matcher\UrlMatcher')->end()
					->scalarNode('matcher_base_class')->defaultValue('Symfony\Component\Routing\Matcher\UrlMatcher')->end()
					->scalarNode('matcher_dumper_class')->defaultValue('Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper')->end()
					->scalarNode('matcher_cache_class')->defaultValue($this->getCacheClassElementName('UrlMatcher'))->end()
					->scalarNode('resource_type')->defaultValue(null)->end()
					->booleanNode('strict_requirements')->defaultTrue()->end()
                ->end()
            ->end()
        ;

        return $node;
    }

	private function getTemplatingNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('templating');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('debugging')->defaultFalse()->end()
				->integerNode('cache_lifetime')->defaultValue(3600)->end()
			    ->scalarNode('left_delimiter')->defaultValue('{')->end()
			    ->scalarNode('right_delimiter')->defaultValue('}')->end()
			->end()
			->beforeNormalization()
                ->ifTrue(function($v) {return !isset($v['debugging']) && isset($v['debug']);})
                ->then(function($v) {
                    $v['debugging'] = $v['debug'];
                    unset($v['debug']);

                    return $v;
                })
            ->end()
        ;

        return $node;
    }

	private function getCacheClassElementName($name)
    {
        return '%app.name%'.ucfirst($this->environment).($this->debug ? 'Debug' : '').$name;
    }

	/**
     * Find proxy auto generate modes for their names and int values
     *
     * @return array
     */
    private function getEvents()
    {
        $constPrefix = '';
        $prefixLen = strlen($constPrefix);
        $refClass = new \ReflectionClass('Novice\Events');
        $constsArray = $refClass->getConstants();
        $namesArray = array();
        $valuesArray = array();

        foreach ($constsArray as $key => $value) {
            /*if (strpos($key, $constPrefix) === 0) {
                $namesArray[] = substr($key, $prefixLen);
                $valuesArray[] = (int) $value;
            }*/
			$namesArray[] = $key;
            $valuesArray[] = (string) $value;
        }
        return array(
            'names' => $namesArray,
            'values' => $valuesArray,
        );
    }

	private function getAssetNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('asset');

        $node
			->addDefaultsIfNotSet()
            ->children()
			->scalarNode('default_path')->defaultValue('/')->end()
			  ->arrayNode('strategy')
			    ->children()
			      //->scalarNode('class')->defaultValue('Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy')->end()
		          ->scalarNode('service')->end()
			      ->scalarNode('version')->end()
			      ->scalarNode('format')->defaultValue('%%s?%%s')->end()
			    ->end()
			  ->end()
			  ->arrayNode('packages')
			 
		     ->fixXmlConfig('url')
             ->children()
                 ->arrayNode('urls')
                     ->useAttributeAsKey('name')
                     ->prototype('array')
						->beforeNormalization()
							->ifString()
							->then(function($v) { return array($v); })
						->end()
				        ->useAttributeAsKey('name')
					    ->prototype('scalar')->end()
					    ->end()
					 ->end()
                 ->end()
			 
		     ->fixXmlConfig('path')
             ->children()
                 ->arrayNode('paths')
				        ->useAttributeAsKey('name')
					    ->prototype('scalar')->end()
					    ->end()
                 ->end()
             ->end()
			  ->end()
			->end()
        ;

        return $node;
    }

	private function addTranslatorSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('translator')
                    ->info('translator configuration')
                    ->canBeEnabled()
                    ->fixXmlConfig('fallback')
					->children()
						->arrayNode('fallbacks')
						->beforeNormalization()
								->ifString()
								->then(function($v) { return array($v); })
						->end()
						->beforeNormalization()
							->ifTrue(function($v) { return empty($v); })
							->then(function($v) { return array('en'); })
						->end()
						->prototype('scalar')->end()
						->defaultValue(array('en'))
                        //->scalarNode('fallback')->defaultValue('en')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
