<?php

namespace Drupal\cd_tools\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\InfoParserException;

/**
 * Provides test form for autocomplete textfield.
 */
class DashboardForm extends FormBase {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The module installer.
   *
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * All modules.
   *
   * @var \Drupal\Core\Extension\Extension[]
   */
  protected $modules;

  /**
   * Claro Distribution test modules.
   *
   * @var \Drupal\Core\Extension\Extension[]
   */
  protected $claroTestModules;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('module_installer')
    );
  }

  /**
   * Constructs the Dashboard form.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   *   The module installer.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ModuleInstallerInterface $module_installer) {
    $this->moduleHandler = $module_handler;
    $this->moduleInstaller = $module_installer;

    // Include system.admin.inc so we can use the sort callbacks.
    $module_handler->loadInclude('system', 'inc', 'system.admin');

    // Sort all modules by their names.
    try {
      $modules = system_rebuild_module_data();
      uasort($modules, 'system_sort_modules_by_info_name');
    }
    catch (InfoParserException $e) {
      $this->messenger()->addError($this->t('Modules could not be listed due to an error: %error', ['%error' => $e->getMessage()]));
      $modules = [];
    }
    $this->modules = $modules;
    $this->claroTestModules = array_filter(
      $modules,
      function ($extension) {
        return !empty($extension->info['claro_test']) && $extension->getType() === 'module';
      }
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cd_tools_dashboard_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $enabled_modules = array_filter(
      $this->claroTestModules,
      function ($extension) {
        return $extension->status;
      }
    );
    $installable_modules = array_filter(
      $this->claroTestModules,
      function ($extension) {
        $installable = TRUE;
        foreach ($extension->requires as $dependency => $dependency_object) {
          if (!isset($this->modules[$dependency])) {
            $installable = FALSE;
            break;
          }
        }
        return !$extension->status && $installable;
      }
    );

    if (!empty($this->claroTestModules)) {
      $form['claro_components'] = [
        '#type' => 'table',
        '#tableselect' => TRUE,
        '#caption' => $this->t('Claro Test Modules'),
        '#header' => [
          [
            'data' => $this->t('Status'),
            'class' => [RESPONSIVE_PRIORITY_MEDIUM],
          ],
          [
            'data' => $this->t('Name'),
          ],
          [
            'data' => $this->t('Description'),
            'class' => [RESPONSIVE_PRIORITY_LOW],
          ],
          [
            'data' => $this->t('Operations'),
            'class' => [RESPONSIVE_PRIORITY_MEDIUM],
          ],
        ],
      ];

      foreach ($this->claroTestModules as $name => $extension) {
        $enabled = $extension->status;
        $locked = FALSE;
        // If this module requires other modules, check their availability.
        /** @var \Drupal\Core\Extension\Dependency $dependency_object */
        foreach ($extension->requires as $dependency => $dependency_object) {
          if (!isset($this->modules[$dependency])) {
            $locked = TRUE;
            break;
          }
        }

        $form['claro_components'][$name] = [
          '#disabled' => $locked,
          '#attributes' => [
            'class' => $enabled ? ['color-success'] : [],
          ],
          'status' => [
            '#type' => 'html_tag',
            '#tag' => $enabled ? 'strong' : 'span',
            '#value' => $locked ? $this->t('Missing dependency') : ($enabled ? $this->t('Enabled') : $this->t('Disabled')),
          ],
          'name' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => [
              'class' => ['heading-d'],
            ],
            '#value' => $extension->info['name'] ?? $extension->getName(),
            '#suffix' => '<br />',
          ],
          'description' => [
            '#markup' => $extension->info['description'] ?? '',
          ],
          'operation_' . $name => [
            '#tree' => FALSE,
            '#type' => 'submit',
            '#name' => $name,
            '#module' => $name,
            '#operation' => !$locked && $enabled ? 'uninstall' : 'install',
            '#button_type' => 'small',
            '#value' => $locked ? $this->t('Disabled') : ($enabled ? $this->t('Uninstall') : $this->t('Install')),
            '#submit' => [[$this, 'operationSubmit']],
            '#disabled' => $locked,
          ],
        ];
      }
    }

    foreach (array_keys($enabled_modules) as $module) {
      $form['claro_components']['#default_value'][$module] = $module;
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply changes'),
      '#button_type' => 'primary',
    ];
    $form['actions']['enable'] = [
      '#type' => 'submit',
      '#op' => 'enable',
      '#value' => $this->t('Enable all'),
      '#disabled' => empty($installable_modules),
      '#submit' => [[$this, 'bulkEnableSubmit']],
    ];
    $form['actions']['disable'] = [
      '#type' => 'submit',
      '#op' => 'disable',
      '#value' => $this->t('Disable all'),
      '#disabled' => empty($enabled_modules),
      '#submit' => [[$this, 'bulkDisableSubmit']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $component_state_required = $form_state->getValue('claro_components');
    $modules_installed_before = array_keys(array_filter(
      $this->claroTestModules,
      function ($extension) {
        return !empty($extension->status);
      }
    ));
    $modules_installed_before = array_combine($modules_installed_before, $modules_installed_before);
    $modules_installed_after = array_filter(
      $component_state_required,
      function ($module_status) {
        return !empty($module_status);
      }
    );
    $name_walk_callback = function (&$module_name, $module, $claro_modules) {
      $module_name = $claro_modules[$module]->info['name'] ?? $module;
    };
    $success_install = $success_uninstall = TRUE;

    //
    // Module uninstall.
    //
    $modules_to_disable = array_diff($modules_installed_before, $modules_installed_after);

    if (!empty($modules_to_disable)) {
      array_walk($modules_to_disable, $name_walk_callback, $this->claroTestModules);

      if ($success_uninstall = $this->uninstallModules(array_keys($modules_to_disable))) {
        $this->messenger()->addStatus($this->uninstallMessage($modules_to_disable));
      }
    }

    //
    // Module install.
    //
    $modules_to_enable = array_diff($modules_installed_after, $modules_installed_before);

    if (!empty($modules_to_enable)) {
      array_walk($modules_to_enable, $name_walk_callback, $this->claroTestModules);
      // Filter out modules with missing dependecy.
      foreach (array_keys($modules_to_enable) as $module) {
        // If this module requires other modules, check their availability.
        /** @var \Drupal\Core\Extension\Dependency $dependency_object */
        foreach ($this->claroTestModules[$module]->requires as $dependency => $dependency_object) {
          if (!isset($this->modules[$dependency])) {
            unset($modules_to_enable[$module]);
          }
        }
      }

      if ($success_install = $this->moduleInstaller->install(array_keys($modules_to_enable))) {
        $this->messenger()->addStatus($this->installMessage($modules_to_enable));
      }
    }

    // Set errors if any.
    if (!$success_uninstall) {
      $this->messenger()->addError($this->uninstallErrorMessage());
    }
    if (!$success_install) {
      $this->messenger()->addError($this->installErrorMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function operationSubmit(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $module = $triggering_element['#name'];
    $module_name = $this->claroTestModules[$module]->info['name'] ?? $module;

    switch ($triggering_element['#operation']) {
      case 'install':
        if ($this->moduleInstaller->install([$module])) {
          $this->messenger()->addStatus($this->installMessage([$module => $module_name]));
        }
        else {
          $this->messenger()->addError($this->installErrorMessage());
        }
        break;

      case 'uninstall':
        if ($this->uninstallModules([$module])) {
          $this->messenger()->addStatus($this->uninstallMessage([$module => $module_name]));
        }
        else {
          $this->messenger()->addError($this->uninstallErrorMessage());
        }
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function bulkEnableSubmit(array &$form, FormStateInterface $form_state) {
    $modules_to_enable = array_filter(
      $this->claroTestModules,
      function ($extension) {
        return empty($extension->status);
      }
    );

    // Filter out modules with missing dependecy.
    foreach (array_keys($modules_to_enable) as $module) {
      // If this module requires other modules, check their availability.
      /** @var \Drupal\Core\Extension\Dependency $dependency_object */
      foreach ($this->claroTestModules[$module]->requires as $dependency => $dependency_object) {
        if (!isset($this->modules[$dependency])) {
          unset($modules_to_enable[$module]);
        }
      }
    }

    if (!empty($modules_to_enable)) {
      if ($this->moduleInstaller->install(array_keys($modules_to_enable))) {
        $this->messenger()->addStatus($this->t('Every Claro test module is installed.'));
      }
      else {
        $this->messenger()->addError($this->installErrorMessage());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function bulkDisableSubmit(array &$form, FormStateInterface $form_state) {
    $modules_to_enable = array_filter(
      $this->claroTestModules,
      function ($extension) {
        return !empty($extension->status);
      }
    );

    $this->uninstallModules(array_keys($modules_to_enable), TRUE);
  }

  /**
   * Helper callback to uninstall modules.
   *
   * @param string[] $modules
   *   An array of module names to uninstall.
   * @param bool $all
   *   It was a bulk operation.
   *
   * @return bool
   *   Whether the uninstall operation was successfull.
   */
  protected function uninstallModules(array $modules, $all = FALSE) {
    $reasons = $this->moduleInstaller
      ->validateUninstall($modules);

    if (!empty($reasons)) {
      $this->messenger()->addError(implode('<br />', $reasons));
      return FALSE;
    }

    if (
      empty($reasons) &&
      $this->moduleInstaller->uninstall($modules)
    ) {
      if ($all) {
        $this->messenger()->addStatus($this->t('Claro test modules are uninstalled.'));
      }
      return TRUE;
    }
    else {
      if ($all) {
        $this->messenger()->addError($this->uninstallErrorMessage());
      }
      return FALSE;
    }
  }

  /**
   * Hellper callbact to construct an install message.
   *
   * @param string[] $modules
   *   An array of installed module names, keyed by module machine name.
   *
   * @return \Drupal\Core\StringTranslation\PluralTranslatableMarkup
   *   The install message.
   */
  protected function installMessage(array $modules) {
    return $this->formatPlural(count($modules), 'Module %name has been enabled.', '@count modules have been enabled: %names.', [
      '%name' => reset($modules),
      '%names' => implode(', ', $modules),
    ]);
  }

  /**
   * Hellper callbact to return an uninstall message.
   *
   * @param string[] $modules
   *   An array of uninstalled module names, keyed by module machine name.
   *
   * @return \Drupal\Core\StringTranslation\PluralTranslatableMarkup
   *   The uninstall message.
   */
  protected function uninstallMessage(array $modules) {
    return $this->formatPlural(count($modules), 'Module %name has been uninstalled.', '@count modules have been uninstalled: %names.', [
      '%name' => reset($modules),
      '%names' => implode(', ', $modules),
    ]);
  }

  /**
   * Helper callback to return an install error message.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The general install error message.
   */
  protected function installErrorMessage() {
    return $this->t('Something went wrong during the module install process.');
  }

  /**
   * Helper callback to return an uninstall error message.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The general uninstall error message.
   */
  protected function uninstallErrorMessage() {
    return $this->t('Something went wrong during the uninstall process.');
  }

}
