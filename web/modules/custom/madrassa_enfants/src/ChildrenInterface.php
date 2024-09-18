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

    public function getBirthday(): string;

    public function getOldOfBirthday(): string;

    public function getParentLink();

    public function getGender(): string;

    public function getFrenchClass(): string;

    public function getParent();

    public function getRegistrationData(): array;

    public function getFiche(): array;

}
