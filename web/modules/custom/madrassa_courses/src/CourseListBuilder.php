<?php

declare(strict_types=1);

namespace Drupal\madrassa_courses;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the cours entity type.
 */
final class CourseListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
    $header['course_intended_for'] = $this->t('Cours destiné aux');
    $header['type_de_cours'] = $this->t('Type de cours');
    $header['count_levels'] = $this->t('Nombre de niveaux');
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
    /** @var \Drupal\madrassa_courses\Entity\Course $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->toLink();
    $row['course_intended_for'] = $entity->getCourseIntendedFor();
    $row['type_de_cours'] = $entity->getTypeDeCours();
    $row['count_levels'] = $entity->getCountLevels();
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
