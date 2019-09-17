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
use Drupal\taxonomy\Entity\Term;

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
    $saveValue = '';
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
    
    $node->field_course_organization->target_id = $organizationId;
    //ddl($node);
    foreach ($courseData as $key => $value) {
      if ($key == 'title') {
        continue;
      }

      if ($key == 'published') {
        $node->setPublished($value);
        continue;
      }

      if ($key == 'week_hours') {
        $fieldKey = 'field_course_week_hours';
      } else if ($key == 'teaching_hours_total') {
        $fieldKey = 'field_course_length';
      } else if ($key == 'teaching_hours_per_week') {
        $fieldKey = 'field_number_of_lessons';
      } else {
        $fieldKey = 'field_'.$key;
      }


      if (!$node->hasField($fieldKey)) {
        $noticeFields[] = [
          'key' => $key,
          'message' => 'Target field not found with provided key'
        ];
        continue;
      }
      if (in_array($key, ['course_start_date', 'course_end_date'])) {
        $saveValue = $this->parseDate($value, 'Y-m-d');
      } else if (in_array($key, ['registration_start_date', 'registration_end_date'])) {
        $saveValue = $this->parseDate($value, 'Y-m-d\TH:i:s');
      } else if (in_array($key, ['books_and_materials'])) { 
        $saveValue = [
          'value' => $value,
          'format' => 'only_text',
        ];
      } else if (in_array($key, ['course_online_address', 'map_link', 'registration_link'])) {
        $saveValue = ['uri' => $value];
      } 
      else if ($key == 'course_week_hours') {
        $weekHours = $value;
        if (empty($weekHours)) {
          continue;
        }
        foreach ($weekHours as $key => $hours) {
          if (empty($hours['day']) || empty($hours['starthours']) || empty($hours['endhours'])) {
            unset($weekHours[$key]);
          }
        }

        $saveValue = $weekHours;
        
      } else if ($key == 'course_features') {
        if (empty($value)) {
          continue;
        }

        $courseFeatures = $this->parseCourseFeatures($value);
        $saveValue = $courseFeatures;
      } else if ($key == 'course_street_address') {
        $saveValue = $this->parseStreetAddress($value);
      } else if ($key == 'course_town') {
        $saveValue = $this->parseTown($value, $organizationId);

        if (!$saveValue) {
          $noticeFields[] = [
            'key' => $key,
            'message' => 'Provided town not exists in the system or not linked to the organization'
          ];
          continue;
        }
      }

      else {
        $saveValue = $value;
      }

      $node->set($fieldKey, $saveValue);
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

  /**
   * Parse course features to match Drupal taxonomy terms
   * @param  string $value            Semicolon separated string of course features
   * @return array  $courseFeatures   Array of taxonomy term tids
   */
  public function parseCourseFeatures($value) {
    $courseFeatures = [];

    $featuresArray = explode(';', $value);

    foreach ($featuresArray as $feature) {
      $courseFeature = $this->courseDataService->loadTaxonomyTermByName($feature, 'course_features');
      
      if (!$courseFeature || !is_object($courseFeature) || !method_exists($courseFeature, 'id')) {
        continue;
      }

      $courseFeatures[] = ['target_id' => $courseFeature->id()];
    }

    return $courseFeatures;
  }

  /**
   * Function to parse street address
   * @param  string $streetAddress Street address for the course
   * @return string $addressTid    Street address taxonomy term id in Drupal
   */
  public function parseStreetAddress($streetAddress) {

    $addressTid = '';

    $addressTerm = $this->courseDataService->loadTaxonomyTermByName($streetAddress, 'street_adresses');
    //\Drupal::logger('course_rest_api')->notice('<pre><code>' . print_r($addressTerm, TRUE) . '</code></pre>');
    if ($addressTerm) {
      return $addressTerm->id();
    }

    $newAddressTermId = $this->createTaxonomyTerm($streetAddress, 'street_adresses');

    return $newAddressTermId;
  }

  /**
   * Function to parse town
   * @param  string $town         Town for the course
   * @param  string $organization Organization tid
   * @return string $townTid  Town taxonomy term id in Drupal
   */
  public function parseTown($town, $organization) {

    $townTid = '';

    $townTerm = $this->courseDataService->loadTaxonomyTermByName($town, 'towns');

    if (!$townTerm) {
      return FALSE;
    }

    $isOrganizationTown = $this->courseDataService->isOrganizationTown($townTerm->id(), $organization);

    if (!$isOrganizationTown) {
      return FALSE;
    }

    return $townTerm->id();
  }

  public function createTaxonomyTerm($name, $vocabulary) {

    $term = Term::create([
      'vid' => $vocabulary,
      'name' => $name,
    ]);
    $term->save();

    return $term->id();
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