<?php

namespace Novice\Module\SwiftmailerModule\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SwiftmailerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container->getParameter('app.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('swiftmailer.xml');

		$mailers = array();
        foreach ($config['mailers'] as $name => $mailer) {
            $isDefaultMailer = $config['default_mailer'] === $name;
			$this->registerMailerConfiguration($container, $name, $mailer, $isDefaultMailer);
            $mailers[$name] = sprintf('swiftmailer.mailer.%s', $name);
        }
        ksort($mailers);
        $container->setParameter('swiftmailer.mailers', $mailers);
    }

	public function registerMailerConfiguration(ContainerBuilder $container, $name, $mailer, $isDefaultMailer)
    {
		if (null === $mailer['transport']) {
            $transport = 'null';
        } elseif ('gmail' === $mailer['transport']) {
            $mailer['encryption'] = 'ssl';
            $mailer['auth_mode'] = 'login';
            $mailer['host'] = 'smtp.gmail.com';
            $transport = 'smtp';
		} elseif ('voo' === $mailer['transport']) {
            //$mailer['encryption'] = null;
			$mailer['port'] = 25;
            $mailer['host'] = 'smtp.voo.be';
            $transport = 'smtp';
		} elseif (in_array($mailer['transport'], array('proximus','belgacom','skynet'))) {
            //$mailer['encryption'] = null;
			$mailer['port'] = 25;
            $mailer['host'] = 'relay.skynet.be';
            $transport = 'smtp';
        } else {
            $transport = $mailer['transport'];
        }

		if (false === $mailer['port']) {
            $mailer['port'] = 'ssl' === $mailer['encryption'] ? 465 : 25;
        }

		$definitionDecorator = new DefinitionDecorator('swiftmailer.transport.eventdispatcher.abstract');
        $container
            ->setDefinition(sprintf('swiftmailer.mailer.%s.transport.eventdispatcher', $name), $definitionDecorator)
        ;

		if ('smtp' === $transport) {
        $container->setDefinition(
			sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport),
			new Definition('Swift_SmtpTransport')
		)
		->setFactory('Swift_SmtpTransport::newInstance')
		->addMethodCall('setHost', array($mailer['host']))
		->addMethodCall('setPort', array($mailer['port']))
		->addMethodCall('setEncryption', array($mailer['encryption']))
		->addMethodCall('setUsername', array($mailer['username']))
		->addMethodCall('setPassword', array($mailer['password']))
		;
		$container->setAlias(sprintf('swiftmailer.mailer.%s.transport', $name), sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport));
		}
		elseif ('sendmail' === $transport) {
            $definitionDecorator = new DefinitionDecorator(sprintf('swiftmailer.transport.%s.abstract', $transport));
            $container
                ->setDefinition(sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport), $definitionDecorator)
                ->addArgument(new Reference(sprintf('swiftmailer.mailer.%s.transport.eventdispatcher', $name)))
            ;
            $container->setAlias(sprintf('swiftmailer.mailer.%s.transport', $name), sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport));
        } elseif ('mail' === $transport) {
            $definitionDecorator = new DefinitionDecorator(sprintf('swiftmailer.transport.%s.abstract', $transport));
            $container
                ->setDefinition(sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport), $definitionDecorator)
                ->addArgument(new Reference(sprintf('swiftmailer.mailer.%s.transport.eventdispatcher', $name)))
            ;
            $container->setAlias(sprintf('swiftmailer.mailer.%s.transport', $name), sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport));
        } elseif ('null' === $transport) {
            $definitionDecorator = new DefinitionDecorator('swiftmailer.transport.null.abstract');
            $container
                ->setDefinition(sprintf('swiftmailer.mailer.%s.transport.null', $name, $transport), $definitionDecorator)
                ->setArguments(array(
                    new Reference(sprintf('swiftmailer.mailer.%s.transport.eventdispatcher', $name)),
                ))
            ;
            $container->setAlias(sprintf('swiftmailer.mailer.%s.transport', $name), sprintf('swiftmailer.mailer.%s.transport.%s', $name, $transport));
        } else {
            $container->setAlias(sprintf('swiftmailer.mailer.%s.transport', $name), sprintf('swiftmailer.mailer.transport.%s', $transport));
        }

		$container->setDefinition(
			sprintf('swiftmailer.mailer.%s', $name),
			new Definition('Swift_Mailer')
		)
		->setArguments(array(new Reference(sprintf('swiftmailer.mailer.%s.transport', $name))));

		// alias
        if ($isDefaultMailer) {
            $container->setAlias('swiftmailer.mailer', sprintf('swiftmailer.mailer.%s', $name));
            $container->setAlias('swiftmailer.transport', sprintf('swiftmailer.mailer.%s.transport', $name));
            //$container->setParameter('swiftmailer.spool.enabled', $container->getParameter(sprintf('swiftmailer.mailer.%s.spool.enabled', $name)));
            //$container->setParameter('swiftmailer.delivery.enabled', $container->getParameter(sprintf('swiftmailer.mailer.%s.delivery.enabled', $name)));
            //$container->setParameter('swiftmailer.single_address', $container->getParameter(sprintf('swiftmailer.mailer.%s.single_address', $name)));
        }
    }
}
