#!/usr/bin/env php
<?php
// app/console.php

use Novice\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

use Symfony\Component\Console\Helper\HelperSet,
    Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper,
    Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    Doctrine\ORM\Tools\Console\ConsoleRunner;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//umask(0000);

set_time_limit(0);

require_once __DIR__.'/bootstrap.novice.php';
require_once __DIR__.'/App.php';


$input = new ArgvInput(array()); 
$env = $input->getParameterOption(array('--env', '-e'), getenv('NOVICE_ENV') ?: 'dev');
$debug = getenv('NOVICE_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

//dump($env);dump($debug); exit;

$app = new App($env, $debug);
$app->boot();
$application = new Application($app);
$application->run();