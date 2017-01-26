<?php

namespace Novice\Middleware\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddSubscriberCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
		if (!$container->has('event_dispatcher')) {
            return;
        }

        $definition = $container->findDefinition(
            'event_dispatcher'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'app.event_subscriber'
        );
        foreach ($taggedServices as $id => $tags) {
			foreach ($tags as $attributes) {
				$definition->addMethodCall(
					'addSubscriber',
					 array(new Reference($id))
				);
			}
        }
    }
}
