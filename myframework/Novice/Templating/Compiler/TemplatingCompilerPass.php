<?php

namespace Novice\Templating\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TemplatingCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
		if (!$container->has('templating')) {
            return;
        }

        $definition = $container->findDefinition(
            'templating'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'templating.extension'
        );
        foreach ($taggedServices as $id => $tags) {
			foreach ($tags as $attributes) {
				$cacheable = isset($attributes["cacheable"]) ? (bool)$attributes["cacheable"] : true;
				$cache_attrs = isset($attributes["cache_attrs"]) ? explode("|",$attributes["cache_attrs"]) : array();
				$definition->addMethodCall(
					'addExtension',
					 array(new Reference($id), $cacheable, $cache_attrs)
				);

				//dump($id);
			}
        }

		//exit(__METHOD__);
    }
}
