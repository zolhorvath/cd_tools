<?php

namespace Drupal\card\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Makes appearance settings page accessible.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.themes_page')) {
      $route->setRequirement('_permission', 'access card test routes');
    }
  }

}
