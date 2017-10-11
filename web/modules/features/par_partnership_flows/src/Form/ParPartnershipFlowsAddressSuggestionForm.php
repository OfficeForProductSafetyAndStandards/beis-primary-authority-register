<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the premises details.
 */
class ParPartnershipFlowsAddressSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_address_suggestion';
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // If I remove this then this will come back with NULL:
    // $this->getDefaultValues('address_line1', '', 'par_partnership_address').
    $this->retrieveEditableValues($par_data_partnership);

    $conditions = [
      'address' => [
        'OR' => [
          ['address__address_line1', $this->getDefaultValues('address_line1', '', 'par_partnership_address'), '='],
          ['address__postal_code', strtoupper($this->getDefaultValues('postcode', '', 'par_partnership_address')), '='],
        ],
      ],
    ];

    $premises_result = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_premises', $conditions, 10);

    $premises_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');

    $premises_options = [];
    foreach($premises_result as $premises) {
      $premises_view = $premises_view_builder->view($premises, 'detailed');

      $premises_options[$premises->id()] = $this->renderMarkupField($premises_view)['#markup'];
    }

    $form['par_data_premises_id'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these premises?'),
      '#options' => $premises_options + ['new' => "No, it's a new address."],
      '#default_value' => $this->getDefaultValues('par_data_premises_id', 'new'),
    ];

    // If no suggestions were found we want to automatically submit the form.
    if (count($premises_options) <= 0) {
      $this->setTempDataValue('par_data_premises_id', 'new');
      $this->submitForm($form, $form_state);
      return $this->redirect($this->getFlow()->getNextRoute('save'), $this->getRouteParams());
    }

    // Make sure to add the premises cacheability data to this form.
    $this->addCacheableDependency($premises_view_builder);
    $this->addCacheableDependency($premises);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get partnership entity from URL.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    if ($this->getDefaultValues('par_data_premises_id', '', 'par_partnership_address_suggestion') === 'new') {
      // Create new premises entity.
      $par_data_premises = ParDataPremises::create([
        'type' => 'premises',
        'uid' => $this->getCurrentUser()->id(),
        'address' => [
          'country_code' => $this->getDefaultValues('country_code', '', 'par_partnership_address'),
          'address_line1' => $this->getDefaultValues('address_line1', '', 'par_partnership_address'),
          'address_line2' => $this->getDefaultValues('address_line2', '', 'par_partnership_address'),
          'locality' => $this->getDefaultValues('town_city', '', 'par_partnership_address'),
          'administrative_area' => $this->getDefaultValues('county', '', 'par_partnership_address'),
          'postal_code' => $this->getDefaultValues('postcode', '', 'par_partnership_address'),
        ],
        'nation' => $this->getDefaultValues('country', '', 'par_partnership_address'),
      ]);

      $par_data_premises->save();
    }
    else {

      $premises_id = $this->getDefaultValues('par_data_premises_id', '', 'par_partnership_address_suggestion');
      $par_data_premises = ParDataPremises::load($premises_id);

    }

    if ($par_data_premises->id()) {
      // Based on the flow we're in we also need to.
      // Update field_premises on authority or organisation.
      if ($this->getFlowName() === 'partnership_direct' || $this->getFlowName() === 'partnership_coordinated') {
        // Add to field_premises.
        $par_data_organisation->get('field_premises')
          ->appendItem($par_data_premises->id());
        if ($par_data_organisation->save()) {
          $this->deleteStore();
        }
        else {
          $message = $this->t('This %premises could not be saved for %form_id');
          $replacements = [
            '%premises' => $this->getTempDataValue('address_line1'),
            '%form_id' => $this->getFormId(),
          ];
          $this->getLogger($this->getLoggerChannel())
            ->error($message, $replacements);
        }
      }
      // Possibly need to add address for authority journey?
    }
  }

}
