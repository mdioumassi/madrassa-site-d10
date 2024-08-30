<?php

declare(strict_types=1);

namespace Drupal\madrassa_courses\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\madrassa_courses\CourseInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the cours entity class.
 *
 * @ContentEntityType(
 *   id = "madrassa_course",
 *   label = @Translation("Cours"),
 *   label_collection = @Translation("Cours"),
 *   label_singular = @Translation("cours"),
 *   label_plural = @Translation("Cours"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Cours",
 *     plural = "@count Cours",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\madrassa_courses\CourseListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\madrassa_courses\CourseAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\madrassa_courses\Form\CourseForm",
 *       "edit" = "Drupal\madrassa_courses\Form\CourseForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "madrassa_course",
 *   data_table = "madrassa_course_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer madrassa_course",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/madrassa-course",
 *     "add-form" = "/course/add",
 *     "canonical" = "/course/{madrassa_course}",
 *     "edit-form" = "/course/{madrassa_course}/edit",
 *     "delete-form" = "/course/{madrassa_course}/delete",
 *     "delete-multiple-form" = "/admin/content/madrassa-course/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.madrassa_course.settings",
 * )
 */
class Course extends ContentEntityBase implements CourseInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the cours was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the cours was last edited.'));

    return $fields;
  }

  public function getCourseIntendedFor(): string {
    return $this->get('field_course_intended')->value === 'adult' ? 'Adulte' : 'Enfant';
  }

  public function getTypeDeCours(): string {
    return $this->get('field_type_of_course')->value === 'course_arabic' ? 'Cours d\'arabe' : 'Cours de coran';
  }

  public function getCountLevels(): int {
    $id = $this->id();
    $query = \Drupal::entityQuery('madrassa_level')
      ->accessCheck(FALSE)
      ->condition('field_course_id', $id);
    return count($query->execute());
  }
}
