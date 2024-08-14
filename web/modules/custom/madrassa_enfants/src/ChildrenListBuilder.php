<?php

declare(strict_types=1);

namespace Drupal\madrassa_enfants;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the enfant entity type.
 */
final class ChildrenListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['gender'] = $this->t('Genre');
    $header['fullname'] = $this->t('Nom & Prénom');
    $header['birthdate'] = $this->t('Date de naissance');
    $header['old'] = $this->t('Age');
    $header['frenchclass'] = $this->t('Classe de français');
    $header['parent'] = $this->t('Parent');
    $header['status'] = $this->t('Status');
    $header['uid'] = $this->t('Author');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\madrassa_enfants\Entity\Children $entity */
    $row['id'] = $entity->id();
    $row['gender'] = $entity->get('gender')->value ? $this->t('Garçon') : $this->t('Fille');
    $row['fullname'] = $entity->getLink();
    $row['birthdate'] = $entity->getBirthday();
    $row['old'] = $entity->getOldOfBirthday();
    $row['frechclass'] = $entity->get('frenchclass')->value;
    $row['parent'] = $entity->getParent();
    $row['status'] = $entity->get('status')->value ? $this->t('Enabled') : $this->t('Disabled');
    $username_options = [
      'label' => 'hidden',
      'settings' => ['link' => $entity->get('uid')->entity->isAuthenticated()],
    ];
    $row['uid']['data'] = $entity->get('uid')->view($username_options);
    // $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    // $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

}
