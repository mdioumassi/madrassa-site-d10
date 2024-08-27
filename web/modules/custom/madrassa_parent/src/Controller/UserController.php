<?php

declare(strict_types=1);

namespace Drupal\madrassa_parent\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\madrassa_enfants\Entity\Children;
use Drupal\madrassa_parent\Entity\MadrassaParent;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\user\UserAuthenticationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Madrassa parent routes.
 */
final class UserController extends ControllerBase {
  /**
   * Create a new user(parent, enseignant, adulte).
   * route: madrassa_parent.user.create
   * path: /user/{type}/create
   */
  public function createTypeUser(string $type): array {
      $build['content'] = [
        '#theme' => 'madrassa_user_create',
        '#title' => $this->t('Create a new @type', ['@type' => $type]),
        '#type' => $type,
      ];
  
      return $build;
  }

  public function storeUser(Request $request) {
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
    $path = "/admin/".$data['typeUser'].'s';
    $response = new RedirectResponse($path);
    $response->send();
  }

  public function getUserId(string $field, string $mail) {
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
      '#attached' => [
        'library' => [
          'madrassa_parent/madrassa_parent',
        ],
      ],
      '#title' => $this->t('Create a new child'),
      '#parent_id' => $parentId,
    ];

    return $build;
  }

  public function storeChild(Request $request) 
  {
  
    if ($request->isMethod('POST')) {
      $data = $request->request->all();
      $child = Children::create([
        'firstname' => $data['firstname'],
        'lastname' => $data['lastname'],
        'field_birthday' => $data['birthdate'],
        'gender' => $data['gender'],
        'frenchclass' => $data['frenchclass'],
        'parent_id' => $data['parent_id'],
        'status' => 1,
      ]);
      $child_id = $child->save();
    }
    dd($child_id);
  }

  public function listChildren($parentId)
  {
    $build['content'] = [
      '#theme' => 'madrassa_parent_list_children',
      '#attached' => [
        'library' => [
          'madrassa_parent/madrassa_parent',
        ],
      ],
      '#title' => $this->t('List of children'),
    ];

    return $build;
  } 
}