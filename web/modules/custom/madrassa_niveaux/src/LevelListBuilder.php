<?php

declare(strict_types=1);

namespace Drupal\madrassa_niveaux;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the madrassa niveaux entity type.
 */
final class LevelListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
    $header['tariff'] = $this->t('Tarif');
    $header['frais_inscription'] = $this->t('Frais d\'inscription');
    $header['horaire'] = $this->t('Horaires');
    $header['courses'] = $this->t('Cours');
    $header['type_apprenant'] = $this->t('Apprenant');
    $header['status'] = $this->t('Status');
    $header['uid'] = $this->t('Author');
    // $header['created'] = $this->t('Created');
    // $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\madrassa_niveaux\Entity\Level $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->toLink();
    $row['tariff'] = $entity->getTariff();
    $row['frais_inscription'] = $entity->getFraisInscription();
    $row['horaire'] = $entity->getHoraire();
    $row['courses'] = $entity->getCourses();
    $row['type_apprenant'] = $entity->getTypeApprenant();
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
