<?php

declare(strict_types=1);

namespace Drupal\madrassa_parent\Entity;

use Drupal\user\Entity\User;

class MadrassaParent extends User
{


  public function getCivility(): string
  {
    return $this->get('field_civility')->value == 'mr' ? 'Monsieur' : 'Madame';
  }

  public function getLinkFullName()
  {
    return $this->toLink($this->getFullName());
  }

  public function getFullName(): string
  {
    return $this->get('field_firstname')->value . ' ' . $this->get('field_lastname')->value;
  }

  public function getPhone(): string
  {
    return $this->get('field_phone')->value;
  }

  public function getAddress(): string
  {
    return $this->get('field_address')->value;
  }

  public function getFonction(): string
  {
    return $this->get('field_fonction')->value;
  }

  public function getTypeser(): string
  {
    return $this->get('field_user_type')->value;
  }

  public function getPath()
  {
    return \Drupal::service('module_handler')->getModule('madrassa_parent')->getPath();
  }

  public function getPicture()
  {
    return $this->get('user_picture')->entity;
  }
}
