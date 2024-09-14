<?php

declare(strict_types=1);

namespace Drupal\madrassa_registration\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\madrassa_registration\RegistrationInterface;

/**
 * Defines the inscription entity class.
 *
 * @ContentEntityType(
 *   id = "madrassa_registration",
 *   label = @Translation("Inscription"),
 *   label_collection = @Translation("Inscriptions"),
 *   label_singular = @Translation("inscription"),
 *   label_plural = @Translation("inscriptions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count inscriptions",
 *     plural = "@count inscriptions",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\madrassa_registration\RegistrationListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\madrassa_registration\RegistrationAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\madrassa_registration\Form\RegistrationForm",
 *       "edit" = "Drupal\madrassa_registration\Form\RegistrationForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "madrassa_registration",
 *   data_table = "madrassa_registration_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer madrassa_registration",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/madrassa-registration",
 *     "add-form" = "/registration/add",
 *     "canonical" = "/registration/{madrassa_registration}",
 *     "edit-form" = "/registration/{madrassa_registration}/edit",
 *     "delete-form" = "/registration/{madrassa_registration}/delete",
 *     "delete-multiple-form" = "/admin/content/madrassa-registration/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.madrassa_registration.settings",
 * )
 */
final class Registration extends ContentEntityBase implements RegistrationInterface
{

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The name of the inscription entity.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDefaultValue('Inscription enfant')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the inscription was created.'))
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
      ->setDescription(t('The time that the inscription was last edited.'));

    return $fields;
  }

  public function getRegistrationDate()
  {
    return $this->get('field_registration_date')->value;
  }

  public function getRegistrationStatus()
  {
    return $this->get('field_registration_status')->value;
  }

  public function getPaymentStatus()
  {
    return $this->get('field_payment_status')->value;
  }

  // public function getPaymentDate()
  // {
  //   return date('d/m/Y', $this->get('created')->value);
  // }

  public function getPaymentMethod()
  {
    return $this->get('field_payment_method')->value;
  }

  public function getPaymentAmount()
  {
    return $this->get('field_payment_amount')->value;
  }

  public function getChildId()
  {
    if ($this->get('field_child_id')->referencedEntities()[0] === null) {
      return '';
    }

    return $this->get('field_child_id')->referencedEntities()[0]->id();
  }

  public function getLevelId()
  {
    if ($this->get('field_level_id')->referencedEntities()[0] === null) {
      return '';
    }

    return $this->get('field_level_id')->referencedEntities()[0]->id();
  }

  public function getCourseId()
  {
    if ($this->get('field_course_id')->referencedEntities()[0] === null) {
      return '';
    }
    return $this->get('field_course_id')->referencedEntities()[0]->id();
  }

  public function getParentId()
  {
    if ($this->get('field_parent_id')->referencedEntities()[0] === null) {
      return '';
    }

    return $this->get('field_parent_id')->referencedEntities()[0]->id();
  }


  // public function getRegistration(int $child_id)
  // {
  //   // $query = \Drupal::entityQuery('madrassa_registration')
  //   //   ->condition('field_child_id', $child_id)
  //   //   ->sort('created', 'DESC')
  //   //   ->range(0, 1);

  //   // $result = $query->execute();
  //   // $registration_id = array_shift($result);
  //   $registration = \Drupal::entityTypeManager()
  //     ->getStorage('madrassa_registration')
  //     ->loadByProperties(['field_child_id' => $child_id]);
  //     dd($registration);
  //   $data = [
  //     'registration_id' => $registration->id(),
  //     'level' => $registration->getLevelLabel(),
  //     'course' => $registration->getCourseName(),
  //     'payment_status' => $registration->get('field_payment_status')->value,
  //     'payment_date' => date('d/m/Y', $registration->get('created')->value),
  //     'registration_date' => $registration->get('field_registration_date')->value,
  //   ];

  //   return $registration_id;
  // }

  public function getLevelLabel()
  {
      return $this->getLevel()->toLink();
  }

  public function getCourseName()
  {
    return $this->getCourse()->toLink();
  }

  public function getChildGender()
  {
    if ($this->getChild() === null) {
      return '';
    }

    return $this->getChild()->getGender();
  }

  public function getChildFullName()
  {
    if ($this->getChild() === null) {
      return '';
    }

    return $this->getChild()->getLinkFullName() ?? '';
  }

  public function getParentFullName()
  {
    return $this->getParent()->getLinkFullName();
  }

  public function getChild()
  {

    $child_id = $this->getChildId();

    if ($child_id === '') {
      return null;
    }

    $child = \Drupal::entityTypeManager()
      ->getStorage('children')
      ->load($child_id);

    return $child;
  }

  public function getParent()
  {
    $parent_id = $this->getParentId();

    if ($parent_id === '') {
      return null;
    }

    $parent = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->load($parent_id);

    return $parent;
  }


  public function getCourse()
  {
    $course_id = $this->getCourseId();

    if ($course_id === '') {
      return null;
    }

    $course = \Drupal::entityTypeManager()
      ->getStorage('madrassa_course')
      ->load($course_id);

    return $course;
  }

  public function getLevel()
  {
    $level_id = $this->getLevelId();

    if ($level_id === '') {
      return null;
    }

    $level = \Drupal::entityTypeManager()
      ->getStorage('madrassa_level')
      ->load($level_id);

    return $level;
  }
}
