<?php

namespace Drupal\par_data\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'par_list_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "par_registered_organisations_formatter",
 *   label = @Translation("PAR Registered Organisations Formatter"),
 *   field_types = {
 *     "string",
 *     "text",
 *   }
 * )
 */
class ParRegisteredOrganisationsFormatter extends FormatterBase {

  /**
   * The PAR registered organisations manager for retrieving organisation data.
   */
  protected OrganisationManagerInterface $organisationManager;

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
   * @param \Drupal\registered_organisations\OrganisationManagerInterface $registered_organisations_manager
   *   The registered organisation manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, OrganisationManagerInterface $registered_organisations_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->organisationManager = $registered_organisations_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('registered_organisations.organisation_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\par_data\Entity\ParDataLegalEntity $entity */
    $entity = $items->getEntity();
    $bundle_entity = $entity?->type?->entity;
    $element = [];

    // This plugin is only allowed for legal entity types.
    if ($entity->getEntityTypeId() !== 'par_data_legal_entity') {
      return $element;
    }

    // Get the organisation profile.
    $organisation_profile = $entity->getOrganisationProfile() ?? NULL;

    foreach ($items as $delta => $item) {
      $field_type_mapping = [
        'registered_number' => 'id',
        'registered_name' => 'name',
        'legal_entity_type' => 'type',
      ];
      $field_name = $item->getFieldDefinition()->getName();
      $property_name = $field_type_mapping[$field_name] ?? NULL;

      // If this is a registered organisation, render the value
      // processed with the organisation register plugin.
      if ($entity->isRegisteredOrganisation()) {
        $value = $organisation_profile && $property_name ?
          $organisation_profile->process($property_name, $item->value) :
          NULL;
      }
      // If the field supports allowed values, render the field label,
      // otherwise render the plain field value.
      else {
        $value = $bundle_entity?->getAllowedValues($field_name) ?
          $bundle_entity->getAllowedFieldlabel($field_name, $item->value) :
          $item->value;
      }

      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $value ?? '',
      ];
    }

    return $element;
  }

}
