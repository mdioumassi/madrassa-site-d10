<?php

declare(strict_types=1);

namespace Drupal\madrassa_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\madrassa_niveaux\Entity\Level;
use Drupal\madrassa_registration\Entity\Registration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Returns responses for Madrassa Inscription routes.
 */
class RegistrationController extends ControllerBase
{

  protected SessionInterface $session;
  protected EntityTypeManagerInterface $em;
  protected Level $level;

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
   * Parent choice page.
   * name: register.parent.choice
   * route: /register/parent/choice
   *
   * @return void
   */
  public function parentChoice()
  {
    $parents = $this->em->getStorage('user')->loadByProperties(['roles' => 'parent_role']);
  
    $build['content'] = [
      '#theme' => 'parent_choice',
      '#parents' => $parents,
    ];

    return $build;
  }

  /**
   * Children list page.
   * name: register.children
   * route: /register/children
   *
   * @return void
   */
  public function childrenCourse(int $parentId, int $enfantId)
  {
    if (isset($enfantId) && isset($parentId)) {
      $registration_data = [
        'child_id' => $enfantId,
        'parent_id' => $parentId,
      ];
    }

    $this->session->set('registration_data', $registration_data);

    $response = new RedirectResponse('/nos-cours/type/child?field_course_intended_value=child');
    $response->send();
  }

  /**
   * Children course type page.
   * name: register.child.course.type
   * route: /nos-cours/type/child
   * method: POST
   *
   * @return void
   */
  public function childrenCourseLevelStore(Request $request)
  {
    $registration_data = $this->session->get('registration_data');
    if ($request->isMethod('POST') && $data = $request->request->all()) {
      $levelId = $data['levelId'];
      if(isset($levelId)) {
        $level = $this->em->getStorage('madrassa_level')->load($levelId);
        $registration_data['course_id'] = $level->getCourseId();;
        $registration_data['level_id'] = $levelId;
        $this->session->set('registration_data', $registration_data);
      }
    }

    $response = new RedirectResponse('/register/child/schooling');
    $response->send();
  }

  /**
   * Schooling page.
   * name: register.child.schooling
   * route: /register/child/schooling
   * method: GET
   *
   * @return void
   */
  public function schooling()
  {
    $registration_data = $this->session->get('registration_data');

    if (isset($registration_data['child_id']) && isset($registration_data['parent_id']) 
    && isset($registration_data['level_id']) && isset($registration_data['course_id'])) {
      $child = $this->em->getStorage('children')->load($registration_data['child_id']);
      $parent = $this->em->getStorage('user')->load($registration_data['parent_id']);
      $level = $this->em->getStorage('madrassa_level')->load($registration_data['level_id']);
      $course = $this->em->getStorage('madrassa_course')->load($registration_data['course_id']);
    }

    $build['content'] = [
      '#theme' => 'schooling',
      '#child' => $child ?? '',
      '#parent' => $parent ?? '',
      '#level' => $level ?? '',
      '#course' => $course ?? '',
    ];

    return $build;
  }

  /**
   * Store schooling data.
   * name: register.child.schooling.store
   * route: /register/child/schooling/store
   * method: POST
   *
   * @param Request $request
   * @return void
   */
  public function schoolingStore(Request $request)
  {
    $registration_data = $this->session->get('registration_data');
    if ($request->isMethod('POST')) {
      $data = $request->request->all();
      $registration_data['payment_amount'] = $data['payment_amount'];
      $this->session->set('registration_data', $registration_data);
    }

    $response = new RedirectResponse('/register/child/payment');
    $response->send();
  }

  /**
   * Payment page.
   * name: register.child.payment
   * route: /register/child/payment
   * method: GET
   *
   * @return void
   */
  public function payment()
  {
    $registration_data = $this->session->get('registration_data');

    if (isset($registration_data['child_id']) && isset($registration_data['parent_id']) 
    && isset($registration_data['level_id']) && isset($registration_data['course_id'])) {
  
      $child = $this->em->getStorage('children')->load($registration_data['child_id']);
      $parent = $this->em->getStorage('user')->load($registration_data['parent_id']);
      $level = $this->em->getStorage('madrassa_level')->load($registration_data['level_id']);
      $course = $this->em->getStorage('madrassa_course')->load($registration_data['course_id']);
    }

    $build['content'] = [
      '#theme' => 'payment',
      '#child' => $child,
      '#parent' => $parent,
      '#level' => $level,
      '#course' => $course,
      '#payment_amount' => $registration_data['payment_amount'] ?? 0,
      '#date_now' => date('d/m/Y')
    ];

    return $build;
  }

  /**
   * Payment store.
   * name: register.child.payment.store
   * route: /register/child/payment/store
   *
   * @param Request $request
   * @return void
   */
  public function paymentStore(Request $request)
  {
    $registration_data = $this->session->get('registration_data');

    if ($request->isMethod('POST') && $data = $request->request->all()) {

      $registration_data['payment_method'] = $data['payment_method'];
      $registration_data['payment_date'] = $data['payment_date'];
      $registration_data['payment_note'] = $data['payment_note'];

      if ($registration_data['payment_method'] === 'espece') {
        $registration_data['payment_status'] = 'paid';
        $registration_data['registration_status'] = 'registered';
      } elseif($registration_data['payment_method'] === 'cheque') {
        $registration_data['payment_status'] = 'paid';
        $registration_data['registration_status'] = 'registered';
      } else {
        $registration_data['payment_status'] = 'pending';
        $registration_data['registration_status'] = 'registered';
      }

      $registration_data['registration_date'] = date('Y-m-d');
      $this->session->set('registration_data', $registration_data);
    }

    $response = new RedirectResponse('/register/child/recapitulatif');
    $response->send();
  }

  /**
   * Recapitulatif page.
   * name: register.child.recapitulatif
   * route: /register/child/recapitulatif
   *
   * @return void
   */
  public function recapitulatif()
  {
    $registration_data = $this->session->get('registration_data');

    if (isset($registration_data['child_id']) && isset($registration_data['parent_id']) 
    && isset($registration_data['level_id']) && isset($registration_data['course_id'])) {
  
      $child = $this->em->getStorage('children')->load($registration_data['child_id']);
      $parent = $this->em->getStorage('user')->load($registration_data['parent_id']);
      $level = $this->em->getStorage('madrassa_level')->load($registration_data['level_id']);
      $course = $this->em->getStorage('madrassa_course')->load($registration_data['course_id']);
    }

    $build['content'] = [
      '#theme' => 'recapitulatif',
      '#child' => $child,
      '#parent' => $parent,
      '#level' => $level,
      '#course' => $course,
      '#payment_amount' => $registration_data['payment_amount'] ?? 0,
      '#payment_method' => $registration_data['payment_method'] ?? '',
      '#payment_date' => $registration_data['payment_date'] ?? '',
      '#payment_status' => $registration_data['payment_status'] ?? '',
      '#payment_note' => $registration_data['payment_note'] ?? '',
      '#registration_date' => $registration_data['registration_date'] ?? '',
      '#registration_status' => $registration_data['registration_status'] ?? '',
    ];

    return $build;
  }

  /**
   * Recapitulatif store.
   * name: register.child.recapitulatif.store
   * route: /register/child/recapitulatif/store
   * method: POST
   * @return void
   */
  public function RecapitulatifStore()
  {
    $registration_data = $this->session->get('registration_data');
    Registration::create([
      'field_child_id' => $registration_data['child_id'] ?? 0,
      'field_parent_id' => $registration_data['parent_id']  ?? 0,
      'field_course_id' => $registration_data['course_id']  ?? 0,
      'field_level_id' => $registration_data['level_id']  ?? 0,
      'field_payment_amount' => $registration_data['payment_amount']  ?? 0,
      'field_payment_method' => $registration_data['payment_method']  ?? '',
      'field_payment_date' => $registration_data['payment_date']  ?? '',
      'field_payment_status' => $registration_data['payment_status']  ?? '',
      'field_payment_note' => $registration_data['payment_note']  ?? '',
      'field_registration_date' => $registration_data['registration_date']  ?? '',
      'field_registration_status' => $registration_data['registration_status']  ?? '',
    ])->save();

    $response = new RedirectResponse('/register/child/fiche');
    $response->send();
  }

  /**
   * Fiche page.
   * name: register.child.fiche
   * route: /register/child/fiche
   *
   * @return void
   */
  public function fiche($registrationId = null)
  {
    $registration_data = [];

    if ($registrationId) {
      /**@var \Drupal\madrassa_registration\Entity\Registration $registration */
      $registration = $this->em->getStorage('madrassa_registration')->load($registrationId);
      $child = $this->em->getStorage('children')->load($registration->getChildId());
      $parent = $this->em->getStorage('user')->load($registration->getParentId());
      $level = $this->em->getStorage('madrassa_level')->load($registration->getLevelId());
      $course = $this->em->getStorage('madrassa_course')->load($registration->getCourseId());
      $registration_data = [
        'child_id' => $child->id(),
        'parent_id' => $parent->id(),
        'level_id' => $level->id(),
        'course_id' => $course->id(),
        'payment_amount' => $registration->getPaymentAmount(),
        'payment_method' => $registration->getPaymentMethod(),
       // 'payment_date' => $registration->getPaymentDate(),
        'payment_status' => $registration->getPaymentStatus(),
        'payment_note' => $registration->getPaymentNote(),
        'registration_date' => $registration->getRegistrationDate(),
        'registration_status' => $registration->getRegistrationStatus(),
      ];
    }


    $registration_data = $this->session->get('registration_data') ?? $registration_data;

    if (isset($registration_data['child_id']) && isset($registration_data['parent_id']) 
    && isset($registration_data['level_id']) && isset($registration_data['course_id'])) {

      $child = $this->em->getStorage('children')->load($registration_data['child_id']);
      $parent = $this->em->getStorage('user')->load($registration_data['parent_id']);
      $level = $this->em->getStorage('madrassa_level')->load($registration_data['level_id']);
      $course = $this->em->getStorage('madrassa_course')->load($registration_data['course_id']);
    }

    $build['content'] = [
      '#theme' => 'fiche',
      '#parent_id' => $registration_data['parent_id'] ?? 0,
      '#child' => $child ?? '',
      '#parent' => $parent ?? '',
      '#level' => $level ?? '',
      '#course' => $course ?? '',
      '#payment_amount' => $registration_data['payment_amount'] ?? 0,
      '#payment_method' => $registration_data['payment_method'] ?? '',
      '#payment_date' => $registration_data['payment_date'] ?? '',
      '#payment_status' => $registration_data['payment_status'] ?? '',
      '#payment_note' => $registration_data['payment_note'] ?? '',
      '#registration_date' => $registration_data['registration_date'] ?? '',
      '#registration_status' => $registration_data['registration_status'] ?? '',
    ];

    return $build;
  }
}
