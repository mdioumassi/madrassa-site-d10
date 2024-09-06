<?php

declare(strict_types=1);

namespace Drupal\madrassa_registration;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the inscription entity type.
 */
final class RegistrationListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    // $header['id'] = $this->t('ID');
    $header['gender'] = $this->t('Genre');
    $header['child'] = $this->t('Enfant');
    $header['parent'] = $this->t('Parent');
    $header['course'] = $this->t('Cours');
    $header['level'] = $this->t('Niveau');
    $header['registration_date'] = $this->t('Date d\'inscription');
    $header['payment_method'] = $this->t('Paiement méthode');
    $header['payment_status'] = $this->t('Paiement stztus');
    $header['registration_status'] = $this->t('Inscription status');
    $header['status'] = $this->t('Status');
    
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\madrassa_registration\Entity\Registration $entity */
    // $row['id'] = $entity->toLink();
    $row['gender'] = $entity->getChildGender();
    $row['child'] = $entity->getChildFullName();
    $row['parent'] = $entity->getParentFullName();
    $row['course'] = $entity->getCourseName();
    $row['level'] = $entity->getLevelLabel();
    $row['registration_date'] = $entity->get('field_registration_date')->value;
    $row['payment_method'] = $entity->get('field_payment_method')->value;
    $row['payment_status'] = $entity->get('field_payment_status')->value;
    $row['registration_status'] = $entity->get('field_registration_status')->value;
    $row['status'] = $entity->get('status')->value ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

}
