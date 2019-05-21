<?php

namespace Drupal\dropbutton;

use Drupal\language\LanguageAccessControlHandler as DefaultLanguageAccessControlHandler;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the language entity type.
 *
 * @see \Drupal\language\Entity\Language
 */
class LanguageAccessControlHandler extends DefaultLanguageAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return parent::checkAccess($entity, $operation, $account);

      case 'update':
        /* @var \Drupal\Core\Language\LanguageInterface $entity */
        return AccessResult::allowedIf(!$entity->isLocked())->addCacheableDependency($entity)
          ->andIf(parent::checkAccess($entity, $operation, $account)->orIf(AccessResult::allowedIfHasPermission($account, 'access dropbutton test routes')));

      case 'delete':
        /* @var \Drupal\Core\Language\LanguageInterface $entity */
        return AccessResult::allowedIf(!$entity->isLocked())->addCacheableDependency($entity)
          ->andIf(AccessResult::allowedIf(!$entity->isDefault())->addCacheableDependency($entity))
          ->andIf(parent::checkAccess($entity, $operation, $account)->orIf(AccessResult::allowedIfHasPermission($account, 'access dropbutton test routes')));

      default:
        // No opinion.
        return AccessResult::neutral();
    }
  }

}
