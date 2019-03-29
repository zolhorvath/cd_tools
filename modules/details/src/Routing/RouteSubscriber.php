<?php

namespace Drupal\details\Routing;

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
    $overview_routes = [
      'system.site_information_settings',
    ];

    foreach ($overview_routes as $route_name) {
      if ($route = $collection->get($route_name)) {
        $route->setRequirement('_permission', 'access details test routes');
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
