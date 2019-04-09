<?php

namespace Drupal\courses_listings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Provides Users Courses Listing controller
 *
 */
class UsersCoursesListing extends ControllerBase {

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
  */
  protected $currentUser;

  /**
   * Drupal\Core\Entity\EntityTypeManager
   *
   * @var  \Drupal\Core\Entity\EntityTypeManager
  */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\Query\QueryInterface
   *
   * @var  \Drupal\Core\Entity\Query\QueryInterface
  */

  protected $entityQueryService;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxyInterface $current_user, EntityTypeManager $entity_manager, QueryInterface $entity_query_service) {  
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_manager;
    $this->entityQueryService = $entity_query_service;
  } 

  /**
  * {@inheritdoc}
  */
  public static function create(ContainerInterface $container) {  
    return new static(
      $container->get('current_user'),
      $container->get('entity_manager'),
      $container->get('entity.query')
    );  
  } 

  /**
   * {@inheritdoc}
   */
  public function build($nid) {

    if (!$this->currentUser) {
      return;
    }

    $account = $this->loadAccount();

    $userOrganizations = $this->getUserOrganizations($account);

    if (!$userOrganizations) {
      return;
    }

    $userCourses = $this->getUserCourses($userOrganizations);

    // $node = entity_load('node', $nid);
    // if ($node->bundle() != 'event') {
    //   return;
    // }

    // $eventAttendees = $node->get('field_event_attendees')->getValue();

    // if (empty($eventAttendees)) {
    //   $isAttendees = FALSE;
    // } else {
    //   $isAttendees = TRUE;

    //   $parsedData = $this->parseAttendees($eventAttendees);

    // }

    // return [
    //   '#theme' => 'event_attendees_table',
    //   '#attendees' => $parsedData['attendees'],
    //   '#fieldKeys' => $parsedData['fieldKeys'],
    //   '#nid' => $node->id(),
    // ];
    return [
      '#markup' => 'Testi',
    ];
  }

  public function getUserCourses($userOrganizations = FALSE) {
    $userCourses = FALSE;

    return $userCourses;
  }

  public function getUserOrganizations($account = FALSE) {
    
    $userOrganizations = FALSE;

    if ($account) {
      $userOrganizations = $account->get('field_or')->getValue();
    }

    return $userOrganizations;

  }

  public function loadAccount() {
    $account = $this->entityTypeManager->getStorage('user')
    ->load($this->currentUser->id());

    return $account;
  }
}
