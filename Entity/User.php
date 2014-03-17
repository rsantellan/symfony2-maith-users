<?php

namespace Maith\Common\UsersBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="maith_user")
 *
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
  
  function __construct() {
    parent::__construct();
      
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
    
}