<?php

namespace Maith\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of LoadUserFixture
 *
 * @author Rodrigo Santellan
 */
class LoadUserFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function getOrder() {
        return 2;
    }

    public function load(ObjectManager $manager) {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('rsantellan@gmail.com');
        $user->setEmail('rsantellan@gmail.com');
        $user->setPlainPassword('1234');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        $user->setFullName("Rodrigo Santellan");
        $user->fixtureAddUserGroup($this->getReference('group-permisssion-groups'));
        $user->fixtureAddUserGroup($this->getReference('group-users'));
        $manager->persist($user);
        
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}


