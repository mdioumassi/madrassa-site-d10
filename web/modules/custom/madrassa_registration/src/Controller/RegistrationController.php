<?php

declare(strict_types=1);

namespace Drupal\madrassa_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Returns responses for Madrassa Inscription routes.
 */
class RegistrationController extends ControllerBase {

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
  public function childrenCourse(int $enfantId)
  {
    $enfant = $this->em->getStorage('children')->load($enfantId);
    $parent_id = $enfant->get('field_parent_id')->getValue()[0]['target_id'];
    $registration_data = [
      'child_id' => $enfantId,
      'parent_id' => $parent_id,
    ];
  
    $this->session->set('registration_data', $registration_data);
    //$this->session->set('enfant_id', $enfantId);
    
    $response = new RedirectResponse('/nos-cours/type/child');
    $response->send();
  }

  public function childrenCourseLevel(Request $request)
  {
    $registration_data = $this->session->get('registration_data');
    if ($request->isMethod('POST')) {
      $data = $request->request->all();
      $levelId = $data['levelId'];
      $level = $this->em->getStorage('madrassa_level')->load($levelId);
      $registration_data['course_id'] = $level->getCourseId();;
      $registration_data['level_id'] = $levelId;
      $this->session->set('registration_data', $registration_data);
    }
  
   //dd($registration_data);

    $response = new RedirectResponse('/nos-cours/niveau/child');
    $response->send();
  }

}
