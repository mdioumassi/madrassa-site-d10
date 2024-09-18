<?php

declare(strict_types=1);

namespace Drupal\madrassa_registration;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining an inscription entity type.
 */
interface RegistrationInterface extends ContentEntityInterface, EntityChangedInterface {

}
