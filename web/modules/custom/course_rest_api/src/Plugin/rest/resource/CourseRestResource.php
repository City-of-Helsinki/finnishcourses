<?php
namespace Drupal\course_rest_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Drupal\course_services\CourseDataService;
use Drupal\course_services\CourseDataSave;

/**
 * Annotation for post method
 *
 * @RestResource(
 *   id = "course_rest_api_endpoint",
 *   label = @Translation("Course Rest Api Endpoint"),
 *   serialization_class = "",
 *   uri_paths = {
 *     "canonical" = "/api/v1/course",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/course",
 *   }
 * )
 */
class CourseRestResource extends ResourceBase {
  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * CourseDataService
   *
   * @var  \Drupal\course_services\CourseDataService
   */
  protected $courseDataService;

  /**
   * CourseDataSave
   *
   * @var  \Drupal\course_services\CourseDataSave
   */
  protected $courseDataSave;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   * @param  \Drupal\course_services\CourseDataService $courseDataService
   *   Service to provide course data handling
   * @param  \Drupal\course_services\CourseDataSave $courseDataSave
   *   Service to provide course data saving
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    CourseDataService $courseDataService,
    CourseDataSave $courseDataSave)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->courseDataService = $courseDataService;
    $this->courseDataSave = $courseDataSave;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('course_rest_api'),
      $container->get('current_user'),
      $container->get('course_services.course_data_service'),
      $container->get('course_services.course_data_save')
    );
  }
  /**
   * Responds to POST requests.
   *
   * Create Course content.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {
    $response_status['status'] = false;
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('create course content')) {
      throw new AccessDeniedHttpException();
    }

    if (empty($data['Organization'])) {
      throw new BadRequestHttpException('Information is missing');
    } else {
      $organization = $this->courseDataService->loadTaxonomyTermByName($data['Organization'], 'organizations');
      if (!$organization) {
        throw new BadRequestHttpException('Organization not found with provided organization name');
      }

      $organizationId = $organization->id();
    }

    if (empty($data['Courses'])) {
      throw new BadRequestHttpException('Courses array is empty');
    }

    $responseRows = [
      'errors' => [],
      'notices' => [],
    ];
    $responseMessage = '';
    $allErrorFields = '';
    foreach ($data['Courses'] as $key => $courseData) {
      $courseResponse = $this->courseDataSave->createCourse($courseData, $organizationId);
      if (!$courseResponse['courseCreated'] && !empty($courseResponse['errorFields'])) {
        $allErrorFields = implode(', ', $courseResponse['errorFields']);
        $responseMessage .= ' *** Could not create course "'.$courseData['title'].'". Missing fields: '.$allErrorFields.' *** ';       
      }
    }
    // if(!empty($data->email->value)){
    //   $system_site_config = \Drupal::config('system.site');
    //   $site_email = $system_site_config->get('mail');
    //   $mailManager = \Drupal::service('plugin.manager.mail');
    //   $module = 'custom_rest';
    //   $key = 'notice';
    //   $to = $site_email;
    //   $params['message'] = $data->message->value;
    //   $params['title'] = $data->subject->value;
    //   $params['from'] = $data->email->value;
    //   $langcode = $data->lang->value;
    //   $send = true;
    //   $response_status['status'] = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    // }
    $response = new ResourceResponse($responseMessage);
    return $response;
  }

  /**
  * Responds to GET requests.
  *
  * Returns a list of bundles for specified entity.
  *
  * @throws \Symfony\Component\HttpKernel\Exception\HttpException
  *   Throws exception expected.
  */

  public function get() {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple();
    foreach ($entities as $entity) {
      $result[$entity->id()] = $entity->title->value;
    }
    $response = new ResourceResponse($result);
    $response->addCacheableDependency($result);
    return $response;
  }
}