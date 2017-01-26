<?php
namespace DoctrineModule\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('managers.event_manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'managers.event_manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'managers.event_subscriber'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
			//var_dump($taggedServices); exit(' DoctrineCompilerPass ');
            foreach ($tagAttributes as $attributes) {
				//var_dump($tagAttributes); exit(' DoctrineCompilerPass ');
                $definition->addMethodCall(
                    'addEventSubscriber',
                    array(new Reference($id))//, $attributes["connection"])
                );
            }
        }
    }
}
