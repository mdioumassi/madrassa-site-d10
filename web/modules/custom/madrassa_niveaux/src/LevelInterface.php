<?php

declare(strict_types=1);

namespace Drupal\madrassa_niveaux;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a madrassa niveaux entity type.
 */
interface LevelInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
