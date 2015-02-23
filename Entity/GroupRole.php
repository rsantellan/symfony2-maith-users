<?php

namespace Maith\Common\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Role
 *
 * @author Rodrigo Santellan
 * @ORM\Entity
 * @ORM\Table(name="maith_user_role_group")
 */
class GroupRole {

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
   * @ORM\Column(name="name", type="string", unique=true, length=70)
   */
  private $name;

  /**
   * @ORM\ManyToMany(targetEntity="User", mappedBy="user_groups")
   */
  private $users;

  /**
   * @ORM\ManyToMany(targetEntity="Role", indexBy="name", inversedBy="groups")
   * @ORM\JoinTable(name="maith_tekoa_groups_roles")
   */
  protected $groupRoles;

  public function __construct() {
    $this->users = new ArrayCollection();
    $this->groupRoles = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
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

  /**
   * Set name
   *
   * @param string $name
   * @return GroupRole
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Add groupRoles
   *
   * @param \Maith\Common\UsersBundle\Entity\Role $groupRoles
   * @return GroupRole
   */
  public function addGroupRole(\Maith\Common\UsersBundle\Entity\Role $groupRoles) {
    $this->groupRoles[] = $groupRoles;

    return $this;
  }

  /**
   * Remove groupRoles
   *
   * @param \Maith\Common\UsersBundle\Entity\Role $groupRoles
   */
  public function removeGroupRole(\Maith\Common\UsersBundle\Entity\Role $groupRoles) {
    $this->groupRoles->removeElement($groupRoles);
  }

  /**
   * Get groupRoles
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getGroupRoles() {
    return $this->groupRoles;
  }

}
