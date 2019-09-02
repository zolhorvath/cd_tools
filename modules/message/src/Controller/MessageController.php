<?php

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Contrains code for Message test route.
 */
class MessageController extends ControllerBase {

  /**
   * Displays page with test messages.
   *
   * @param string $type
   *   Length of the messages: short or long.
   *
   * @return array
   *   A render array for the test page with messages.
   */
  public function messagePage($type) {
    $build = [
      '#type' => 'markup',
      '#markup' => $this->t('Messages'),
    ];
    $message_types = [
      'status' => $this->t('A status message'),
       // This is a custom type that's defined by Claro design.
      'info' => $this->t('An info message'),
      'warning' => $this->t('A warning message'),
      'error' => $this->t('An error message'),
       // This is a custom type.
      'custom' => $this->t('A custom type message'),
    ];

    $recommendations_items = [
      $this->t('Make it at least 12 characters'),
      $this->t('Add lowercase letters'),
      $this->t('Add uppercase letters'),
      $this->t('Add numbers'),
      $this->t('Add punctuation'),
    ];
    $recommendations = [
      '#prefix' => $this->t('Recommendations to make your <a href=":wiki_url_password_strength">password</a> stronger:', [
        ':wiki_url_password_strength' => 'https://en.wikipedia.org/wiki/Password_strength#Usability_and_implementation_considerations',
      ]),
      '#theme' => 'item_list',
      '#list_type' => 'ul',
    ];
    foreach ($recommendations_items as $recommendations_item) {
      $recommendations['#items'][] = ['#markup' => $recommendations_item];
    }

    switch ($type) {
      case 'js':
        $build['#attached']['library'][] = 'message/message-test';
        foreach (array_keys($message_types) as $message_type) {
          $build['#attached']['drupalSettings']['message'][$message_type] = $this->t('Some background about <a href=":wiki_url_password">passwords</a>.', [
            ':wiki_url_password' => 'https://en.wikipedia.org/wiki/Password',
          ]);
        }
        break;

      default:
        foreach ($message_types as $message_type => $message) {
          $this->messenger()->addMessage($message, $message_type);

          if ($type === 'long') {
            $this->messenger()->addMessage($this->t('An another message with type %message-type. Check the <a href=":status_report_url">status report</a> page.', [
              '%message-type' => $message_type,
              ':status_report_url' => Url::fromRoute('system.status')->toString(),
            ]), $message_type);
            $this->messenger()->addMessage($recommendations, $message_type);
          }
        }
        break;
    }

    return $build;
  }

}
