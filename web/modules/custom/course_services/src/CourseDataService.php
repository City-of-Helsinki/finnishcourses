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

    $allCourses = $entity_ids;

    return $allCourses;
  }

  public function queryUsersByRole($role) {
    $user_storage = $this->entityTypeManager->getStorage('user');

    $users = [];

    $ids = $user_storage->getQuery()
      ->condition('status', 1)
      ->condition('roles', $role)
      ->execute();

    if ($ids) {
      $users = $user_storage->loadMultiple($ids);
    }

    return $users;
  }

  /**
   * Function to get taxonomy terms in vocabulary
   * @param  string $vid    Vocabulary id 
   * @return array  $terms  Array of terms in vocabulary
   */
  public function getAllTermsInVocabulary($vid) {

    $terms = [];

    if (empty($vid)) {
      return [];
    }

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid);

    return $terms;
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

  public function getTermsFieldValue($fieldName, $term, $valueSource = 'base') {
    if (!$fieldName || !$term) {
      return FALSE;
    }

    if (!is_object($term)) {
      if (isset($term['target_id'])) {
        $term = $this->loadTaxonomyTermByTid($term['target_id']);
      } else {
        $term = $this->loadTaxonomyTermByTid($term);
      }
    }

    if (!$term && !is_object($term)) {
      return FALSE;
    }

    $fieldValue = $term->get($fieldName)->getValue();

    if (!empty($fieldValue)) {
      if ($valueSource == 'valueInArray') {
        $fieldValue = $fieldValue[0]['value'];
      }
    }

    return $fieldValue;

  }

  public function loadTaxonomyTermByTid($tid) {
    if (empty($tid)) {
      return FALSE;
    }

    return $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
  }

  public function loadAccount() {

    $account = $this->entityTypeManager->getStorage('user')
    ->load($this->currentUser->id());

    return $account;
  }

  public function loadNode($nid) {
    if (empty($nid)) {
      return FALSE;
    }

    return $this->entityTypeManager->getStorage('node')->load($nid);;
  }

  public function setNodeValue($fieldName, $value, $node) {
    if (!$fieldName || !$value || !$node) {
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

    if ($fieldName == 'published') {
      if ($value == 'FALSE') {
        $value = FALSE;
      } else {
        $value = TRUE;
      }
      $node->setPublished($value);
    }

    $node->save();
  }

}