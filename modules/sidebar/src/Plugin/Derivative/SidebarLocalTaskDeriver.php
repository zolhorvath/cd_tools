<?php

namespace Drupal\sidebar\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the edit local tasks.
 */
class SidebarLocalTaskDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Creates a SidebarLocalTaskDeriver instance.
   *
   * @param string $base_plugin_id
   *   The base plugin id of the plugin.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   */
  public function __construct($base_plugin_id, EntityRepositoryInterface $entity_repository) {
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $sidebar_node_uuids = array_keys(_sidebar_nodes());
    $test_node = $this->entityRepository->loadEntityByUuid('node', reset($sidebar_node_uuids));

    if ($test_node) {
      $this->derivatives['edit'] = [
        'title' => $this->t('Edit'),
        'description' => $this->t('Edit test node'),
        'route_name' => 'entity.node.edit_form',
        'route_parameters' => ['node' => $test_node->id()],
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
