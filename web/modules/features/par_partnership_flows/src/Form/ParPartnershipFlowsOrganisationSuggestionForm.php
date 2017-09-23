<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsOrganisationSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_organisation_suggestion';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues() {
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $searchQuery = $this->getDefaultValues('organisation_name', '', 'par_partnership_application_organisation_search');

    // Go to previous step if search query is not specified.
    if (!$searchQuery) {
      return $this->redirect($this->getFlow()->getPrevStep(), $this->getRouteParams());
    }

    $conditions = [
      'name' => [
        'or' => [
          ['organisation_name', $searchQuery, 'CONTAINS'],
          ['trading_name', $searchQuery, 'CONTAINS'],
        ]
      ],
    ];

    $organisationViewBuilder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    $options = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_organisation', $conditions);

    $radio_options = [];

    foreach($options as $option) {
      $option_view = $organisationViewBuilder->view($option, 'summary');

      $radio_options[$option->id()] = $this->renderMarkupField($option_view)['#markup'];
    }

    // If no suggestions were found we want to automatically submit the form.
    if (count($radio_options) <= 0) {
      $this->setTempDataValue('par_data_organisation_id', 'new');
      $this->submitForm($form, $form_state);
      return $this->redirect($this->getFlow()->getNextRoute('save'), $this->getRouteParams());
    }

    $form['par_data_organisation_id'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these organisations?'),
      '#options' => $radio_options + ['new' => "No, it's a new partnership."],
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($organisationViewBuilder);
    $this->addCacheableDependency($options);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    // New Organisation requires both an address and contact.
    if ($this->getDefaultValues('par_data_organisation_id', '', 'par_partnership_organisation_suggestion') === 'new') {
      $form_state->setRedirect($this->getFlow()->getNextRoute('add_address'), $this->getRouteParams());
    }

    // If Organisation exists already (e.g. selected existing organisation)
    // Check if contact details are entered, if not, prompt for main contact.
    if ($par_data_organisation = ParDataOrganisation::load($this->getDefaultValues('par_data_organisation_id', '', 'par_partnership_organisation_suggestion'))) {
      if ($par_data_organisation->id() &&
        empty($par_data_organisation->retrieveEntityIds('field_person'))) {
        $form_state->setRedirect($this->getFlow()
          ->getNextRoute('add_contact'), $this->getRouteParams());
      }
    }

  }

}
