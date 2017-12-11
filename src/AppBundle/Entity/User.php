<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User extends BaseUser
{
  /**
   * @var int
   */
  protected $id;

  public function __construct()
  {
    parent::__construct();
    // your own logic
  }

  public function getId()
  {
    return $this->id;
  }
}
