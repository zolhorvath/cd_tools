<?php

namespace Drupal\vertical_tabs\Routing;

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
    $test_routes = [
      'entity.block.edit_form',
    ];

    foreach ($test_routes as $route_name) {
      if ($route = $collection->get($route_name)) {
        $route->setRequirements(['_permission' => 'access vertical tabs test routes']);
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
