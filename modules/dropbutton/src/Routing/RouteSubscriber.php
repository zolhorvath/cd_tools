<?php

namespace Drupal\dropbutton\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Modifies some route options.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $routes = [
      'entity.configurable_language.edit_form',
      'entity.configurable_language.delete_form',
    ];

    foreach ($routes as $route) {
      if ($route = $collection->get('entity.configurable_language.collection')) {
        $route->setRequirement('_permission', 'access dropbutton test routes');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    // This needs to run after user module added it's entity route.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -176];
    return $events;
  }

}
