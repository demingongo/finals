<?php

namespace Rgs\CatalogModule\Command;

use Novice\Password;
use Rgs\UserModule\Entity\User;
use Rgs\UserModule\Entity\Group;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DoctrineModule\Command\Proxy\DoctrineCommandHelper;


class RgsDbInitCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('rgs:db:init')
            ->setAliases(array('rgs:db-init'))
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
       DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

       $helperSet = $this->getApplication()->getHelperSet();

       $em = $helperSet->get('em')->getEntityManager();

        $login = 'rgsroot';
        $password = 'rgsroot';
        $email = 'sdemingongo@gmail.com';


       $clientG = new Group('Client');
       $managerG = new Group('Manager', [User::ROLE_ADMIN]);
       $adminG = new Group('Admin', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN]);

       $user = new User();
       $user->setLogin($login);
       $user->setEmail($email);
       $user->setPassword(Password::hash($password));
       $user->setRoles([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN]);
       $user->setActivated(User::ACTIVATED);

       $adminG->addUser($user);

       $em->persist($clientG);
       $em->persist($managerG);
       $em->persist($adminG);

       $em->flush();

       $output->write(
                        sprintf(' Login <info>%s</info>', $login ). PHP_EOL
                    );
       $output->write(
                        sprintf(' Password <info>%s</info>', $password ). PHP_EOL
                    );
    }
}
