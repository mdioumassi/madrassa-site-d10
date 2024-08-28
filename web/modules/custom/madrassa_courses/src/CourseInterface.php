<?php

declare(strict_types=1);

namespace Drupal\madrassa_courses;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a cours entity type.
 */
interface CourseInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
