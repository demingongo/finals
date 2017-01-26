<?php
// bootstrap.novice.php

use Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\ClassLoader;

$autoload = require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../web/plugins/securimage/securimage.php';

date_default_timezone_set("Europe/Brussels");

$libraryDir = __DIR__ .'/..';
$pathtoNoviceDir = $libraryDir . '/myframework';
$moduleDir = $libraryDir . '/modules';

// Loading namespaces for Novice

$autoload->setPsr4('Novice\\', $pathtoNoviceDir . "/Novice");
$autoload->set('', $moduleDir );
$autoload->register(true);

// nestedset extensions
$loader = new ClassLoader('DoctrineExtensions\\NestedSet', $libraryDir . '/vendor-packages/doctrine2-nestedset-master/lib');
$loader->register();

// myFramework namespace
//$loader = new ClassLoader('Novice', $pathtoNoviceDir);
//$loader->register();

//mapping (example uses annotations, could be any of XML/YAML or plain PHP)
AnnotationRegistry::registerFile($libraryDir . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
AnnotationRegistry::registerLoader(array($autoload, "loadClass"));
//AnnotationRegistry::registerLoader(array($loader, "loadClass"));