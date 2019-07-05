<?php

namespace Drupal\dialog\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Component\Render\MarkupInterface;

/**
 * Contrains code for Dialog example route and components.
 */
class DialogController extends ControllerBase {

  /**
   * Displays page with dialog buttons.
   *
   * @return array
   *   A render array for the test page.
   */
  public function dialogPage() {
    return [
      'auto_modals' => [
        '#type' => 'container',
        'prefix' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Auto sized dialogs'),
        ],
        'dialog' => $this->buildDialogLinkButton(
          $this->t('Dialog'),
          'dialog',
          ['js-dialog-button']
        ),
        'modal_dialog' => $this->buildDialogLinkButton(
          $this->t('Modal dialog'),
          'modal',
          ['js-modal-dialog-button']
        ),
        'offcanvas_dialog' => $this->buildDialogLinkButton(
          $this->t('Off-canvas dialog'),
          'off_canvas',
          ['js-offcanvas-dialog-button']
        ),
        'offcanvas_top_dialog' => $this->buildDialogLinkButton(
          $this->t('Off-canvas top dialog'),
          'off_canvas_top',
          ['js-offcanvas-top-dialog-button']
        ),
      ],
      'sized_modals' => [
        '#type' => 'container',
        'prefix' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Sized dialogs'),
        ],
        'dialog' => $this->buildDialogLinkButton(
          $this->t('Dialog 700'),
          'dialog',
          ['js-dialog-sized-button'],
          700
        ),
        'modal_dialog' => $this->buildDialogLinkButton(
          $this->t('Modal dialog 700'),
          'modal',
          ['js-modal-dialog-sized-button'],
          700
        ),
        'offcanvas_dialog' => $this->buildDialogLinkButton(
          $this->t('Off-canvas dialog 500'),
          'off_canvas',
          ['js-offcanvas-dialog-sized-button'],
          500
        ),
        'offcanvas_top_dialog' => $this->buildDialogLinkButton(
          $this->t('Off-canvas top dialog 250'),
          'off_canvas_top',
          ['js-offcanvas-top-dialog-sized-button'],
          250
        ),
      ],
      'filter_tips' => [
        '#theme' => 'filter_tips',
        '#long' => TRUE,
        '#tips' => _filter_tips(-1, TRUE),
      ],
    ];
  }

  /**
   * Builds dialog button.
   *
   * @param null|\Drupal\Component\Render\MarkupInterface $text
   *   (optional) The text of the button. 'Dialog' will be used if omitted.
   * @param null|string $type
   *   (optional) The dialog type, that could be:
   *   - 'dialog' for a regular dialog (no overlay)
   *   - 'modal' for a modal dialog
   *   - 'off_canvas' for an off-canvas dialog (opened in sidebar)
   *   - 'off_canvas_top' for an off-canvas dialog that is placed on the top of
   *     the page.
   *   If omitted, then 'dialog' will be used.
   * @param string[] $button_classes
   *   (optional) Additional classes that should be added to the button.
   * @param null|int $size
   *   (optional) The main size of the dialog. This is the height if the $type
   *   parameter is 'off_canvas_top', or the width in every other cases.
   *   If it is null or 0, the dialog size will be auto-calculated.
   * @param null|\Drupal\Core\Url $url
   *   (optional) The internal url to open in the dialog. If omitted, then the
   *   url of the password reminder form will be used.
   *
   * @return array
   *   A renderable array with the link markup.
   */
  protected function buildDialogLinkButton($text = NULL, $type = NULL, array $button_classes = [], $size = NULL, $url = NULL) {
    $dialog_types = [
      'dialog' => [
        'type' => 'dialog',
        'renderer' => NULL,
      ],
      'modal' => [
        'type' => 'modal',
        'renderer' => NULL,
      ],
      'off_canvas' => [
        'type' => 'dialog',
        'renderer' => 'off_canvas',
      ],
      'off_canvas_top' => [
        'type' => 'dialog',
        'renderer' => 'off_canvas_top',
      ],
    ];
    $text = $text instanceof MarkupInterface ? $text : $this->t('Dialog');
    $type = !empty($type) && in_array($type, array_keys($dialog_types)) ?
      $type : 'dialog';
    $button_classes = array_unique(array_merge(['button', 'use-ajax'], $button_classes));
    $dialog_options = [];
    if (!empty($size)) {
      $dialog_options = $type === 'off_canvas_top' ?
        ['height' => $size] :
        ['width' => $size];
    }
    $url = $url instanceof Url ? $url : Url::fromRoute('user.pass');

    $link = [
      '#type' => 'link',
      '#title' => $text,
      '#url' => $url,
      '#options' => [
        'attributes' => [
          'class' => $button_classes,
          'data-dialog-type' => $dialog_types[$type]['type'],
          'data-dialog-renderer' => $dialog_types[$type]['renderer'],
          'rel' => 'nofollow',
        ],
      ],
      '#attached' => ['library' => ['core/drupal.dialog.ajax']],
    ];

    if (!empty($dialog_options)) {
      $link['#options']['attributes']['data-dialog-options'] = Json::encode($dialog_options);
    }

    return $link;
  }

}
