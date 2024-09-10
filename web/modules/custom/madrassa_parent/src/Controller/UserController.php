<?php

declare(strict_types=1);

namespace Drupal\madrassa_parent\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\madrassa_enfants\Entity\Children;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Returns responses for Madrassa parent routes.
 */
class UserController extends ControllerBase
{

  protected SessionInterface $session;
  protected EntityTypeManagerInterface $em;

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('session'),
      $container->get('entity_type.manager')
    );
  }

  public function __construct(SessionInterface $session, EntityTypeManagerInterface $entityTypeManager)
  {
    $this->session = $session;
    $this->em = $entityTypeManager;
  }

  /**
   * Create a new user(parent, enseignant, adulte).
   * route: madrassa_parent.user.create
   * path: /user/{type}/create
   */
  public function createTypeUser(string $type): array
  {
    $build['content'] = [
      '#theme' => 'madrassa_user_create',
      '#title' => $this->t('Create a new @type', ['@type' => $type]),
      '#type' => $type,
    ];

    return $build;
  }

  public function storeUser(Request $request)
  {
    if ($request->isMethod('POST')) {
      $data = $request->request->all();
      if ($this->getUserId('mail', $data['mail'])) {
        $parent_id = $this->getUserId('mail', $data['mail']);
        if (isset($parent_id)) {
          return $this->redirect('madrassa_parent.child.create', ['parentId' => $parent_id]);
        }
      }
      $values = [
        'field_civility' => $data['field_civility'] ?? '',
        'field_user_type' => $data['field_user_type'] ?? '',
        'field_fonction' => $data['field_fonction'] ?? '',
        'field_firstname' => $data['field_firstname'] ?? '',
        'field_lastname' => $data['field_lastname'] ?? '',
        'field_phone' => $data['field_phone'] ?? '',
        'field_address' => $data['field_address'] ?? '',
        'mail' => $data['mail'] ?? '',
        'name' => $data['name'] ?? '',
        'roles' => $data['role_user'] ?? '',
        'pass' => $data['pass'] ?? '',
        'status' => 1,
      ];
      User::create($values)->save();
    }
    if ($data['typeUser'] === 'parent') {
      $parent_id = $this->getUserId('mail', $data['mail']);
      if (isset($parent_id)) {
        return $this->redirect('madrassa_parent.child.create', ['parentId' => $parent_id]);
      }
    }
    $path = "/admin/" . $data['typeUser'] . 's';
    $response = new RedirectResponse($path);
    $response->send();
  }

  public function getUserId(string $field, string $mail)
  {
    $uid = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->condition($field, $mail)
      ->execute();
    $parent_id = array_shift($uid);

    return $parent_id;
  }


  public function createChild(int $parentId)
  {
    $build['content'] = [
      '#theme' => 'madrassa_parent_create_child',
      '#title' => $this->t('Create a new child'),
      '#parent_id' => $parentId,
    ];

    return $build;
  }

  public function storeChild(Request $request, int $parentId)
  {
    $path = "/parent/" . $parentId . "/children";
    if ($request->isMethod('POST')) {
      $data = $request->request->all();

      if ($this->isExistChild($data['firstname'], $data['lastname'], $data['birthdate'])) {
        // $child = $this->isExistChild($data['firstname'], $data['lastname'], $data['birthdate']);
        // if (isset($enfantId) && isset($parentId)) {
        //   $registration_data = [
        //     'child_id' => $enfantId,
        //     'parent_id' => $parentId,
        //   ];
        // }
        // $this->session->set('registration_data', $registration_data);
  
        $response = new RedirectResponse($path);
        $response->send();
      } else {
        Children::create([
          'firstname' => $data['firstname'],
          'lastname' => $data['lastname'],
          'field_birthday' => $data['birthdate'],
          'gender' => $data['gender'],
          'frenchclass' => $data['frenchclass'],
          'field_old' => $data['old'],
          'field_parent_id' => $data['parent_id'],
          'status' => 1,
        ])->save();
        $response = new RedirectResponse($path);
        $response->send();
      }

    }
  }

  public function isExistChild(string $firstname, string $lastname, string $birthdate)
  {
    $child = \Drupal::entityTypeManager()
      ->getStorage('children')
      ->loadByProperties([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'field_birthday' => $birthdate,
      ]);
    $child_id = array_shift($child);

    return $child_id;
  }

  public function listChildren(int $parentId)
  {
    /**@var \Drupal\madrassa_parent\Entity\MadrassaParent $parent */
    $parent = User::load($parentId);
    /**@var \Drupal\madrassa_enfants\Entity\Children $children */
    $children = \Drupal::entityTypeManager()
      ->getStorage('children')
      ->loadByProperties(['field_parent_id' => $parentId]);

    foreach ($children as $child) {
      $data_children[] = [
        'id' => $child->id(),
        'fullname' => $child->getFullName(),
        'birthdate' => $child->getBirthday(),
        'frenchclass' => $child->getFrenchClass(),
        'old' => $child->getOldOfBirthday(),
        'gender' => $child->getGender(),
        'photo' => $child->getPhoto(),
        'path' => $child->getPath(),
        'registration' => $child->getRegistrationData(),
      ];
    }

    return [
      '#theme' => 'list_children',
      '#title' => $this->t('Les enfants de: @parent', ['@parent' => $parent->getFullName()]),
      '#datas' => $data_children,
      '#parentId' => $parentId,
    ];
  }
}
