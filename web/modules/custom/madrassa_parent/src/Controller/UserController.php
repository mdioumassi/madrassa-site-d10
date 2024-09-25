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
          $this->messenger()->addMessage($this->t('Ce parent existe déjà.'));
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
    /**@var \Drupal\madrassa_parent\Entity\MadrassaParent $parent */
    $parent = User::load($parentId);
    $build['content'] = [
      '#theme' => 'madrassa_parent_create_child',
      '#title' => $this->t('Create a new child'),
      '#parent_id' => $parentId,
      '#parent' => $parent->getFullName(),
    ];

    return $build;
  }

  public function storeChild(Request $request, int $parentId)
  {

    if ($request->isMethod('POST') && $parentId) {
      $path = "/parent/" . $parentId . "/children";

      $data = $request->request->all();
      $label = $data['firstname'] . ' ' . strtoupper($data['lastname']);
      /**@var \Drupal\madrassa_enfants\Entity\Children $child */
      $child = $this->isExistChild($label, $data['birthdate'], $parentId);
      if ($child) { 
        $is_registered = $child->getRegistrationData();
        if ($is_registered) {
          $response = new RedirectResponse($path);
          $response->send();
          $this->messenger()->addMessage($this->t('L\'enfant @child est déjà inscrit.', ['@child' => $label]));
        } else {
          $path = "/register/parent/" . $parentId . "/child/" . $child->id() . "/course";
          $response = new RedirectResponse($path);
          $response->send();
        }
        $response = new RedirectResponse($path);
        $response->send();
        $this->messenger()->addMessage($this->t('L\'enfant @child existe déjà.', ['@child' => $label]));
      } else {
        Children::create([
          'label' => $label,
          'field_birthday' => $data['birthdate'],
          'gender' => $data['gender'],
          'frenchclass' => $data['frenchclass'],
          'field_old' => $data['old'],
          'field_parent_id' => $parentId,
          'status' => 1,
        ])->save();
    
       // $this->messenger()->addMessage($this->t('Enfant @child créé avec succès.', ['@child' => $label]));
        $child = $this->isExistChild($label, $data['birthdate'], $parentId);
        if ($child) {
          $path = "/register/parent/" . $parentId . "/child/" . $child->id() . "/course";
          $response = new RedirectResponse($path);
          $response->send();
        }
  

        // $response = new RedirectResponse($path);
        // $response->send();
      }
    }
  }

  public function isExistChild(string $fullname, string $birthdate, int $parentId)
  {
    $child = \Drupal::entityTypeManager()
      ->getStorage('children')
      ->loadByProperties([
        'label' => $fullname,
        'field_birthday' => $birthdate,
        'field_parent_id' => $parentId,
      ]);
    $child_id = array_shift($child);

    return $child_id;
  }

  public function listChildren(int $parentId)
  {
    $data_children = [];

    /**@var \Drupal\madrassa_parent\Entity\MadrassaParent $parent */
    $parent = User::load($parentId);

    if (isset($parentId)) {
      $children = $this
                  ->em->getStorage('children')
                  ->loadByProperties(['field_parent_id' => $parentId]);
      if (empty($children)) {
        $this->messenger()->addMessage($this->t('Aucun enfant trouvé.'));
      }

      foreach ($children as $child) {
        /**@var \Drupal\madrassa_enfants\Entity\Children $child */
        $data_children[] = [
          'id' => $child->id() ?? '',
          'fullname' => $child->getFullName() ?? '',
          'birthdate' => $child->getBirthday() ?? '',
          'frenchclass' => $child->getFrenchClass() ?? '',
          'old' => $child->getOldOfBirthday() ?? '',
          'gender' => $child->getGender(),
          'photo' => $child->getPhoto(),
          'path' => $child->getPath() ?? '',
          'registration' => $child->getRegistrationData(),
        ];
      }
    }

    return [
      '#theme' => 'list_children',
      '#datas' => $data_children ?? [],
      '#parentId' => $parentId,
      '#parent' => $parent->getFullName(),
    ];
  }
}
