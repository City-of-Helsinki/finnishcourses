<?php
 
/**
 * @file
 * Contains \Drupal\course_services\CourseDataService
 *
 */
 
namespace Drupal\course_services;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class CourseDataService {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
  */
  protected $currentUser;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface
   *
   * @var  \Drupal\Core\Entity\EntityTypeManagerInterface
  */
  protected $entityTypeManager;

  public function __construct(QueryFactory $entityQuery, AccountProxyInterface $current_user, EntityTypeManagerInterface $entityTypeManager) {
    $this->entityQuery = $entityQuery;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Function to query and return courses with conditions current user has permissions to 
   * @param  array $userOrganizations Array of organization ids the current user has permission to
   * @param  integer $status          Status used in query
   * @return array                    Array of course ids(nodes)
   */
  public function queryUserCourses($userOrganizations = [], $status = 1) {
    
    if (empty($userOrganizations)) {
      return FALSE;
    }

    $userCourses = FALSE;

    if ($status == 'unpublished') {
      $status = 0;
    } else {
      $status = 1;
    }

    $query = $this->entityQuery->get('node');
    $query->condition('type', 'course')
          ->condition('field_course_organization', $userOrganizations, 'IN')
          ->condition('status', $status);
    $query->sort('field_course_start_date.value', 'ASC');
    $entity_ids = $query->execute();

    $userCourses = $entity_ids;

    return $userCourses;
  }

  public function queryAllCourses($status = 1) {
    $query = $this->entityQuery->get('node');
    $query->condition('type', 'course')
          ->condition('status', $status);
    $entity_ids = $query->execute();

    ddl($entity_ids);
    $allCourses = $entity_ids;

    return $allCourses;
  }


  /**
   * Function to return organizations the current user has access to
   * @param  boolean $account [description]
   * @return [type]           [description]
   */
  public function getUserOrganizations($account = FALSE) {
    
    $userOrganizations = FALSE;

    if ($account) {
      $userOrganizations = $account->get('field_organization')->getValue();
    }

    return $userOrganizations;

  }

  public function getFieldValue($fieldName, $node, $valueSource = 'base', $valueType = FALSE, $referencedEntitysField = FALSE) {
    if (!$fieldName || !$node) {
      return FALSE;
    }

    if (!is_object($node)) {
      if (isset($node['target_id'])) {
        $node = $this->loadNode($node['target_id']);
      } else {
        $node = $this->loadNode($node);
      }
    }

    if (!$node && !is_object($node)) {
      return FALSE;
    }

    $fieldValue = $node->get($fieldName)->getValue();

    if (!empty($fieldValue)) {
      if ($valueSource == 'valueInArray') {
        $fieldValue = $fieldValue[0]['value'];
      } else if ($valueType == 'referenced_term' && $referencedEntitysField) {
        if ($referencedEntitysField == 'label') {
          $fieldValue = $node->$fieldName->entity->label();
        }
      } else if ($valueSource == 'targetId') {
        $fieldValue = $fieldValue[0]['target_id'];
      }
    }

    return $fieldValue;

  }

  public function loadAccount() {

    $account = $this->entityTypeManager->getStorage('user')
    ->load($this->currentUser->id());

    return $account;
  }

  public function loadNode($nid) {

    $node = $this->entityTypeManager->getStorage('node')
    ->load($nid);

    return $node;
  }

}