<?php

namespace Drupal\password\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides test form for password widget.
 */
class PasswordForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'password_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['password_confirm'] = [
      '#type' => 'password_confirm',
      '#title' => $this->t('@widget-name widget', [
        '@widget-name' => $this->t('Confirm password'),
      ]),
      '#description' => $this->t('This widget contains two <code>password</code> input and some help for a better UX'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (!empty($pw = $form_state->getValue('password_confirm'))) {
      $this->messenger()->addStatus($this->t('Password submitted! It was @password, right? :)', [
        '@password' => str_repeat('•', mb_strlen($pw)),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('No password submitted'));
    }
  }

}
