<?php

namespace Drupal\par_subscriptions\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'par_email_obfuscation_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "par_email_obfuscation_formatter",
 *   label = @Translation("PAR Email Obfuscation Formatter"),
 *   field_types = {
 *     "string",
 *     "text",
 *   }
 * )
 */
class ParEmailObfuscationFormatter extends FormatterBase {

  /**
   * Constructs a PAR List Formatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  public function getEmailValidator() {
    return \Drupal::service('email.validator');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      if ($item->value && $this->getEmailValidator()->isValid($item->value)) {
        $replaceable = substr($item->value, 1, strpos($item->value, '@')-2);

        $regex = '/'.preg_quote($replaceable, '/').'/';

        $value = preg_replace($regex, 'xxxxxx', $item->value, 1);
      }

      // Only display the original value if this is supported.
      if ($this->getSetting('display_original_value') && !isset($value)) {
        $value = $item->value;
      }
      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $value,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'display_original_value' => FALSE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['display_original_value'] = [
      '#title' => t('Display original value if there is no match'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('display_original_value'),
    ];

    return $element;
  }

}
