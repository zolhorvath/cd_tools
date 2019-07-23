<?php

namespace Drupal\dropbutton;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeTypeAccessControlHandler as DefaultAccessControlHandler;

/**
 * Defines the access control handler for the node type entity type.
 *
 * @see \Drupal\node\Entity\NodeType
 */
class NodeTypeAccessControlHandler extends DefaultAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access content');

      case 'delete':
        if ($entity->isLocked()) {
          return AccessResult::forbidden()->addCacheableDependency($entity);
        }
        else {
          return $this->originalCheckAccess($entity, $operation, $account)->addCacheableDependency($entity);
        }
        break;

      default:
        return $this->originalCheckAccess($entity, $operation, $account);

    }
  }

  /**
   * Performs access checks.
   *
   * This method was overwritten by the original NodeTypeAccessControlHandler's
   * checkAccess method.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to check access.
   * @param string $operation
   *   The entity operation. Usually one of 'view', 'view label', 'update' or
   *   'delete'.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  protected function originalCheckAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation == 'delete' && $entity
      ->isNew()) {
      return AccessResult::forbidden()
        ->addCacheableDependency($entity);
    }
    return AccessResult::allowedIfHasPermission($account, 'access dropbutton test routes');
  }

}
