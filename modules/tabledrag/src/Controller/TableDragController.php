<?php

namespace Drupal\tabledrag\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for tabledrag test forms.
 */
class TableDragController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a TableDragController object.
   *
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(FormBuilderInterface $form_builder, LanguageManagerInterface $language_manager) {
    $this->formBuilder = $form_builder;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('language_manager')
    );
  }

  /**
   * Returns the mixed height form.
   *
   * @return array
   *   The renderable array.
   */
  public function mixedHeightTable() {
    $main_rows = [
      'main1' => ['title' => "Row 1"],
      'main2' => ['title' => "Row 2"],
      'main3' => ['title' => "Row 3"],
      'main4' => ['title' => "Row 4"],
      'main5' => ['title' => "Row 5"],
    ];
    $main_rows['main2']['title_rows'] = 6;
    $main_rows['main5']['title_rows'] = 10;
    $build = $this->formBuilder->getForm('\Drupal\tabledrag\Form\TableDragTestForm', 'mixed', $main_rows);

    return $build;
  }

  /**
   * Returns the nested table without initial hierarchy.
   *
   * @return array
   *   The renderable array.
   */
  public function nestedTable() {
    $main_rows = [
      'main1' => ['title' => "Parent row 1"],
      'main2' => ['title' => "Parent row 2"],
      'main3' => ['title' => "Parent row 3"],
      'main4' => ['title' => "Parent row 4"],
      'main5' => ['title' => "Parent row 5"],
    ];
    $nested_rows = [
      'main2' => [
        'child1' => ['title' => "Child 1"],
        'child2' => ['title' => "Child 2"],
        'child3' => ['title' => "Child 3"],
      ],
    ];
    $build = $this->formBuilder->getForm('\Drupal\tabledrag\Form\TableDragTestForm', 'nested', $main_rows, $nested_rows);

    return $build;
  }

  /**
   * Returns the nested table with initial hierarchy.
   *
   * @return array
   *   The renderable array.
   */
  public function nestedWithHierarchyTable() {
    $main_rows = [
      'main2' => ['title' => "Parent row 2", 'weight' => -10],
      'main1' => ['title' => "Parent row 1", 'parent' => 'main2', 'depth' => 1],
      'main3' => ['title' => "Parent row 3", 'parent' => 'main1', 'depth' => 2],
      'main4' => ['title' => "Parent row 4", 'parent' => 'main3', 'depth' => 3],
      'main5' => ['title' => "Parent row 5", 'weight' => -9],
    ];
    $nested_rows = [
      'main1' => [
        'child2' => ['title' => "Child 2 (of parent row 1)"],
        'child1' => [
          'title' => "Child 1 (of parent row 1)",
          'parent' => 'child2',
          'depth' => 1,
        ],
        'child3' => ['title' => "Child 3 (of parent row 1)"],
      ],
    ];
    $build = $this->formBuilder->getForm('\Drupal\tabledrag\Form\TableDragTestForm', 'hierarchy', $main_rows, $nested_rows);

    $build['description'] = [
       // Display after buttons.
      '#weight' => 101,
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Expected structure'),
      ],
      'expected' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#value' => $this->getHierarchyExpectiationExample(),
        '#attributes' => [
          'style' => 'line-height: 1.15;',
        ],
      ],
    ];

    return $build;
  }

  /**
   * Constructs and returns the hierarchy example for the hierarchy table.
   *
   * @return string
   *   The hierarchy example ready for using as <pre>formatted content.
   */
  private function getHierarchyExpectiationExample() {
    $language_is_ltr = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getDirection() === 'ltr';

    $d_lt = $language_is_ltr ? '╔' : '╗';
    $d_rt = $language_is_ltr ? '╗' : '╔';
    $d_lb = $language_is_ltr ? '╚' : '╝';
    $d_rb = $language_is_ltr ? '╝' : '╚';

    $ds_l = $language_is_ltr ? '╟' : '╢';
    $ds_r = $language_is_ltr ? '╢' : '╟';

    $s_lt = $language_is_ltr ? '┌' : '┐';
    $s_rt = $language_is_ltr ? '┐' : '┌';
    $s_lb = $language_is_ltr ? '└' : '┘';
    $s_rb = $language_is_ltr ? '┘' : '└';

    $ss_l = $language_is_ltr ? '├' : '┤';
    $ss_r = $language_is_ltr ? '┤' : '├';

    return "{$d_lt}═════════════════════════════════════{$d_rt}
║ + Parent row 2                      ║
{$ds_l}─────────────────────────────────────{$ds_r}
║ {$s_lb} + Parent row 1                    ║
║   {$s_lt}───────────────────────────────{$s_rt} ║
║   │ + Child 2 (of parent row 1)   │ ║
║   {$ss_l}───────────────────────────────{$ss_r} ║
║   │ {$s_lb} + Child 1 (of parent row 1) │ ║
║   {$ss_l}───────────────────────────────{$ss_r} ║
║   │ + Child 3 (of parent row 1)   │ ║
║   {$s_lb}───────────────────────────────{$s_rb} ║
{$ds_l}─────────────────────────────────────{$ds_r}
║   {$s_lb} + Parent row 3                  ║
{$ds_l}─────────────────────────────────────{$ds_r}
║     {$s_lb} + Parent row 4                ║
{$ds_l}─────────────────────────────────────{$ds_r}
║ + Parent row 5                      ║
{$d_lb}═════════════════════════════════════{$d_rb}";
  }

}
