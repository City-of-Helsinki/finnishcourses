<?php

namespace Drupal\course_listings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
//use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Path\AliasManager;
use Drupal\Core\Render\Element\Dropbutton;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\course_services\CourseDataService;

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
   * Drupal\Core\Entity\EntityTypeManagerInterface
   *
   * @var  \Drupal\Core\Entity\EntityTypeManagerInterface
  */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  //protected $entityQuery;

  /**
   * Drupal\Core\Path\AliasManager definition.
   *
   * @var Drupal\Core\Path\AliasManager 
   */
  protected $aliasManager;

  /**
   * Drupal\course_services\CourseDataService definition.
   *
   * @var Drupal\course_services\CourseDataService 
   */
  protected $courseDataService;


  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxyInterface $current_user, EntityTypeManagerInterface $entityTypeManager, AliasManager $aliasManager, CourseDataService $courseDataService) {  
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entityTypeManager;
    //$this->entityQuery = $entityQuery;
    $this->aliasManager = $aliasManager;
    $this->courseDataService = $courseDataService;
  } 

  /**
  * {@inheritdoc}
  */
  public static function create(ContainerInterface $container) {  
    return new static(
      $container->get('current_user'),
      //$container->get('entity_type.manager'),
      //$container->get('entity.query'),
      $container->get('path.alias_manager'),
      $container->get('course_services.course_data_service')
    );  
  } 

  /**
   * {@inheritdoc}
   */
  public function build() {

    if (!$this->currentUser) {
      return;
    }

    $account = $this->courseDataService->loadAccount();

    $userOrganizations = $this->courseDataService->getUserOrganizations($account);

    if (!$userOrganizations) {
      return;
    }

    $organizationIds = [];

    foreach ($userOrganizations as $key => $organizationId) {
      $organizationIds[] = $organizationId['target_id'];
    }

    $published = (\Drupal::request()->query->get('published') == '0') ? 'unpublished' : '';

    $userCourses = $this->courseDataService->queryUserCourses($organizationIds, $published);

    if (!$userCourses) {
      $parsedNodes = [];
    } else {
      $parsedNodes = $this->getParsedNodes($userCourses);
    }

    $headings = $this->getTableHeadings();

    $linkPublishedOptions = ($published != 'unpublished') ? ['attributes' => ['class' => 'active']] : [];
    $linkUnPublishedOptions = ($published == 'unpublished') ? ['attributes' => ['class' => 'active']] : [];

    $pageHeading = ($published != 'unpublished') ? $this->t('Published courses') : $this->t('Unpublished courses');

    $linkPublished = Link::fromTextAndUrl($this->t('Published'), Url::fromRoute('course_listings.users_courses', [], $linkPublishedOptions));
    $linkUnPublished = Link::fromTextAndUrl($this->t('Unpublished'), Url::fromRoute('course_listings.users_courses', ['published' => '0'], $linkUnPublishedOptions));

    $navLinks = [
      'published' => $linkPublished,
      'unPublished' => $linkUnPublished,
    ];

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

    return [
      '#theme' => 'manage_courses_listing',
      '#pageHeading' => $pageHeading,
      '#courses' => $parsedNodes,
      '#tableHeadings' => $headings,
      '#navLinks' => $navLinks,
    ];
  }

  public function getParsedNodes($nids) {
    $parsedNodes = [];

    // if (empty($nids) || !is_array($nids)) {
    //   return FALSE;
    // }

    foreach ($nids as $key => $nid) {
      $node = $this->loadNode($nid);

      if ($node instanceof NodeInterface) {
        $title = $node->label();
        $organizationName = $node->field_course_organization->entity->label();
        //$format = ($event->get('field_course_start_date')->getValue()[0]['value_all_day'] == '1') ? 'j.n.Y' : 'j.n.Y, H:i';

        $courseStartDate = $node->get('field_course_start_date')->value;
        $formattedStartDate = new DrupalDateTime($courseStartDate, new \DateTimeZone('UTC'));
        $formattedStartDate->setTimezone(new \DateTimezone('Europe/Helsinki'));
        $formattedStartDate = $formattedStartDate->format('j.n.Y, H:i');

        $courseEndDate = $node->get('field_course_end_date')->value;
        $formattedEndDate = new DrupalDateTime($courseEndDate, new \DateTimeZone('UTC'));
        $formattedEndDate->setTimezone(new \DateTimezone('Europe/Helsinki'));
        $formattedEndDate = $formattedEndDate->format('j.n.Y, H:i');

        $nodeViewUrl = $node->toUrl()->toString();
        $nodeEditUrl = '/node/'.$nid.'/edit';

        $dropbutton = [
          '#type' => 'dropbutton', 
          '#links' => [
            'a' => [
              'title' => $this->t('View course'), 
              'url' =>  $nodeViewUrl,
            ],
            'b' => [
               'title' => $this->t('Yahoo'), 
               'url' =>  $nodeEditUrl,
            ],
          ],
        ];

        $parsedNodes[] = [
          'title' => $title,
          'organizationName' => $organizationName,
          'startDate' => $formattedStartDate,
          'endDate' => $formattedEndDate,
          'editUrl' => $nodeEditUrl,
        ];
      }
    }
    return $parsedNodes;
  }

  public function getTableHeadings() {
    return [$this->t('Course name'), $this->t('Organization'), $this->t('Start date'), $this->t('End date')];
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

  public function getNodeAlias($nid) {

  }
}
