<?php

namespace Drupal\sidebar\Routing;

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
    $accessible_routes = [
      'node.add_page' => [
        'removeRequirements' => ['_node_add_access'],
      ],
      'node.add' => [
        'removeRequirements' => ['_node_add_access'],
      ],
    ];

    foreach ($accessible_routes as $route_name => $ops) {
      if ($route = $collection->get($route_name)) {
        if (
          !empty($ops['removeRequirements'])
          && is_array($ops['removeRequirements'])
        ) {
          $requirements = $route->getRequirements();

          foreach ($ops['removeRequirements'] as $route_requirement_to_remove) {
            unset($requirements[$route_requirement_to_remove]);
          }
          $route->setRequirements($requirements);
        }
        $route->setRequirement('_permission', 'access sidebar test routes');
      }
    }

    // Prevent accidental test node delete.
    if ($route = $collection->get('entity.node.delete_form')) {
      $route->setRequirement('_custom_access', '\Drupal\sidebar\Controller\SidebarTestController::deleteAccess');
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
