<?php

declare(strict_types=1);

namespace Drupal\madrassa_enfants\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\image_test\Plugin\ImageToolkit\Operation\test\Failing;
use Drupal\madrassa_enfants\ChildrenInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the enfant entity class.
 *
 * @ContentEntityType(
 *   id = "children",
 *   label = @Translation("Enfant"),
 *   label_collection = @Translation("Enfants"),
 *   label_singular = @Translation("enfant"),
 *   label_plural = @Translation("enfants"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enfants",
 *     plural = "@count enfants",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\madrassa_enfants\ChildrenListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\madrassa_enfants\Form\ChildrenForm",
 *       "edit" = "Drupal\madrassa_enfants\Form\ChildrenForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "children",
 *   admin_permission = "administer children",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/children",
 *     "add-form" = "/children/add",
 *     "canonical" = "/children/{children}",
 *     "edit-form" = "/children/{children}/edit",
 *     "delete-form" = "/children/{children}/delete",
 *     "delete-multiple-form" = "/admin/content/children/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.children.settings",
 * )
 */
final class Children extends ContentEntityBase implements ChildrenInterface {

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

    $fields['firstname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Prénom'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['lastname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nom'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['gender'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Genre'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        'boy' => 'Garçon', 
        'girl' => 'Fille'
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

      $fields['frenchclass'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Classe française'))
      ->setRequired(FALSE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

      $fields['parent_id'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('Parent'))
        ->setDescription(t('The parent of the child.'))
        ->setSetting('target_type', 'user')
        ->setSetting('handler', 'default');

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
      ->setDescription(t('The time that the enfant was created.'))
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
      ->setDescription(t('The time that the enfant was last edited.'));

    return $fields;
  }

  public function getBirthday(): string {
    $date = new \DateTime($this->get('field_birthday')->value);
    return $date->format('d/m/Y');
  }
  public function getFullName(): string {
    return $this->get('firstname')->value . ' ' . $this->get('lastname')->value;
  }

  public function getLink() {
    return $this->toLink($this->getFullName());
  }

  public function getParent() {
    /**@var \Drupal\madrassa_parent\Entity\MadrassaParent */
    $parent = \Drupal::entityTypeManager()->getStorage('user')->load($this->get('parent_id')->target_id);

    return $parent->get('field_firstname')->value.' '.$parent->get('field_lastname')->value;
  }

  public function getOldOfBirthday(): string {
    $birthday = $this->get('field_birthday')->value;
    $today = date("Y-m-d");
    $diff = date_diff(date_create($birthday), date_create($today));

    return $diff->format('%y'). ' ans';
  }
}
