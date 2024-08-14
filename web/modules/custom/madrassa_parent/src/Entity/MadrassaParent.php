<?php

declare(strict_types=1);

namespace Drupal\madrassa_parent\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\madrassa_parent\MadrassaParentInterface;
use Drupal\user\Entity\User;
use Drupal\user\EntityOwnerTrait;

class MadrassaParent extends User {


  public function getCivility(): string {
    return $this->get('field_civility')->value;
  }

}
