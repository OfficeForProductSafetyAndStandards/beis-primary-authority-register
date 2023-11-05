<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "partnership_regulatory_functions",
 *   title = @Translation("Partnership Regulatory Functions form.")
 * )
 */
class ParPartnershipRegulatoryFunctionsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [];

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    // Decide which entity to use.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $existing_selection = $par_data_partnership->getRegulatoryFunction();
      $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", array_keys($this->getParDataManager()->getEntitiesAsOptions($existing_selection)));

      // Get the available options from the authority.
      if ($authority = $par_data_partnership->getAuthority(TRUE)) {
        $available_regulatory_functions = $authority->getRegulatoryFunction();
        $this->getFlowDataHandler()->setFormPermValue("regulatory_function_options", $this->getParDataManager()->getEntitiesAsOptions($available_regulatory_functions));

        // If there are no regulatory functions then we need to get the authority
        // id required for updating the authority information.
        if (empty($available_regulatory_functions)) {
          $this->getFlowDataHandler()->setFormPermValue("partnership_authority_id", $authority->id());
        }

        // Determine whether this is a default or bespoke partnership.
        $default = empty($existing_selection) || empty(array_diff(array_keys($available_regulatory_functions), array_keys($existing_selection)));
        $this->getFlowDataHandler()->setFormPermValue("default", $default);
      }

      // Identify what other partnerships cover this organisation.
      if ($organisation = $par_data_partnership->getOrganisation(TRUE)) {
        $relationships = $organisation->getRelationships('par_data_partnership');

        // Ignore the current organisation.
        $relationships = array_filter($relationships, function ($relationship) use ($par_data_partnership) {
          return $relationship->getEntity()->id() !== $par_data_partnership->id();
        });

        // Discover the regulatory functions for these partnerships.
        $covered_by_other_partnerships = [];
        foreach ($relationships as $relationship) {
          $alternative_partnership = $relationship->getEntity();
          $already_covered_regulatory_functions = $alternative_partnership->getRegulatoryFunction();
          $covered_by_other_partnerships += $this->getParDataManager()->getEntitiesAsOptions($already_covered_regulatory_functions);
        }
        $this->getFlowDataHandler()->setFormPermValue("covered_by_other_partnerships", implode(', ', $covered_by_other_partnerships));
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $renderer = \Drupal::service('renderer');

    // If there are no available regulatory functions then this form should not be displayed.
    $regulatory_function_options = $this->getFlowDataHandler()->getFormPermValue('regulatory_function_options');
    if (empty($regulatory_function_options)) {
      try {
        $params = ['par_data_authority' => $this->getDefaultValuesByKey('partnership_authority_id', $index, NULL)];
        $link_options = ['attributes' => ['class' => ['flow-link', 'govuk-link'], 'target' => '_blank']];
        $authority_update_link = $this->getLinkByRoute('par_authority_update_flows.authority_update_review', $params, $link_options)
            ->setText('Update the authority\'s regulatory functions')
            ->toString();
      } catch (ParFlowException $e) {

      }
      $form['no_regulatory_functions'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group', 'notice']],
        'warning' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('This authority does not provide any regulatory functions, please update the authority before continuing: %link', ['%link' => $authority_update_link]),
          '#attributes' => ['class' => 'bold-small'],
          '#prefix' => '<i class="icon icon-important"><span class="visually-hidden">Warning</span></i>'
        ],
      ];

      return $form;
    }

    // Identify if the organisation is covered by any regulatory
    // functions through other partnerships.
    $covered_by_other_partnerships = $this->getFlowDataHandler()->getDefaultValues('covered_by_other_partnerships', TRUE);
    if ($covered_by_other_partnerships) {
      $form['covered_by_other_patnerships'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group', 'notice']],
        'warning' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('This organisation is already covered for %regulatory_functions by other partnerships.', ['%regulatory_functions' => $covered_by_other_partnerships]),
          '#attributes' => ['class' => 'bold-small'],
          '#prefix' => '<i class="icon icon-important"><span class="visually-hidden">Warning</span></i>'
        ],
      ];
    }

    $default_label = [
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Normal or Sequenced'),
      ],
      [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#list_header_tag' => 'h2',
        '#title' => 'The following regulatory functions will be added',
        '#items' => $regulatory_function_options,
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
        '#wrapper_attributes' => ['class' => 'govuk-form-group'],
      ]
    ];
    $bespoke_label = [
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Bespoke'),
      ],
    ];

    // Default partnerships are those that cover all the regulatory functions
    // that the authority can offer.
    $default = $this->getFlowDataHandler()->getDefaultValues('default', TRUE);
    $form['partnership_cover'] = [
      '#type' => 'radios',
      '#title' => 'Is this a sequenced or bespoke partnership?',
      '#title_tag' => 'h2',
      '#options' => [
        'default' => $this->t('Normal or Sequenced'),
        'bespoke' => $this->t('Bespoke'),
      ],
      '#options_descriptions' => array(
        'default' => 'Either for normal partnerships, where the organisation only has one partnership, or for sequenced partnerships, where a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities do not overlap.',
        'bespoke' => 'Bespoke partnerships should only be selected when a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities overlap.',
      ),
      '#after_build' => [
        [get_class($this), 'optionsDescriptions'],
      ],
      '#default_value' => $default ? 'default' : 'bespoke',
    ];

    // Set the default regulatory functions to be applied to all default partnerships.
    $values = [];
    foreach ($regulatory_function_options as $key => $option) {
      $values[$key] = (string) $key;
    }
    $form['default_regulatory_functions'] = [
      '#type' => 'value',
      '#value' => $values,
    ];

    $form['sequenced_regulatory_functions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-form-group']],
      '#states' => [
        'visible' => [
          'input[name="partnership_cover"]' => ['value' => 'default'],
        ],
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('The following regulatory functions will be added'),
        '#attributes' => ['class' => 'govuk-heading-m'],
      ],
      [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#items' => $regulatory_function_options,
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
      ],
    ];

    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => 'Regulatory Functions',
      '#title_tag' => 'h2',
      '#options' => $regulatory_function_options,
      '#default_value' => $this->getDefaultValuesByKey('regulatory_functions', $index, []),
      '#attributes' => ['class' => ['govuk-form-group']],
      '#states' => [
        'visible' => [
          'input[name="partnership_cover"]' => ['value' => 'bespoke'],
        ],
        'disabled' => [
          'input[name="partnership_cover"]' => ['value' => 'default'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $partnership_cover_key = $this->getElementKey('partnership_cover');
    $regulatory_functions_key = $this->getElementKey('regulatory_functions');
    $default_regulatory_functions_key = $this->getElementKey('default_regulatory_functions');

    // For default partnerships the regulatory functions need to be set as the default options.
    if ($form_state->getValue($partnership_cover_key) === 'default') {
      $form_state->setValue($regulatory_functions_key, $form_state->getValue($default_regulatory_functions_key));
    }
    elseif ($form_state->getValue($partnership_cover_key) === 'bespoke') {
      $regulatory_functions = array_filter($form_state->getValue($regulatory_functions_key));
      if (empty($regulatory_functions)) {
        $id_key = $this->getElementKey('regulatory_functions', $index, TRUE);
        $message = $this->wrapErrorMessage('You must choose at least one regulatory function.', $this->getElementId($id_key, $form));
        $form_state->setErrorByName($this->getElementName($regulatory_functions_key), $message);
      }
    }
    else {
      // In case no partnership type was selected.
      $id_key = $this->getElementKey('partnership_cover', $index, TRUE);
      $message = $this->wrapErrorMessage('Please choose whether this is a normal, sequenced or bespoke partnership.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($partnership_cover_key), $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
