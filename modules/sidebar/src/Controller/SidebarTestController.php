<?php

namespace Drupal\sidebar\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Access\AccessResultReasonInterface;

/**
 * Contains custom delete access for nodes.
 */
class SidebarTestController {

  /**
   * Custom access callback to prevent accidental delete of our test node.
   *
   * @param \Drupal\Core\Entity\EntityInterface $node
   *   The node object.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result, forbidden when $node is the test node.
   */
  public function deleteAccess(EntityInterface $node) {
    $access = AccessResult::allowed();

    if (in_array($node->uuid(), array_keys(_sidebar_nodes()))) {
      $access = AccessResult::forbidden();
    }

    if ($access instanceof AccessResultReasonInterface) {
      $access->setReason("Sidebar test node cannot be deleted.");
    }

    return $access;
  }

}
