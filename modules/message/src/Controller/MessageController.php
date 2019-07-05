<?php

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Contrains code for Message test route.
 */
class MessageController extends ControllerBase {

  /**
   * Displays page with test messages.
   *
   * @param string $length
   *   Length of the messages: short or long.
   *
   * @return array
   *   A render array for the test page with messages.
   */
  public function messagePage($length) {
    $message_types = [
       // This is a custom type.
      'custom' => $this->t('Custom type message'),
      'status' => $this->t('Status message'),
       // This is a custom type that's defined by Claro design.
      'info' => $this->t('Info message'),
      'warning' => $this->t('Warning message'),
      'error' => $this->t('Error message'),
    ];

    $recommendations = [
      $this->t('Make it at least 12 characters'),
      $this->t('Add lowercase letters'),
      $this->t('Add uppercase letters'),
      $this->t('Add numbers'),
      $this->t('Add punctuation'),
    ];

    foreach ($message_types as $message_type => $message) {
      $this->messenger()->addMessage($message, $message_type);

      if ($length === 'long') {
        $this->messenger()->addMessage($this->t('Recommendations to make your password stronger:'), $message_type);
        foreach ($recommendations as $recommendation) {
          $this->messenger()->addMessage($recommendation, $message_type);
        }
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Messages'),
    ];
  }

}
