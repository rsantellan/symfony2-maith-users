<?php

namespace Maith\Common\UsersBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Description of User
 *
 * @ORM\Entity
 * @ORM\Table(name="maith_user")
 * @UniqueEntity("email")
 * @author Rodrigo Santellan
 */
class User extends BaseUser{

    /**
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;


    /**
     * @ORM\Column(type="string", nullable=true)
     * @var String
     */
    protected $fullName;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role", indexBy="name", inversedBy="users")
     * @ORM\JoinTable(name="maith_users_roles")
     */
    protected $user_roles;


    /**
     * @ORM\ManyToMany(targetEntity="GroupRole", indexBy="name", inversedBy="users")
     * @ORM\JoinTable(name="maith_groups_users")
     */
    protected $user_groups;
    
    
    function __construct() {
      parent::__construct();
      $this->user_roles = new ArrayCollection();
      $this->user_groups = new ArrayCollection();
    }


     /**
     * Returns an ARRAY of Role objects with the default Role object appended.
     * @return array
     */
    public function getRoles() {
        $user_roles = array();
        if($this->user_roles)
          $user_roles = $this->user_roles->toArray();
        return $user_roles;
        return array_merge($user_roles, array(new Role(array(parent::ROLE_DEFAULT))));
    }

    /**
     * Returns the true ArrayCollection of Roles.
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getRolesCollection() {
        return $this->user_roles;
    }

    /**
     * Pass a string, get the desired Role object or null.
     * @param string $role
     * @return Role|null
     */
    public function getRole($role) {
        foreach ($this->getRoles() as $roleItem) {
            if ($role == $roleItem->getRole()) {
                return $roleItem;
            }
        }
        return null;
    }

    /**
     * Pass a string, checks if we have that Role. Same functionality as getRole() except returns a real boolean.
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {
        if ($this->getRole($role)) {
            return true;
        }
        return false;
    }

    /**
     * Adds a Role OBJECT to the ArrayCollection. Can't type hint due to interface so throws Exception.
     * @throws Exception
     * @param Role $role
     */
    public function addRole($role) {
        if(is_string($role))
        {
          parent::addRole($role);
        }
        else
        {
          if($role instanceof Role)
          {
            if (!$this->hasRole($role->getRole())) {
              $this->user_roles->add($role);
            }
          }else{
            throw new \Exception(sprintf("addRole takes a Role object as the parameter. %s given", get_class($role)));
          }
        }
        
    }

    /**
     * Pass a string, remove the Role object from collection.
     * @param string $role
     */
    public function removeRole($role) {
        $roleElement = $this->getRole($role);
        if ($roleElement) {
            $this->user_roles->removeElement($roleElement);
        }
    }

    /**
     * Pass an ARRAY of Role objects and will clear the collection and re-set it with new Roles.
     * Type hinted array due to interface.
     * @param array $roles Of Role objects.
     */
    public function setRoles(array $roles) {
        $this->user_roles->clear();
        $parentRoles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
            $parentRoles[] = $role->getName();
        }
        parent::setRoles($parentRoles);
        
    }

    /**
     * Directly set the ArrayCollection of Roles. Type hinted as Collection which is the parent of (Array|Persistent)Collection.
     * @param Doctrine\Common\Collections\Collection $role
     */
    public function setRolesCollection(Collection $collection) {
        $this->user_roles = $collection;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }
    
    /**
     * Add user_roles
     *
     * @param \Maith\Common\UsersBundle\Entity\Role $userRoles
     * @return User
     */
    public function addUserRole(\Maith\Common\UsersBundle\Entity\Role $userRoles)
    {
        $this->user_roles[] = $userRoles;

        return $this;
    }

    /**
     * Remove user_roles
     *
     * @param \Maith\Common\UsersBundle\Entity\Role $userRoles
     */
    public function removeUserRole(\Maith\Common\UsersBundle\Entity\Role $userRoles)
    {
        $this->user_roles->removeElement($userRoles);
    }

    /**
     * Get user_roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserRoles()
    {
        return $this->user_roles;
    }

    /**
     * Add user_groups
     *
     * @param \Maith\Common\UsersBundle\Entity\GroupRole $userGroups
     * @return User
     */
    public function addUserGroup(\Maith\Common\UsersBundle\Entity\GroupRole $userGroups)
    {
        $this->user_groups[] = $userGroups;
        /*
        foreach($userGroups->getGroupRoles() as $role)
        {
          $this->addRole($role);
        }
        */
        return $this;
    }

    
    public function fixtureAddUserGroup(\Maith\Common\UsersBundle\Entity\GroupRole $userGroups)
    {
        $this->user_groups[] = $userGroups;
        
        foreach($userGroups->getGroupRoles() as $role)
        {
          $this->addRole($role);
        }
        
        return $this;
    }
    /**
     * Remove user_groups
     *
     * @param \Maith\Common\UsersBundle\Entity\GroupRole $userGroups
     */
    public function removeUserGroup(\Maith\Common\UsersBundle\Entity\GroupRole $userGroups)
    {
        $this->user_groups->removeElement($userGroups);
    }

    /**
     * Get user_groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserGroups()
    {
        return $this->user_groups;
    }
    
    /**
     * @param ArrayCollection $user_groups Of UserGroup objects.
     */
    public function setUserGroups(Collection $groups) {
        $this->user_groups->clear();
        foreach ($groups as $group) {
            $this->addUserGroup($group);
        }
    }    

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }
}
