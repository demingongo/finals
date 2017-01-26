<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler;
namespace Novice\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged translation.formatter services to translation writer
 */
class PropertyEditorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('novice.property_editor.registry')) {
            return;
        }

        $definition = $container->getDefinition('novice.property_editor.registry');

        foreach ($container->findTaggedServiceIds('novice.property_editor') as $id => $attributes) {
            $definition->addMethodCall('set', array($attributes[0]['alias'], new Reference($id)));
        }
    }
}
