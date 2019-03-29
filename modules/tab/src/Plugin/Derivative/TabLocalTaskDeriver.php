<?php

namespace Drupal\tab\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides dummy local tasks.
 */
class TabLocalTaskDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a DummyMenuLinkDeriver instance.
   *
   * @param string $base_plugin_id
   *   The base plugin id of the plugin.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct($base_plugin_id, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $filters = $this->entityTypeManager->getStorage('filter_format')->loadMultiple();
    $weight = 1;

    foreach ($filters as $filter_id => $filter) {
      $this->derivatives[$filter_id] = [
        'title' => $this->t('@filter format', [
          '@filter' => $filter->label(),
        ]),
        'description' => $this->t('Filter tips of @filter filter', [
          '@filter' => $filter->label(),
        ]),
        'route_parameters' => ['filter_format' => $filter_id],
        'weight' => $weight++,
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
