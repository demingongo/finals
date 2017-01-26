<?php

namespace Novice\Console\Command;

use Novice\Console\Application;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper as DoctrineBundleDoctrineCommandHelper;

abstract class DoctrineCommandHelper //extends DoctrineBundleDoctrineCommandHelper
{
	public static function setApplicationEntityManager(Application $application, $emName = "")
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $application->getApp()->getContainer()->get('managers')->getManager($emName);
        $helperSet = $application->getHelperSet();
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($em), 'em');
    }

    /**
     * Convenience method to push the helper sets of a given connection into the application.
     *
     * @param Application $application
     * @param string      $connName
     */
    public static function setApplicationConnection(Application $application, $connName = "")
    {
        $connection = $application->getApp()->getContainer()->get('doctrine')->getConnection($connName);
        $helperSet = $application->getHelperSet();
        $helperSet->set(new ConnectionHelper($connection), 'db');
    }
}