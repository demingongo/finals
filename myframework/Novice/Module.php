<?php
namespace Novice;
/*namespace Symfony\Component\HttpKernel\Bundle
{*/
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Application as ConsoleApp;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
abstract class  Module extends ContainerAware// implements BundleInterface
{
protected $name;
protected $reflected;
protected $extension;

public function boot()
{
}

public function shutdown()
{
}
public function build(ContainerBuilder $container)
{
}

public function getContainerExtension()
{
if (null === $this->extension) {
$basename = preg_replace('/Module$/','', $this->getName());
//dump($basename);
$class = $this->getNamespace().'\\DependencyInjection\\'.$basename.'Extension';
//dump($class);
if (class_exists($class)) {
$extension = new $class();
$expectedAlias = Container::underscore($basename);
//dump($expectedAlias);
/*if ($expectedAlias != $extension->getAlias()) {
throw new \LogicException(sprintf('The extension alias for the default extension of a '.'bundle must be the underscored version of the '.'bundle name ("%s" instead of "%s")',
$expectedAlias, $extension->getAlias()
));
}*/
$this->extension = $extension;
} else {
$this->extension = false;
}
}
//dump($this->extension); //exit;
if ($this->extension) {
return $this->extension;
}
}

public function getNamespace()
{
if (null === $this->reflected) {
$this->reflected = new \ReflectionObject($this);
}
return $this->reflected->getNamespaceName();
}

public function getPath()
{
if (null === $this->reflected) {
$this->reflected = new \ReflectionObject($this);
}
return dirname($this->reflected->getFileName());
}

public function getParent()
{
return null;
}

final public function getName()
{
if (null !== $this->name) {
return $this->name;
}
$name = get_class($this);
$pos = strrpos($name,'\\');
return $this->name = false === $pos ? $name : substr($name, $pos + 1);
}

public function registerCommands(ConsoleApp $application)
{
if (!is_dir($dir = $this->getPath().'/Command')) {
return;
}
$finder = new Finder();
$finder->files()->name('*Command.php')->in($dir);
$prefix = $this->getNamespace().'\\Command';
foreach ($finder as $file) {
$ns = $prefix;
if ($relativePath = $file->getRelativePath()) {
$ns .='\\'.strtr($relativePath,'/','\\');
}
$r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
$application->add($r->newInstance());
}
}
}


public function registerExtensions(ContainerBuilder $container)
{
if (!is_dir($dir = $this->getPath().'/DependencyInjection')) {
return;
}
$finder = new Finder();
$finder->files()->name('*Extension.php')->in($dir);
$prefix = $this->getNamespace().'\\Extension';
foreach ($finder as $file) {
$ns = $prefix;
if ($relativePath = $file->getRelativePath()) {
$ns .='\\'.strtr($relativePath,'/','\\');
}
$r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
if ($r->isSubclassOf('Symfony\\Component\\DependencyInjection\\Extension\\Extension') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
$container->registerExtension($r->newInstance());
}
}
}
}