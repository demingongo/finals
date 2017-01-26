<?php

namespace Doctrine\NestedSetModule\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
//use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NestedSetExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

		//$this->registerMailerConfiguration($container);
    }

	public function registerMailerConfiguration(ContainerBuilder $container)
    {
        $container->setDefinition(
			'swift.smtp_transport',
			new Definition('Swift_SmtpTransport')
		)
		->setFactory('Swift_SmtpTransport::newInstance')
		->addMethodCall('setHost', array('relay.skynet.be'))
		->addMethodCall('setPort', array(25))
		->addMethodCall('setUsername', array(null))
		->addMethodCall('setPassword', array(null));

		$container->setDefinition(
			'swift.mailer',
			new Definition('Swift_Mailer')
		)
		->setArguments(array(new Reference('swift.smtp_transport')));
    }
}
