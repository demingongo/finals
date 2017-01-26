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
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * YamlFileLoader loads Yaml routing files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 *
 * @api
 */
class YamlFileLoader extends FileLoader
{
    private static $availableKeys = array(
        'resource', 'type', 'prefix', 'pattern', 'path', 'host', 'schemes', 'methods', 'defaults', 'requirements', 'options', 'condition',
    );
    private $yamlParser;

	private static $resourceNumber = 0;

    /**
     * Loads a Yaml file.
     *
     * @param string      $file A Yaml file path
     * @param string|null $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     *
     * @throws \InvalidArgumentException When a route can't be parsed because YAML is invalid
     *
     * @api
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        $config = $this->yamlParser->parse(file_get_contents($path));

		//$this->container->addResource(new FileResource($path));

		$number = ++self::$resourceNumber;

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));


        // empty file
        if (null === $config) {
            return $collection;
        }

        // not an array
        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $path));
        }

        foreach ($config as $name => $config) {
            if (isset($config['pattern'])) {
                if (isset($config['path'])) {
                    throw new \InvalidArgumentException(sprintf('The file "%s" cannot define both a "path" and a "pattern" attribute. Use only "path".', $path));
                }

                trigger_error(sprintf('The "pattern" option in file "%s" is deprecated since version 2.2 and will be removed in 3.0. Use the "path" option in the route definition instead.', $path), E_USER_DEPRECATED);

                $config['path'] = $config['pattern'];
                unset($config['pattern']);
            }

            $this->validate($config, $name, $path);

            if (isset($config['resource'])) {
                $this->parseImport($collection, $config, $path, $file);
            } else {
                $this->parseRoute($collection, $name, $config, $path);
            }
        }

		//$this->container->setDefinition('router', $collection);

        return $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && in_array(pathinfo($resource, PATHINFO_EXTENSION), array('yml', 'yaml'), true) && (!$type || 'yaml' === $type);
    }

    /**
     * Parses a route and adds it to the RouteCollection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     * @param string          $name       Route name
     * @param array           $config     Route definition
     * @param string          $path       Full path of the YAML file being processed
     */
    protected function parseRoute(/*RouteCollection*/ $collection, $name, array $config, $path)
    {
        $defaults = isset($config['defaults']) ? $config['defaults'] : array();
        $requirements = isset($config['requirements']) ? $config['requirements'] : array();
        $options = isset($config['options']) ? $config['options'] : array();
        $host = isset($config['host']) ? $config['host'] : '';
        $schemes = isset($config['schemes']) ? $config['schemes'] : array();
        $methods = isset($config['methods']) ? $config['methods'] : array();
        $condition = isset($config['condition']) ? $config['condition'] : null;

        $route = new Route($config['path'], $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
		
        $collection->add($name, $route);
    }

    /**
     * Parses an import and adds the routes in the resource to the RouteCollection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     * @param array           $config     Route definition
     * @param string          $path       Full path of the YAML file being processed
     * @param string          $file       Loaded file name
     */
    protected function parseImport(/*RouteCollection*/ $collection, array $config, $path, $file)
    {
        $type = isset($config['type']) ? $config['type'] : null;
        $prefix = isset($config['prefix']) ? $config['prefix'] : '';
        $defaults = isset($config['defaults']) ? $config['defaults'] : array();
        $requirements = isset($config['requirements']) ? $config['requirements'] : array();
        $options = isset($config['options']) ? $config['options'] : array();
        $host = isset($config['host']) ? $config['host'] : null;
        $condition = isset($config['condition']) ? $config['condition'] : null;
        $schemes = isset($config['schemes']) ? $config['schemes'] : null;
        $methods = isset($config['methods']) ? $config['methods'] : null;

        $this->setCurrentDir(dirname($path));

		$resource = $this->resolveParameters($config['resource']);

		$subCollection = $this->import($resource, $type, false, $file);

		//$this->container->addResource(new FileResource($resource));
			
		//$subCollection = $this->import($config['resource'], $type, false, $file);
        /* @var $subCollection RouteCollection */
        $subCollection->addPrefix($prefix);
        if (null !== $host) {
            $subCollection->setHost($host);
        }
        if (null !== $condition) {
            $subCollection->setCondition($condition);
        }
        if (null !== $schemes) {
            $subCollection->setSchemes($schemes);
        }
        if (null !== $methods) {
            $subCollection->setMethods($methods);
        }
        $subCollection->addDefaults($defaults);
        $subCollection->addRequirements($requirements);
        $subCollection->addOptions($options);

        $collection->addCollection($subCollection);
    }

    /**
     * Validates the route configuration.
     *
     * @param array  $config A resource config
     * @param string $name   The config key
     * @param string $path   The loaded file path
     *
     * @throws \InvalidArgumentException If one of the provided config keys is not supported,
     *                                   something is missing or the combination is nonsense
     */
    protected function validate($config, $name, $path)
    {
        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf('The definition of "%s" in "%s" must be a YAML array.', $name, $path));
        }
        if ($extraKeys = array_diff(array_keys($config), self::$availableKeys)) {
            throw new \InvalidArgumentException(sprintf(
                'The routing file "%s" contains unsupported keys for "%s": "%s". Expected one of: "%s".',
                $path, $name, implode('", "', $extraKeys), implode('", "', self::$availableKeys)
            ));
        }
        if (isset($config['resource']) && isset($config['path'])) {
            throw new \InvalidArgumentException(sprintf(
                'The routing file "%s" must not specify both the "resource" key and the "path" key for "%s". Choose between an import and a route definition.',
                $path, $name
            ));
        }
        if (!isset($config['resource']) && isset($config['type'])) {
            throw new \InvalidArgumentException(sprintf(
                'The "type" key for the route definition "%s" in "%s" is unsupported. It is only available for imports in combination with the "resource" key.',
                $name, $path
            ));
        }
        if (!isset($config['resource']) && !isset($config['path'])) {
            throw new \InvalidArgumentException(sprintf(
                'You must define a "path" for the route "%s" in file "%s".',
                $name, $path
            ));
        }
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
