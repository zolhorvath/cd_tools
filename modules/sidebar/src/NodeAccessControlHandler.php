<?php

namespace Drupal\sidebar;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeAccessControlHandler as DefaultNodeAccessControlHandler;

/**
 * Extends access control handler of node.
 */
class NodeAccessControlHandler extends DefaultNodeAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $account = $this->prepareUser($account);

    if ($account->hasPermission('access sidebar test routes') && $operation === 'update') {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    return parent::access($entity, $operation, $account, $return_as_object);
  }

  /**
   * {@inheritdoc}
   */
  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = [], $return_as_object = FALSE) {
    $account = $this->prepareUser($account);

    if ($account->hasPermission('access sidebar test routes')) {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    return parent::createAccess($entity_bundle, $account, $context, $return_as_object);
  }

}
