<?php

namespace Drupal\par_data\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'par_legal_entity_formatter' formatter.
 *
 * Formats the PAR Legal Entity fields correctly according to the register
 * from which the information comes from.
 *
 * @FieldFormatter(
 *   id = "par_legal_entity_formatter",
 *   label = @Translation("PAR Legal Entity Formatter"),
 *   field_types = {
 *     "string",
 *     "text",
 *   }
 * )
 */
class ParLegalEntityFormatter extends FormatterBase {

  /**
   * The registered organisation manager service.
   */
  protected $registereOrganisationManager;

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
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    // Cannot be injected as a dependency.
    // @see https://drupal.stackexchange.com/questions/224247/how-do-i-inject-a-dependency-into-a-fieldtype-plugin#comment273484_224248
    $this->registereOrganisationManager = \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $field_name = $items->getFieldDefinition()->getName();
    $bundle_entity = $this->parDataManager->getParBundleEntity($items->getEntity()->getEntityTypeId());

    foreach ($items as $delta => $item) {
      $value = $bundle_entity->getAllowedFieldlabel($field_name, $item->value);
      // Render each element as markup.
      if (!$value && $this->getSetting('display_original_value')) {
        $value = $item->value;
      }

      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $value ? $value : 'Unknown value',
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
