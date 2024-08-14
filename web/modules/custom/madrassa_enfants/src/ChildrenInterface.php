<?php

declare(strict_types=1);

namespace Drupal\madrassa_enfants;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an enfant entity type.
 */
interface ChildrenInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
