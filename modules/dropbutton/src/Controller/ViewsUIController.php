<?php

namespace Drupal\dropbutton\Controller;

use Drupal\views_ui\Controller\ViewsUIController as DefaultViewsUIController;
use Drupal\views\Entity\View;
use Drupal\views_ui\ViewUI;
use Drupal\views\ViewsData;

/**
 * Returns responses for Views UI routes.
 */
class ViewsUIController extends DefaultViewsUIController {

  /**
   * Returns the form to edit the content view.
   *
   * @return array
   *   An array containing the Views edit form.
   */
  public function contentViewEdit() {
    $build = [
      'edit' => [
        '#type' => 'item',
        '#markup' => 'Content view entity cannot be found.',
      ],
    ];

    if ($view = View::load('content')) {
      $view_ui = new ViewUI($view);

      $name = $view_ui->label();
      $data = $this->viewsData->get($view_ui->get('base_table'));

      if (isset($data['table']['base']['title'])) {
        $name .= ' (' . $data['table']['base']['title'] . ')';
      }

      $build['#title'] = $this->t('Dropbutton on the @view-name view edit form', [
        '@view-name' => $name,
      ]);

      $build['edit'] = $this->entityFormBuilder()->getForm($view_ui, 'edit', ['display_id' => NULL]);
    }

    return $build;
  }

}
