<?php

namespace Drupal\finnishcourses_gdpr\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class for logging when sensitive user information is being viewed or edited
 *
 * 
 */
 
class FinnishcoursesGdprSubscriber implements EventSubscriberInterface {

    public function checkForUserPage(GetResponseEvent $event) {
	
	
	if ($event->getRequest()->get('_route') == 'entity.user.canonical') {
		$uid = $event->getRequest()->get('user')->uid->value;
		$account = \Drupal\user\Entity\User::load($uid); 
        $name = $account->getUsername();
		
	    // Logs a notice when users page is visited
		\Drupal::logger('finnishcourses_gdpr')->notice('user %name visited.',
			array(
				'%name' => $name,
			));
	}
	
    if ($event->getRequest()->get('_route') == 'entity.user.edit_form') {
	   $uid = $event->getRequest()->get('user')->uid->value;
	   $account = \Drupal\user\Entity\User::load($uid); // pass your uid
       $name = $account->getDisplayName();
	   
	    // Logs a notice when users edit page is visited
		\Drupal::logger('finnishcourses_gdpr')->notice('user %name edit visited.',
			array(
				'%name' => $name,
			));
    }
	
	if ($event->getRequest()->get('_route') == 'entity.user.collection') {
		// Logs a notice when users admin page is visited
		\Drupal::logger('finnishcourses_gdpr')->notice('Users admin page visited');
	}
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('checkForUserPage');
    return $events;
  }

}
