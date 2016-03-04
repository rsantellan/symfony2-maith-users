<?php

namespace Maith\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Maith\Common\UsersBundle\Entity\Role;
use Maith\Common\UsersBundle\Entity\GroupRole;

/**
 * Description of LoadUserFixture
 *
 * @author Rodrigo Santellan
 */
class LoadPermissionsFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function getOrder() {
        return 1;
    }

    public function load(ObjectManager $manager) {
		$viewPermissionGroups = new Role();
        $viewPermissionGroups->setName("ROLE_VIEW_PERMISSION_GROUPS");
        $viewPermissionGroups->setDescription("Permiso para ver grupos de permisos");
        $manager->persist($viewPermissionGroups);
		
		$addEditPermissionGroups = new Role();
        $addEditPermissionGroups->setName("ROLE_ADD_EDIT_PERMISSION_GROUPS");
        $addEditPermissionGroups->setDescription("Permiso para agregar o editar grupos de permisos");
        $manager->persist($addEditPermissionGroups);
		
		$removePermissionGroups = new Role();
        $removePermissionGroups->setName("ROLE_REMOVE_PERMISSION_GROUPS");
        $removePermissionGroups->setDescription("Permiso para eliminar grupos de permisos");
        $manager->persist($removePermissionGroups);
		
		$viewUsers = new Role();
        $viewUsers->setName("ROLE_VIEW_USERS");
        $viewUsers->setDescription("Permiso para ver los usuarios");
        $manager->persist($viewUsers);
		
		$addEditUsers = new Role();
        $addEditUsers->setName("ROLE_ADD_EDIT_USERS");
        $addEditUsers->setDescription("Permiso para agregar o editar usuarios");
        $manager->persist($addEditUsers);
		
		$removeUsers = new Role();
        $removeUsers->setName("ROLE_REMOVE_USERS");
        $removeUsers->setDescription("Permiso para eliminar ususuarios");
        $manager->persist($removeUsers);
		
		$manager->flush();
       
		$permissionGroupsGroup = new GroupRole();
		$permissionGroupsGroup->setName("Permisos para grupos de permisos");
		$permissionGroupsGroup->addGroupRole($viewPermissionGroups);
		$permissionGroupsGroup->addGroupRole($addEditPermissionGroups);
		$permissionGroupsGroup->addGroupRole($removePermissionGroups);
		$manager->persist($permissionGroupsGroup);

		$usersGroup = new GroupRole();
		$usersGroup->setName("Permisos para usuarios");
		$usersGroup->addGroupRole($viewUsers);
		$usersGroup->addGroupRole($addEditUsers);
		$usersGroup->addGroupRole($removeUsers);
		$manager->persist($usersGroup);		
		
        $manager->flush();
        
        $roleAdmin = new Role();
        $roleAdmin->setName("ROLE_ADMIN");
        $manager->persist($roleAdmin);
        $adminGroup = new GroupRole();
        $adminGroup->setName("Grupo basico de los administradores");
        $adminGroup->addGroupRole($roleAdmin);
		$manager->persist($adminGroup);		
        
        $manager->flush();
        
        $this->addReference('group-admin', $adminGroup);
        $this->addReference('group-permisssion-groups', $permissionGroupsGroup);
        $this->addReference('group-users', $usersGroup);
        
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}


