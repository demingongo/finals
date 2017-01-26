<?php

namespace Novice\Routing\Loader;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;
use Symfony\Component\Config\FileLocatorInterface;


abstract class FileLoader extends BaseFileLoader
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerBuilder     $container A Container instance
     * @param FileLocatorInterface $locator   A FileLocator instance
     */
    public function __construct(Container $container, FileLocatorInterface $locator)
    {
        $this->container = $container;

        parent::__construct($locator);
    }
}
