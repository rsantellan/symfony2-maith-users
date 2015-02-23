<?php

namespace Maith\Common\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Description of Role
 *
 * @author Rodrigo Santellan
 * @ORM\Entity
 * @ORM\Table(name="maith_user_role")
 */
class Role implements RoleInterface {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="role", type="string", unique=true, length=70)
   */
  private $role;
  
  /**
   * @var string
   *
   * @ORM\Column(name="description", type="string", length=255, nullable=true)
   */
  private $description;  

  /**
   * @ORM\ManyToMany(targetEntity="User", mappedBy="user_roles")
   */
  private $users;

  /**
   * @ORM\ManyToMany(targetEntity="GroupRole", mappedBy="groupRoles")
   */
  private $groups;

  public function __construct() {
    $this->users = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function setName($name) {
    $this->role = $name;

    return $this;
  }

  public function getName() {
    return $this->role;
  }

  public function addUser(User $user) {
    $user->addRole($this);
    $this->users->add($user);

    return $this;
  }

  public function removeUser(User $user) {
    $user->removeRole($this);
    $this->users->removeElement($user);
  }

  public function getUsers() {
    return $this->users;
  }

  function __toString() {
    return $this->getName();
  }

  public function getRole() {
    return $this->getName();
  }

  /**
   * Set role
   *
   * @param string $role
   * @return Role
   */
  public function setRole($role) {
    $this->role = $role;

    return $this;
  }

  /**
   * Add groups
   *
   * @param \Maith\Common\UsersBundle\Entity\GroupRole $groups
   * @return Role
   */
  public function addGroup(\Maith\Common\UsersBundle\Entity\GroupRole $groups) {
    $this->groups[] = $groups;

    return $this;
  }

  /**
   * Remove groups
   *
   * @param \Maith\Common\UsersBundle\Entity\GroupRole $groups
   */
  public function removeGroup(\Maith\Common\UsersBundle\Entity\GroupRole $groups) {
    $this->groups->removeElement($groups);
  }

  /**
   * Get groups
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getGroups() {
    return $this->groups;
  }


    /**
     * Set description
     *
     * @param string $description
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
