<?php
 
/**
 * @file
 * Contains \Drupal\course_services\CourseDataSave
 *
 */
 
namespace Drupal\course_services;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\course_services\CourseDataService;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;

class CourseDataSave {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface
   *
   * @var  \Drupal\Core\Entity\EntityTypeManagerInterface
  */
  protected $entityTypeManager;


/**
 * Drupal\course_services\courseDataService
 *
 * @var  \Drupal\course_services\CourseDataService
 */
  protected $courseDataService;

  public function __construct(QueryFactory $entityQuery, EntityTypeManagerInterface $entityTypeManager, CourseDataService $courseDataService) {
    $this->entityQuery = $entityQuery;
    $this->entityTypeManager = $entityTypeManager;
    $this->courseDataService = $courseDataService;
  }

  /**
   * Function to programmatically create a new course
   * @param  array  $courseData      Array of values to import
   * @return array $courseResponse   Response including info if course creation was succesful.
   * - boolean courseCreated
   * - string courseNid,
   * - array errorFields,
   * - array noticeFields
   */
  public function createCourse(array $courseData, $organizationId) {

    $noticeFields = [];
    // First check that we have all required data
    $requiredResponse = $this->checkRequiredData($courseData);

    // If there are error fields, return them
    if (!$requiredResponse['createCourse']) {
      return [
        'courseCreated' => FALSE,
        'courseNid' => '',
        'errorFields' => $requiredResponse['errorFields'],
        'noticeFields' => [],
      ];
    }

    $node = Node::create([
      'type' => 'course',
      'title' => $courseData['title'],
    ]);
    \Drupal::logger('course_services')->notice('Organization id is' . $organizationId);
    $node->field_course_organization->target_id = $organizationId;
    //ddl($node);
    foreach ($courseData as $key => $value) {
      $fieldKey = 'field_'.$key;
      if (in_array($key, ['course_start_date', 'course_end_date'])) {
        $date = $this->parseDate($value, 'Y-m-d');
        $node->set($fieldKey, $date); 
      } else if (in_array($key, ['registration_start_date', 'registration_end_date'])) {
        $date = $this->parseDate($value, 'Y-m-d\TH:i:s');
        $node->set($fieldKey, $date);
      }

      if (empty($courseData['number_of_lessons'])) {
        $node->set('field_number_of_lessons', 10);
      }

      if (!$node->hasField($fieldKey)) {
        $noticeFields[] = $key;
      }
    }

    $node->save();
    
    return [
      'courseCreated' => TRUE,
      'courseNid' => $node->id(),
      'errorFields' => [],
      'noticeFields' => $noticeFields,
    ];
  }

  /**
   * Function to check if course data has all the required fields for course creation
   * @param  array $courseData Course data for new course creation
   * @return [type]             [description]
   */
  public function checkRequiredData($courseData) {
    $createCourse = TRUE;
    $errorFields = [];
    if (empty($courseData['title'])) {
      $errorFields[] = 'Title';
      $createCourse = FALSE;
    }

    if (empty($courseData['course_start_date'])) {
      $errorFields[] = 'Start date';
      $createCourse = FALSE;
    }

    if (empty($courseData['course_end_date'])) {
      $errorFields[] = 'End date';
      $createCourse = FALSE;
    }

    return [
      'createCourse' => $createCourse,
      'errorFields' => $errorFields
    ];

  }

  public function parseDate($date, $dateFormat) {
    
    $dateTimestamp = strtotime($date);

    $date = DrupalDateTime::createFromTimestamp($dateTimestamp, new \DateTimeZone('Europe/Helsinki'));
    $formattedDate = $date->format($dateFormat);

    return $formattedDate;
  }

  // public function setNodeValue($fieldName, $value, $node) {
  //   if (!$fieldName || !$value || !$node) {
  //     return FALSE;
  //   }

  //   if (!is_object($node)) {
  //     if (isset($node['target_id'])) {
  //       $node = $this->loadNode($node['target_id']);
  //     } else {
  //       $node = $this->loadNode($node);
  //     }
  //   }

  //   if (!$node && !is_object($node)) {
  //     return FALSE;
  //   }

  //   if ($fieldName == 'published') {
  //     if ($value == 'FALSE') {
  //       $value = FALSE;
  //     } else {
  //       $value = TRUE;
  //     }
  //     $node->setPublished($value);
  //   }

  //   $node->save();
  // }

}