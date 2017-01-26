<?php

namespace Rgs\CatalogModule\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RgsCatalogExtension extends Extension
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

	/*public function registerMailerConfiguration(ContainerBuilder $container)
    {
        $container->setDefinition(
			'swift.smtp_transport',
			new Definition('Swift_SmtpTransport')
		)
		->setFactory('Swift_SmtpTransport::newInstance')
		//->addMethodCall('setHost', array('relay.skynet.be'))
		->addMethodCall('setHost', array('smtp.voo.be'))
		->addMethodCall('setPort', array(25))
		//->addMethodCall('setHost', array('smtp.gmail.com'))
		//->addMethodCall('setPort', array(465))
		//->addMethodCall('setEncryption', array("ssl"))
		->addMethodCall('setUsername', array(null))
		->addMethodCall('setPassword', array(null))
		;

		$container->setDefinition(
			'swift.mailer',
			new Definition('Swift_Mailer')
		)
		->setArguments(array(new Reference('swift.smtp_transport')));
    }*/
}
