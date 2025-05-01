<?php

namespace Drupal\par_data\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;

/**
 * Plugin implementation of the 'par_member_formatter' formatter.
 *
 * Used for converting the member_numbers field into a true value
 * of members using the member display option to determine how
 * the value is calculated.
 *
 * @FieldFormatter(
 *   id = "par_member_formatter",
 *   label = @Translation("PAR Member Formatter"),
 *   field_types = {
 *     "integer",
 *   }
 * )
 */
class ParMemberFormatter extends FormatterBase {

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_field.manager')
    );
  }


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
   * @param EntityFieldManagerInterface $entityFieldManager
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $member_number = $items->getEntity() instanceof ParDataPartnership
      && $items->getEntity()->isCoordinated() ?
        $items->getEntity()->numberOfMembers() : NULL;

    if ($member_number) {
      $element[] = [
        '#type' => 'markup',
        '#markup' => $member_number,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function defaultSettings() {
    return [
      'display_field' => NULL,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $entity_type_id = $this->fieldDefinition->getTargetEntityTypeId();
    $bundle = $this->fieldDefinition->getTargetBundle();

    $fields = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle);
    array_walk($fields, function (&$value) {
      $value = $value->getLabel();
    });

    $element['display_field'] = [
      '#title' => t('Choose the field that contains the member display method'),
      '#type' => 'select',
      '#options' => $fields,
      '#default_value' => $this->getSetting('display_field'),
    ];

    return $element;
  }

}
