<?php
 
/**
 * @file
 * Contains \Drupal\course_services\CourseDataService
 *
 */
 
namespace Drupal\course_services;

//use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

class CourseDataService {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
 // protected $entityQuery;

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

  public function __construct(AccountProxyInterface $current_user, EntityTypeManagerInterface $entityTypeManager) {
   // $this->entityQuery = $entityQuery;
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

    //$query = $this->entityQuery->get('node');
	$query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->condition('type', 'course')
		  ->accessCheck(TRUE)
          ->condition('field_course_organization', $userOrganizations, 'IN')
          ->condition('status', $status);
    $query->sort('field_course_start_date.value', 'ASC');
    $entity_ids = $query->execute();

    $userCourses = $entity_ids;

    return $userCourses;
  }

  /**
   * Function to query and return courses with given conditions
   * @param  string $fieldName Array of organization ids the current user has permission to
   * @param  string $value          Status used in query
   * @return array                    Array of course ids(nodes)
   */
  public function queryCourses($fieldName, $value, $valueType = 'value') {


    //$query = $this->entityQuery->get('node');
	$query = $this->entityTypeManager->getStorage('node')->getQuery()->accessCheck(FALSE);
    $query->condition('type', 'course')
          ->condition($fieldName, $value, '=')
          ->addMetaData('account', \Drupal\user\Entity\User::load(1));
    $entity_ids = $query->execute();

    $courses = $entity_ids;

    return $courses;
  }

  public function queryAllCourses($status = 1) {
    //$query = $this->entityQuery->get('node');
	$query = $this->entityTypeManager->getStorage('node')->getQuery()->accessCheck(FALSE);
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
	  ->accessCheck(TRUE)
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

  public function loadTaxonomyTermByName($termName, $vid = '') {
    if (empty($termName)) {
      return FALSE;
    }

    $properties = [];

    $properties['name'] = $termName;

    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }

    $term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties($properties);

    return !empty($term) ? reset($term) : FALSE;
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

    return $this->entityTypeManager->getStorage('node')->load($nid);
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
        //$value = FALSE;
		//$message = "value: FALSE";
		//\Drupal::logger('course_services')->notice($message);
		$node->setUnpublished();
      } else {
        $node->setPublished();
      }
      //$node->setPublished($value);
    }
	
	
	 
    $node->save();
  }

  /**
   * Function to check if provided town is linked to organization
   * @param  string  $townId       Town taxonomy term id
   * @param  string  $organization Organization id
   * @return boolean               True or false
   */
  public function isOrganizationTown($townId, $organization) {
    $organizationTowns = $this->getOrganizationProperties($organization, 'field_locations_towns');

    if (in_array($townId, $organizationTowns)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Function to get property values from Organization. For example to get Street addresses and towns linked to Organization.
   * @param  string $organization Organization id
   * @param  string $propertyField Property type field machine name
   * @return array  $properties   Array of properties
   */
  function getOrganizationProperties($organization = '', $propertyField = '') {
  $term = $this->loadTaxonomyTermByTid($organization);

  if (!$term) {
    return FALSE;
  }

  $values = $term->get($propertyField)->getValue();

  if (empty($values)) {
    return FALSE;
  }
  $properties = [];

  foreach ($values as $key => $value) {
    $properties[] = $value['target_id'];
  }

  return $properties;
}

}