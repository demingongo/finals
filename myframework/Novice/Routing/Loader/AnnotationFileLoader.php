<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novice\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Loader\AnnotationFileLoader as BaseAnnotationFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;

/**
 * YamlFileLoader loads Yaml routing files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 *
 * @api
 */
class AnnotationFileLoader extends BaseAnnotationFileLoader
{
	
	protected $container;
	
	public function __construct(Container $container, FileLocatorInterface $locator, AnnotationClassLoader $loader)
    {
        $this->container = $container;

        parent::__construct($locator, $loader);
    }
	
	
	/**
	  *
	  *{inherit}
	  */
    protected function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];

            if (!isset($token[1])) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1]) && in_array($tokens[$i][0], array(T_NS_SEPARATOR, T_STRING))) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }

            if (T_CLASS === $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }


	/**
     * Resolves services.
     *
     * @param string|array $value
     *
     * @return array|string|Reference
     */
    private function resolveServices($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, 'resolveServices'), $value);
        } elseif (is_string($value) &&  0 === strpos($value, '@=')) {
            return new Expression(substr($value, 2));
        } elseif (is_string($value) &&  0 === strpos($value, '@')) {
            if (0 === strpos($value, '@@')) {
                $value = substr($value, 1);
                $invalidBehavior = null;
            } elseif (0 === strpos($value, '@?')) {
                $value = substr($value, 2);
                $invalidBehavior = ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } else {
                $value = substr($value, 1);
                $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            }

            if ('=' === substr($value, -1)) {
                $value = substr($value, 0, -1);
                $strict = false;
            } else {
                $strict = true;
            }

            if (null !== $invalidBehavior) {
                $value = new Reference($value, $invalidBehavior, $strict);
            }
        }

        return $value;
    }

	private function resolveParameters($value)
	{		
		if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->resolve($val);
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        $container = $this->container;

        $escapedValue = preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($container, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }

            $key = strtolower($match[1]);

            if (!$container->hasParameter($key)) {
                throw new ParameterNotFoundException($key);
            }

            $resolved = $container->getParameter($key);

            if (is_string($resolved) || is_numeric($resolved)) {
                return (string) $resolved;
            }

            throw new RuntimeException(sprintf(
                'A string value must be composed of strings and/or numbers,' .
                'but found parameter "%s" of type %s inside string value "%s".',
                $key,
                gettype($resolved),
                $value)
            );

        }, $value);

        return str_replace('%%', '%', $escapedValue);
	}
}
