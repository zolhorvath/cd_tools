<?php

namespace Drupal\vertical_tab\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides Vertical Tab menu link.
 */
class MenuLinkDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates a DummyMenuLinkDeriver instance.
   *
   * @param string $base_plugin_id
   *   The base plugin id of the plugin.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct($base_plugin_id, ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $theme_settings = $this->configFactory->get('system.theme');
    $admin_theme = !empty($theme_settings->get('admin'))
      ? $theme_settings->get('admin')
      : $theme_settings->get('default');

    $this->derivatives['vertical_tab.test'] = [
      'route_parameters' => ['block' => $admin_theme . '_vertical_tab_test'],
    ] + $base_plugin_definition;

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
