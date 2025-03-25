<?php

namespace Drupal\par_data\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'par_boolean_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "par_boolean_formatter",
 *   label = @Translation("PAR Boolean Formatter"),
 *   field_types = {
 *     "boolean",
 *   }
 * )
 */
class ParBooleanFormatter extends FormatterBase {

  /**
   * The PAR Data Manager for accessing data configuration.
   */
  protected $parDataManager;

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

    // Cannot be injected as a dependency.
    // @see https://drupal.stackexchange.com/questions/224247/how-do-i-inject-a-dependency-into-a-fieldtype-plugin#comment273484_224248
    $this->parDataManager = \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $field_name = $items->getFieldDefinition()->getName();
    $bundle_entity = $this->parDataManager->getParBundleEntity($items->getEntity()->getEntityTypeId());

    // Handle booleans with empty data.
    if (count($items) == 0) {

      // Force boolean with no data to show "off" text.
      $value = $bundle_entity->getBooleanFieldLabel($field_name, FALSE);

      $element[0] = [
        '#type' => 'markup',
        '#markup' => $value ?: 'Unknown value',
      ];

      return $element;
    }

    foreach ($items as $delta => $item) {

      $value = $bundle_entity->getBooleanFieldLabel($field_name, !empty($item->value));

      // Render each element as markup.
      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $value ?: 'Unknown value',
      ];

    }

    return $element;
  }

}
