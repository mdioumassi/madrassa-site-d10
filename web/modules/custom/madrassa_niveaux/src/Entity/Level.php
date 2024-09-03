<?php

declare(strict_types=1);

namespace Drupal\madrassa_niveaux\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Url;
use Drupal\madrassa_courses\Entity\Course;
use Drupal\madrassa_niveaux\LevelInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the madrassa niveaux entity class.
 *
 * @ContentEntityType(
 *   id = "madrassa_level",
 *   label = @Translation("Niveau"),
 *   label_collection = @Translation("Niveaux"),
 *   label_singular = @Translation("Niveau"),
 *   label_plural = @Translation("Niveaux"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Niveaux",
 *     plural = "@count Niveaux",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\madrassa_niveaux\LevelListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\madrassa_niveaux\LevelAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\madrassa_niveaux\Form\LevelForm",
 *       "edit" = "Drupal\madrassa_niveaux\Form\LevelForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "madrassa_level",
 *   data_table = "madrassa_level_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer madrassa_level",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/madrassa-level",
 *     "add-form" = "/levels/add",
 *     "canonical" = "/levels/{madrassa_level}",
 *     "edit-form" = "/levels/{madrassa_level}/edit",
 *     "delete-form" = "/levels/{madrassa_level}/delete",
 *     "delete-multiple-form" = "/admin/content/madrassa-level/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.madrassa_level.settings",
 * )
 */
class Level extends ContentEntityBase implements LevelInterface {

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

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setTranslatable(TRUE)
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
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
      ->setDescription(t('The time that the madrassa niveaux was created.'))
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
      ->setDescription(t('The time that the madrassa niveaux was last edited.'));

    return $fields;
  }
 public function getCourseId(): string {
    $course_id = $this->get('field_course_id')->referencedEntities()[0]->id();
   
    return $course_id;
  }
  public function getTariff(): string {
    return $this->get('field_tariff')->value. ' €/an';
  }
  
  public function getFraisInscription(): string {
    return $this->get('field_registration_fees')->value. ' €';
  }

  public function getHoraire(): string {
    return $this->get('field_hours')->value. ' h/semaine';
  }

  public function getCoursesLinks() {
    $url = \Drupal\Core\Url::fromRoute('entity.madrassa_course.collection');
  }

  public function getCourses(): string {
    $courses = $this->get('field_course_id')->referencedEntities();
    $courses_labels = [];
    foreach ($courses as $course) {
      $courses_labels[] = $course->label();
    }
  
    return implode(', ', $courses_labels);
  }

  public function getTypeApprenant(): string {
    $course_id = $this->get('field_course_id')->referencedEntities()[0]->id();
    $course = \Drupal::entityTypeManager()->getStorage('madrassa_course')->load($course_id);

    return $course->getCourseIntendedFor();
  }

  public function getDescription(): string {
    return $this->get('description')->value;
  }


  public function getGeneratedLink($id, $label)
  {
    $url_object = Url::fromRoute('entity.madrassa_course.canonical', ['madrassa_course' => $id]);
    $link = [
      '#type' => 'link',
      '#url' => $url_object,
      '#title' => $this->t('Read More'),
    ];
    
    return $link;
  }

  public function getTotalTariffAndFees(): int
  {

    $tariff = $this->get('field_tariff')->value;
    $fees = $this->get('field_registration_fees')->value;
    $total = $tariff + $fees;

    return $total;
  }
}
