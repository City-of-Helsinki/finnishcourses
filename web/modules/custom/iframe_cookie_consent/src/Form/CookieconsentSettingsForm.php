<?php

/**
* @file
* Contains Drupal\iframe_cookie_consent\Form\CookieconsentSettingsForm.
*/

namespace Drupal\iframe_cookie_consent\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class CookieconsentSettingsForm extends ConfigFormBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'iframe_cookie_consent.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'cookieconsent_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('iframe_cookie_consent.settings');

    $form['cookieconsent_category'] = [
      '#title' => $this->t('Cookieconsent category'),
      '#type' => 'select',
      '#description' => $this->t(
        'To be able to enable the iframe when consent has been given,
        the data-cookieconsent attribute must be added with one of these values.'
      ),
      '#options' => [
        'preferences' => 'Preferences',
        'statistics' => 'Statistics',
        'marketing' => 'Marketing'
      ],
      '#default_value' => $config->get('cookieconsent_category')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('iframe_cookie_consent.settings');
    $config->set('cookieconsent_category', $form_state->getValue('cookieconsent_category'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
